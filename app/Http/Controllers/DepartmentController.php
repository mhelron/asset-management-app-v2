<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Location;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::whereNull('deleted_at')->get();
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
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'location' => 'nullable|string',
            'location_id' => 'required|exists:locations,id',
        ]);

        Department::create([
            'name' => $validatedData['name'],
            'desc' => $validatedData['desc'],
            'location' => $validatedData['location'] ?? '',
            'location_id' => $validatedData['location_id'],
            'status' => 'Active',
        ]);

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
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'location' => 'nullable|string',
            'location_id' => 'required|exists:locations,id',
            'status' => 'required|string',
        ]);

        $department = Department::findOrFail($id);
        $department->update($validatedData);

        return redirect('departments')->with('success', 'Department Updated Successfully');
    }

    public function archive($id)
    {
        $department = Department::findOrFail($id);
        $department->delete(); // Soft delete (archives the department)

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
