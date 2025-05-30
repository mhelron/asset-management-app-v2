<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogController extends Controller
{
    public function showLogs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $allLogs = [];
        $perPage = 10;
        
        // Get filter parameters
        $search = $request->input('search');
        $user_filter = $request->input('user');
        $action_filter = $request->input('action');
        $date_filter = $request->input('date');
        
        if (File::exists($logFile)) {
            $handle = fopen($logFile, 'r');
            if ($handle) {
                $currentLog = '';
                
                while (!feof($handle)) {
                    $line = fgets($handle);
                    
                    if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line)) {
                        if (!empty($currentLog)) {
                            // Only match Activity Log format
                            if (preg_match('/\[(.+?)\] local\.INFO: Activity Log ({.+})/', $currentLog, $matches)) {
                                if (isset($matches[1], $matches[2])) {
                                    $logData = json_decode($matches[2], true);
                                    if ($logData && isset($logData['user'], $logData['action'])) {
                                        $allLogs[] = [
                                            'datetime' => $matches[1],
                                            'user' => $logData['user'],
                                            'message' => $logData['action'],
                                            'action_type' => $this->extractActionType($logData['action'])
                                        ];
                                    }
                                }
                            }
                        }
                        $currentLog = $line;
                    } else {
                        $currentLog .= $line;
                    }
                }
                
                // Process last log entry
                if (!empty($currentLog)) {
                    if (preg_match('/\[(.+?)\] local\.INFO: Activity Log ({.+})/', $currentLog, $matches)) {
                        if (isset($matches[1], $matches[2])) {
                            $logData = json_decode($matches[2], true);
                            if ($logData && isset($logData['user'], $logData['action'])) {
                                $allLogs[] = [
                                    'datetime' => $matches[1],
                                    'user' => $logData['user'],
                                    'message' => $logData['action'],
                                    'action_type' => $this->extractActionType($logData['action'])
                                ];
                            }
                        }
                    }
                }
                
                fclose($handle);
                
                // Sort logs by datetime (newest first)
                usort($allLogs, function($a, $b) {
                    return strtotime($b['datetime']) - strtotime($a['datetime']);
                });
            }
        }
        
        // Apply filters
        $filteredLogs = collect($allLogs);
        
        // Apply search filter
        if ($search) {
            $filteredLogs = $filteredLogs->filter(function($log) use ($search) {
                return stripos($log['user'], $search) !== false || 
                       stripos($log['message'], $search) !== false;
            });
        }
        
        // Apply user filter
        if ($user_filter) {
            $filteredLogs = $filteredLogs->filter(function($log) use ($user_filter) {
                return $log['user'] === $user_filter;
            });
        }
        
        // Apply action type filter
        if ($action_filter) {
            $filteredLogs = $filteredLogs->filter(function($log) use ($action_filter) {
                return $log['action_type'] === $action_filter;
            });
        }
        
        // Apply date filter
        if ($date_filter) {
            $filteredLogs = $filteredLogs->filter(function($log) use ($date_filter) {
                $logDate = date('Y-m-d', strtotime($log['datetime']));
                
                if ($date_filter === 'today') {
                    return $logDate === date('Y-m-d');
                } elseif ($date_filter === 'this_week') {
                    $weekStart = date('Y-m-d', strtotime('monday this week'));
                    $weekEnd = date('Y-m-d', strtotime('sunday this week'));
                    return $logDate >= $weekStart && $logDate <= $weekEnd;
                } elseif ($date_filter === 'this_month') {
                    return date('Y-m', strtotime($log['datetime'])) === date('Y-m');
                } elseif ($date_filter === 'this_year') {
                    return date('Y', strtotime($log['datetime'])) === date('Y');
                }
                
                return true;
            });
        }
        
        // Get unique users for the filter dropdown
        $users = collect($allLogs)->pluck('user')->unique()->values()->all();
        
        // Get unique action types for the filter dropdown
        $actionTypes = collect($allLogs)->pluck('action_type')->unique()->values()->all();
        
        // Paginate the filtered logs
        $page = $request->input('page', 1);
        $total = $filteredLogs->count();
        $items = $filteredLogs->forPage($page, $perPage);
        
        $logs = new LengthAwarePaginator(
            $items, 
            $total, 
            $perPage, 
            $page, 
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('logs.activity', compact('logs', 'users', 'actionTypes', 'search', 'user_filter', 'action_filter', 'date_filter'));
    }
    
    /**
     * Extract the action type from the log message
     * 
     * @param string $message
     * @return string
     */
    private function extractActionType($message)
    {
        if (stripos($message, 'created') !== false) {
            return 'Created';
        } elseif (stripos($message, 'updated') !== false) {
            return 'Updated';
        } elseif (stripos($message, 'archived') !== false) {
            return 'Archived';
        } elseif (stripos($message, 'logged in') !== false) {
            return 'Login';
        } elseif (stripos($message, 'logged out') !== false) {
            return 'Logout';
        } else {
            return 'Other';
        }
    }
} 