<?php

namespace Hongdev\MasterAdmin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    /**
     * Get system performance metrics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPerformanceMetrics()
    {
        $metrics = [
            'cpu' => $this->getCpuUsage(),
            'memory' => $this->getMemoryUsage(),
            'disk' => $this->getDiskUsage(),
            'server_load' => $this->getServerLoad(),
            'uptime' => $this->getUptime(),
        ];
        
        return response()->json($metrics);
    }
    
    /**
     * Get CPU usage
     *
     * @return array
     */
    private function getCpuUsage()
    {
        $cpuUsage = 0;
        $cpuCount = 0;
        
        // Try to get CPU info
        if (function_exists('shell_exec')) {
            if (PHP_OS === 'Darwin') {
                // macOS systems
                $cores = (int) shell_exec('sysctl -n hw.ncpu');
                $loadOutput = shell_exec('sysctl -n vm.loadavg');
                
                if ($loadOutput) {
                    // Format is typically { x.xx, x.xx, x.xx }
                    $matches = [];
                    if (preg_match('/{\s*([\d.]+)/', $loadOutput, $matches)) {
                        $load = (float) $matches[1];
                        if ($cores > 0) {
                            $cpuUsage = min(100, round(($load / $cores) * 100, 2));
                            $cpuCount = $cores;
                        }
                    }
                }
                
                // Alternative method if load average doesn't work
                if ($cpuUsage === 0) {
                    $topOutput = shell_exec('top -l 1 | grep "CPU usage"');
                    if (preg_match('/(\d+\.\d+)%\s+user/', $topOutput, $matches)) {
                        $userPercentage = (float) $matches[1];
                        if (preg_match('/(\d+\.\d+)%\s+sys/', $topOutput, $matches)) {
                            $sysPercentage = (float) $matches[1];
                            $cpuUsage = round($userPercentage + $sysPercentage, 2);
                        }
                    }
                }
            } elseif (PHP_OS !== 'WINNT') {
                // Linux/Unix systems
                $load = sys_getloadavg();
                $cores = (int) shell_exec('nproc');
                
                if ($cores > 0) {
                    $cpuUsage = min(100, round(($load[0] / $cores) * 100, 2));
                    $cpuCount = $cores;
                }
            } elseif (function_exists('shell_exec')) {
                // Windows systems
                $cmd = 'wmic cpu get LoadPercentage';
                $output = shell_exec($cmd);
                if (preg_match('/(\d+)/', $output, $matches)) {
                    $cpuUsage = (float) $matches[1];
                }
                
                $cmd = 'wmic cpu get NumberOfCores';
                $output = shell_exec($cmd);
                if (preg_match('/(\d+)/', $output, $matches)) {
                    $cpuCount = (int) $matches[1];
                }
            }
        }
        
        return [
            'usage' => $cpuUsage,
            'cores' => $cpuCount,
        ];
    }
    
    /**
     * Get memory usage
     *
     * @return array
     */
    private function getMemoryUsage()
    {
        $totalMemory = 0;
        $freeMemory = 0;
        $usedMemory = 0;
        $usedPercentage = 0;
        
        if (function_exists('shell_exec')) {
            if (PHP_OS === 'Darwin') {
                // macOS systems
                $totalMemory = (int) shell_exec('sysctl -n hw.memsize');
                
                // Use vm_stat to get page info
                $vmStat = shell_exec('vm_stat');
                $pageSize = 4096; // Default page size on macOS
                
                if (preg_match('/page size of (\d+) bytes/', $vmStat, $matches)) {
                    $pageSize = (int) $matches[1];
                }
                
                // Get free pages
                $freePages = 0;
                if (preg_match('/Pages free:\s+(\d+)/', $vmStat, $matches)) {
                    $freePages += (int) $matches[1];
                }
                if (preg_match('/Pages inactive:\s+(\d+)/', $vmStat, $matches)) {
                    $freePages += (int) $matches[1];
                }
                
                $freeMemory = $freePages * $pageSize;
                $usedMemory = $totalMemory - $freeMemory;
                $usedPercentage = round(($usedMemory / $totalMemory) * 100, 2);
            } elseif (PHP_OS !== 'WINNT') {
                // Linux/Unix systems
                $memInfo = shell_exec('free -b');
                if (preg_match('/^Mem:\s+(\d+)\s+(\d+)/m', $memInfo, $matches)) {
                    $totalMemory = $matches[1];
                    $usedMemory = $matches[2];
                    $freeMemory = $totalMemory - $usedMemory;
                    $usedPercentage = round(($usedMemory / $totalMemory) * 100, 2);
                }
            } elseif (PHP_OS === 'WINNT' && function_exists('shell_exec')) {
                // Windows systems
                $cmd = 'wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value';
                $output = shell_exec($cmd);
                
                if (preg_match('/TotalVisibleMemorySize=(\d+)/i', $output, $matches)) {
                    $totalMemory = (int) $matches[1] * 1024; // Convert from KB to bytes
                }
                
                if (preg_match('/FreePhysicalMemory=(\d+)/i', $output, $matches)) {
                    $freeMemory = (int) $matches[1] * 1024; // Convert from KB to bytes
                }
                
                $usedMemory = $totalMemory - $freeMemory;
                $usedPercentage = round(($usedMemory / $totalMemory) * 100, 2);
            }
        }
        
        // Format memory values to be human-readable
        $formatMemory = function($bytes) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= (1 << (10 * $pow));
            
            return round($bytes, 2) . ' ' . $units[$pow];
        };
        
        return [
            'total' => $formatMemory($totalMemory),
            'used' => $formatMemory($usedMemory),
            'free' => $formatMemory($freeMemory),
            'percentage' => $usedPercentage,
        ];
    }
    
    /**
     * Get disk usage
     *
     * @return array
     */
    private function getDiskUsage()
    {
        $path = base_path();
        $totalSpace = disk_total_space($path);
        $freeSpace = disk_free_space($path);
        $usedSpace = $totalSpace - $freeSpace;
        $usedPercentage = round(($usedSpace / $totalSpace) * 100, 2);
        
        // Format disk values to be human-readable
        $formatDisk = function($bytes) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= (1 << (10 * $pow));
            
            return round($bytes, 2) . ' ' . $units[$pow];
        };
        
        return [
            'total' => $formatDisk($totalSpace),
            'used' => $formatDisk($usedSpace),
            'free' => $formatDisk($freeSpace),
            'percentage' => $usedPercentage,
        ];
    }
    
    /**
     * Get server load average
     *
     * @return array
     */
    private function getServerLoad()
    {
        $load = [0, 0, 0];
        
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
        }
        
        return [
            '1min' => round($load[0], 2),
            '5min' => round($load[1], 2),
            '15min' => round($load[2], 2),
        ];
    }
    
    /**
     * Get system uptime
     *
     * @return string
     */
    private function getUptime()
    {
        $uptime = 'Unknown';
        
        if (function_exists('shell_exec')) {
            if (PHP_OS === 'Darwin') {
                // macOS systems - use boot time to calculate uptime
                $bootTime = shell_exec('sysctl -n kern.boottime');
                if (preg_match('/sec = (\d+)/', $bootTime, $matches)) {
                    $bootTimestamp = (int) $matches[1];
                    $uptimeSeconds = time() - $bootTimestamp;
                    $days = floor($uptimeSeconds / 86400);
                    $hours = floor(($uptimeSeconds % 86400) / 3600);
                    $minutes = floor(($uptimeSeconds % 3600) / 60);
                    
                    $uptime = '';
                    if ($days > 0) $uptime .= $days . ' days, ';
                    $uptime .= $hours . ' hours, ' . $minutes . ' minutes';
                }
            } elseif (PHP_OS !== 'WINNT') {
                // Linux/Unix systems
                $uptimeOutput = shell_exec('uptime -p');
                if ($uptimeOutput) {
                    $uptime = trim($uptimeOutput);
                }
            } elseif (PHP_OS === 'WINNT' && function_exists('shell_exec')) {
                // Windows systems - more complex, this is a simplified version
                $cmd = 'wmic os get lastbootuptime';
                $output = shell_exec($cmd);
                if (preg_match('/(\d{14})/', $output, $matches)) {
                    $bootTime = \DateTime::createFromFormat('YmdHis', $matches[1]);
                    $now = new \DateTime();
                    $diff = $now->diff($bootTime);
                    
                    $uptime = '';
                    if ($diff->d > 0) $uptime .= $diff->d . ' days, ';
                    $uptime .= $diff->h . ' hours, ' . $diff->i . ' minutes';
                }
            }
        }
        
        return $uptime;
    }
}
