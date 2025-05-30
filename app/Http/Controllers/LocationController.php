<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::whereNull('deleted_at')->get();
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:locations',
            'desc' => 'required|string',
        ]);

        Location::create([
            'name' => $validatedData['name'],
            'desc' => $validatedData['desc'],
            'status' => 'Active',
        ]);

        // Log activity
        ActivityLogger::logCreated('Location', $validatedData['name']);

        return redirect('locations')->with('success', 'Location Added Successfully');
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,'.$id,
            'desc' => 'required|string',
            'status' => 'required|string',
        ]);

        $location = Location::findOrFail($id);
        $location->update($validatedData);

        // Log activity
        ActivityLogger::logUpdated('Location', $validatedData['name']);

        return redirect('locations')->with('success', 'Location Updated Successfully');
    }

    public function archive($id)
    {
        $location = Location::findOrFail($id);
        $locationName = $location->name;
        $location->delete(); // Soft delete (archives the location)

        // Log activity
        ActivityLogger::logArchived('Location', $locationName);

        return redirect('locations')->with('success', 'Location Archived Successfully');
    }
}
