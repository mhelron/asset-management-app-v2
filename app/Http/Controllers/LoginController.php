<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helpers\ActivityLogger;

class LoginController extends Controller
{

    // Redirect to previous page if available, otherwise dashboard (Pag authenticated na si user at inaccess si login page, babalik sa previous page or dashboard)
    public function showLogin(Request $request) {
        if (Auth::check()) {
            $previousUrl = url()->previous();
    
            if ($previousUrl === route('login.form')) {
                return redirect()->route('dashboard');
            }
    
            return redirect()->to($previousUrl);
        }
    
        $intendedUrl = session('url.intended');
        $previousUrl = url()->previous();
    
        $protectedRoutes = [
            url('/dashboard'),
            url('/users'),
            url('/categories'),
            url('/inventory'),
            url('/custom-fields'),
            url('/departments'),
            url('/components'),
            url('/accessories'),
        ];
    
        if ($intendedUrl && $previousUrl !== url()->current()) {
            foreach ($protectedRoutes as $prefix) {
                if (Str::startsWith($intendedUrl, $prefix)) {
                    session()->flash('error', 'You must be logged in to access this page.');
                    break;
                }
            }
        }
    
        return view('login.login');
    }
    


    public function processLogin (Request $request) {
        $email = $request->input('email');
        $password = $request->input('password');

        // Check if email exists in the database
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Email doesn't exist, clear everything
            return back()->withInput([])->with('error', 'User not found. Please try again');
        }

        // Checks if user has remember token (Para sa remember me)
        $remember = $request->has('remember');
        // Email exists, now check credentials
        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            session(['email' => Auth::user()->email]);

            // Set session variables for displaying user information
            $fullName = Auth::user()->first_name . ' ' . Auth::user()->last_name;

            // Make sure user has the correct role assigned
            $user = Auth::user();
            if (!$user->hasRole(ucfirst($user->user_role))) {
                $user->syncRoles([ucfirst($user->user_role)]);
            }

            session([
                'email' => Auth::user()->email,
                'name' => $fullName,
                'user_role' => Auth::user()->user_role,
                'user_email' => Auth::user()->email, // Added for logging
            ]);

            // Log successful login
            ActivityLogger::logLogin($email);

            // Clear the stored intended URL so it won't redirect there
            session()->forget('url.intended');

            return redirect()->intended('dashboard');
        }


         // Password incorrect: keep email only
            return back()->withInput(['email' => $email]) ->with('error', 'Incorrect password. Please try again');
    }


    public function logout(Request $request){
            // Log user logout before the user is logged out
            if (Auth::check()) {
                ActivityLogger::logLogout();
            }
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.form'); // back to login page after logout
      }
}