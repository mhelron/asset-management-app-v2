<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class ActivityLogger
{
    /**
     * Log an activity
     *
     * @param string $action The action being performed
     * @param string|null $details Additional details about the action (optional)
     * @return void
     */
    public static function log($action, $details = null)
    {
        // Set timezone to Philippines (Asia/Manila)
        Carbon::setTestNow(Carbon::now('Asia/Manila'));
        
        // Try to get authenticated user, fallback to session, or use Guest
        $user = Auth::user() ? Auth::user()->email : (Session::get('user_email') ?? 'Guest');
        
        $logMessage = $action;
        if ($details) {
            $logMessage .= ': ' . $details;
        }
        
        Log::info('Activity Log', [
            'user' => $user,
            'action' => $logMessage,
            'timestamp' => Carbon::now('Asia/Manila')->toDateTimeString()
        ]);
        
        // Reset Carbon mock
        Carbon::setTestNow();
    }
    
    /**
     * Log a user login activity
     *
     * @param string $email The user's email
     * @return void
     */
    public static function logLogin($email)
    {
        self::log('User logged in', $email);
    }
    
    /**
     * Log a user logout activity
     *
     * @return void
     */
    public static function logLogout()
    {
        self::log('User logged out');
    }
    
    /**
     * Log a resource creation
     *
     * @param string $resourceType Type of resource (e.g., 'User', 'Category')
     * @param string $identifier Identifier of the resource (e.g., name, email)
     * @return void
     */
    public static function logCreated($resourceType, $identifier)
    {
        self::log("Created $resourceType", $identifier);
    }
    
    /**
     * Log a resource update
     *
     * @param string $resourceType Type of resource (e.g., 'User', 'Category')
     * @param string $identifier Identifier of the resource (e.g., name, email)
     * @return void
     */
    public static function logUpdated($resourceType, $identifier)
    {
        self::log("Updated $resourceType", $identifier);
    }
    
    /**
     * Log a resource deletion/archiving
     *
     * @param string $resourceType Type of resource (e.g., 'User', 'Category')
     * @param string $identifier Identifier of the resource (e.g., name, email)
     * @return void
     */
    public static function logArchived($resourceType, $identifier)
    {
        self::log("Archived $resourceType", $identifier);
    }
    
    /**
     * Log a generic activity
     *
     * @param string $message The activity message
     * @return void
     */
    public static function logGeneric($message)
    {
        self::log($message);
    }
} 