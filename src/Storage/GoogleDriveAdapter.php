<?php

namespace Hongdev\MasterAdmin\Storage;

use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathPrefixer;

class GoogleDriveAdapter implements FilesystemAdapter
{
    protected $service;
    protected $folderId;
    protected $prefixer;

    public function __construct(Drive $service, $folderId = null)
    {
        $this->service = $service;
        $this->folderId = $folderId;
        $this->prefixer = new PathPrefixer('');
    }

    public function fileExists(string $path): bool
    {
        return $this->getFileByPath($path) !== null;
    }

    public function directoryExists(string $path): bool
    {
        return $this->getFolderForPath($path) !== null;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $file = new DriveFile();
        $file->setName(basename($path));
        $file->setParents([$this->getFolderForPath(dirname($path))]);

        $this->service->files->create($file, [
            'data' => $contents,
            'mimeType' => 'application/octet-stream',
            'uploadType' => 'multipart'
        ]);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, stream_get_contents($contents), $config);
    }

    public function read(string $path): string
    {
        $file = $this->getFileByPath($path);
        if (!$file) {
            throw new \Exception("File not found: {$path}");
        }
        
        return $this->service->files->get($file->getId(), ['alt' => 'media']);
    }

    public function readStream(string $path)
    {
        $content = $this->read($path);
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);
        return $stream;
    }

    public function delete(string $path): void
    {
        $file = $this->getFileByPath($path);
        if ($file) {
            $this->service->files->delete($file->getId());
        }
    }

    public function deleteDirectory(string $path): void
    {
        $folder = $this->getFolderForPath($path);
        if ($folder) {
            $this->service->files->delete($folder);
        }
    }

    public function createDirectory(string $path, Config $config): void
    {
        $this->getFolderForPath($path, true);
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // Google Drive doesn't have visibility concept like traditional filesystems
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path, null, 'public');
    }

    public function mimeType(string $path): FileAttributes
    {
        $file = $this->getFileByPath($path);
        $mimeType = $file ? $file->getMimeType() : null;
        return new FileAttributes($path, null, null, null, $mimeType);
    }

    public function lastModified(string $path): FileAttributes
    {
        $file = $this->getFileByPath($path);
        $timestamp = $file ? strtotime($file->getModifiedTime()) : null;
        return new FileAttributes($path, null, null, $timestamp);
    }

    public function fileSize(string $path): FileAttributes
    {
        $file = $this->getFileByPath($path);
        $size = $file ? (int) $file->getSize() : null;
        return new FileAttributes($path, $size);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $folderId = $this->getFolderForPath($path);
        if (!$folderId) {
            return [];
        }

        $query = "'{$folderId}' in parents and trashed=false";
        $results = $this->service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id,name,mimeType,size,modifiedTime)'
        ]);

        foreach ($results->getFiles() as $file) {
            $filePath = $path . '/' . $file->getName();
            $isDir = $file->getMimeType() === 'application/vnd.google-apps.folder';
            
            yield new FileAttributes(
                $filePath,
                $isDir ? null : (int) $file->getSize(),
                null,
                strtotime($file->getModifiedTime()),
                $file->getMimeType()
            );
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $file = $this->getFileByPath($source);
        if ($file) {
            $file->setName(basename($destination));
            $this->service->files->update($file->getId(), $file);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $sourceFile = $this->getFileByPath($source);
        if ($sourceFile) {
            $copiedFile = new DriveFile();
            $copiedFile->setName(basename($destination));
            $copiedFile->setParents([$this->getFolderForPath(dirname($destination))]);
            
            $this->service->files->copy($sourceFile->getId(), $copiedFile);
        }
    }

    protected function getFileByPath($path)
    {
        $parentId = $this->getFolderForPath(dirname($path));
        $fileName = basename($path);
        
        $query = "'{$parentId}' in parents and name='{$fileName}' and trashed=false";
        $results = $this->service->files->listFiles(['q' => $query]);
        
        $files = $results->getFiles();
        return count($files) > 0 ? $files[0] : null;
    }

    protected function getFolderForPath($path, $create = false)
    {
        if (empty($path) || $path === '.' || $path === '/') {
            return $this->folderId;
        }

        $parts = explode('/', trim($path, '/'));
        $currentParent = $this->folderId;

        foreach ($parts as $part) {
            $query = "'{$currentParent}' in parents and name='{$part}' and mimeType='application/vnd.google-apps.folder' and trashed=false";
            $results = $this->service->files->listFiles(['q' => $query]);
            $folders = $results->getFiles();

            if (count($folders) > 0) {
                $currentParent = $folders[0]->getId();
            } elseif ($create) {
                $folder = new DriveFile();
                $folder->setName($part);
                $folder->setMimeType('application/vnd.google-apps.folder');
                $folder->setParents([$currentParent]);
                
                $createdFolder = $this->service->files->create($folder);
                $currentParent = $createdFolder->getId();
            } else {
                return null;
            }
        }

        return $currentParent;
    }
}
