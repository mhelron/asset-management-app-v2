<?php

namespace App\Http\Controllers;

use App\Models\Department;
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
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'location' => 'required|string',
        ]);

        Department::create([
            'name' => $validatedData['name'],
            'desc' => $validatedData['desc'],
            'location' => $validatedData['location'],
            'status' => 'Active',
        ]);

        return redirect('departments')->with('success', 'Department Added Successfully');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'location' => 'required|string',
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
}
