<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role_filter = $request->input('role');
        $department_filter = $request->input('department');
        $date_filter = $request->input('date_added');
        
        $query = User::query();
        
        // Apply search filter if search term is provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('user_role', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply role filter
        if ($role_filter) {
            $query->where('user_role', $role_filter);
        }
        
        // Apply department filter
        if ($department_filter) {
            $query->where('department_id', $department_filter);
        }
        
        // Apply date filter
        if ($date_filter) {
            if ($date_filter === 'today') {
                $query->whereDate('created_at', today());
            } elseif ($date_filter === 'this_week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($date_filter === 'this_month') {
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            } elseif ($date_filter === 'this_year') {
                $query->whereYear('created_at', now()->year);
            }
        }
        
        $users = $query->with('department')->paginate(10);
        // Maintain filter parameters in pagination links
        $users->appends([
            'search' => $search,
            'role' => $role_filter,
            'department' => $department_filter,
            'date_added' => $date_filter
        ]);
        
        $departments = Department::all();
        
        // Get unique user roles for the filter dropdown
        $roles = User::select('user_role')->distinct()->pluck('user_role');

        return view('users.index', compact('users', 'departments', 'roles', 'search', 'role_filter', 'department_filter', 'date_filter'));
    }
    
    public function getUsersData(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $department = $request->input('department');
        $date_added = $request->input('date_added');
        
        $query = User::with('department');
        
        // Apply search filter if search term is provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('user_role', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply role filter
        if ($role) {
            $query->where('user_role', $role);
        }
        
        // Apply department filter
        if ($department) {
            $query->where('department_id', $department);
        }
        
        // Apply date filter
        if ($date_added) {
            if ($date_added === 'today') {
                $query->whereDate('created_at', today());
            } elseif ($date_added === 'this_week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($date_added === 'this_month') {
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            } elseif ($date_added === 'this_year') {
                $query->whereYear('created_at', now()->year);
            }
        }
        
        // Get the page from the request, but reset to page 1 when filters change
        $page = $request->input('reset_pagination') ? 1 : $request->input('page', 1);
        
        $users = $query->paginate(10, ['*'], 'page', $page);
        
        return response()->json([
            'users' => $users,
            'links' => $users->links()->toHtml(),
            'current_page' => $users->currentPage(),
        ]);
    }

    public function view($id)
    {
        $user = User::with(['assets', 'department'])->findOrFail($id);
        return view('users.view', ['user' => $user, 'key' => $id]);
    }

    public function create()
    { 
        $departments = Department::all();
        return view('users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_role' => 'required',
            'first_name' => 'required|regex:/^[a-zA-Z\s\-]+$/',
            'last_name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'min:8',
                /*
                (?=.*[A-Z]) para sa uppercase
                (?=.*[0-9]) para sa number
                (?=.*[!@#$%^&*]) para sa special char
                */
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
                'confirmed',
            ],
            'department_id' => 'required|exists:departments,id',
        ], [ // Custom Error Message
                'password.regex' => 'The password must contain at least one uppercase letter, one number, and one special character.',
        ]);
    
        User::create([
            'user_role' => $validatedData['user_role'],
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'department_id' => $validatedData['department_id'],
        ]);

        /* Log the activity
        Log::info('Activity Log', [
            'user' => auth()->user()->email ?? 'Guest',
            'action' => 'Added a new user: ' . $validatedData['email'] . '.'
        ]);
        */

        return redirect()->route('users.index')->with('success', 'User Added Successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();
        return view('users.edit', compact('user', 'departments'));
    }

    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'user_role' => 'required',
            'first_name' => 'required|regex:/^[a-zA-Z\s\-]+$/',
            'last_name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/|confirmed',
            'department_id' => 'required|exists:departments,id', // Ensure department is selected
        ]);

        $user->update([ 
            'user_role' => $validatedData['user_role'],
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'department_id' => $validatedData['department_id'], // Update department
            'password' => $request->filled('password') ? Hash::make($validatedData['password']) : $user->password,
        ]);

        Log::info('Activity Log', [
            'user' => Session::get('user_email'),
            'action' => 'Updated user: ' . $validatedData['email']
        ]);

        return redirect('users')->with('success', 'User Updated Successfully');
    }

    public function archive($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return back()->with('error', 'User not found');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User archived successfully.');
    }

    public function myProfile()
    {
        // Get the current authenticated user's ID
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login.form')->with('error', 'User not authenticated');
        }
        
        // Get the user with related data
        $user = User::with(['assets', 'department'])->findOrFail($userId);
        
        return view('users.my-profile', ['user' => $user]);
    }
    
    public function updateMyProfile(Request $request)
    {
        // Get the current authenticated user's ID
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login.form')->with('error', 'User not authenticated');
        }
        
        $user = User::findOrFail($userId);
        
        $validatedData = $request->validate([
            'first_name' => 'required|regex:/^[a-zA-Z\s\-]+$/',
            'last_name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|min:8|regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Update user information
        $user->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => $request->filled('password') ? Hash::make($validatedData['password']) : $user->password,
        ]);
        
        // Handle profile picture upload if provided
        if ($request->hasFile('profile_picture')) {
            $imageName = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('images/profile'), $imageName);
            $user->profile_picture = 'images/profile/' . $imageName;
            $user->save();
        }
        
        Log::info('Activity Log', [
            'user' => Auth::user()->email,
            'action' => 'Updated their profile'
        ]);
        
        return redirect()->route('users.my-profile')->with('success', 'Profile updated successfully');
    }
}
