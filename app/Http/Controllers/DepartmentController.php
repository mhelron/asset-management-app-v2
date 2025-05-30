<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index()
    {
        // Force refresh the relationship with the locations table
        DB::statement('PRAGMA foreign_keys = ON;');
        
        $departments = Department::with('location')->whereNull('deleted_at')->get();
        
        // Temporary fix: Ensure all departments with location_id have the relationship
        foreach($departments as $department) {
            if($department->location_id && !$department->location) {
                // Get the location directly
                $locationInfo = DB::table('locations')->where('id', $department->location_id)->first();
                if($locationInfo) {
                    // Add debug information
                    session()->flash('info', "Found location {$locationInfo->name} for department {$department->name}");
                }
            }
        }
        
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $locations = Location::where('status', 'Active')->get();
        return view('departments.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'desc' => 'required|string',
            'location_id' => 'required|exists:locations,id',
        ]);

        // Enable foreign key constraints
        DB::statement('PRAGMA foreign_keys = ON;');
        
        // Find the location and make sure it exists
        $location = Location::find($validatedData['location_id']);
        
        if (!$location) {
            return redirect()->back()->with('error', 'Selected location does not exist');
        }

        $department = new Department();
        $department->name = $validatedData['name'];
        $department->desc = $validatedData['desc'];
        $department->location_id = $validatedData['location_id'];
        $department->status = 'Active';
        $department->save();

        // Log activity
        ActivityLogger::logCreated('Department', $validatedData['name']);

        return redirect('departments')->with('success', 'Department Added Successfully');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $locations = Location::where('status', 'Active')->get();
        return view('departments.edit', compact('department', 'locations'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,'.$id,
            'desc' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'status' => 'required|string',
        ]);

        // Enable foreign key constraints
        DB::statement('PRAGMA foreign_keys = ON;');
        
        $department = Department::findOrFail($id);
        
        // Verify location exists
        $location = Location::find($validatedData['location_id']);
        if (!$location) {
            return redirect()->back()->with('error', 'Selected location does not exist');
        }
        
        $department->name = $validatedData['name'];
        $department->desc = $validatedData['desc'];
        $department->location_id = $validatedData['location_id'];
        $department->status = $validatedData['status'];
        $department->save();

        // Log activity
        ActivityLogger::logUpdated('Department', $validatedData['name']);

        return redirect('departments')->with('success', 'Department Updated Successfully');
    }

    public function archive($id)
    {
        $department = Department::findOrFail($id);
        $name = $department->name;
        $department->delete(); // Soft delete (archives the department)

        // Log activity
        ActivityLogger::logArchived('Department', $name);

        return redirect('departments')->with('success', 'Department Archived Successfully');
    }

    public function getLocation($id)
    {
        $department = Department::findOrFail($id);
        return response()->json([
            'location_id' => $department->location_id,
            'location_name' => $department->location ? $department->location->name : null
        ]);
    }
}
