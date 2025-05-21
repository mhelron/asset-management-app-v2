<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Department;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccessoryController extends Controller
{
    public function index()
    {
        $accessory = Accessory::with(['department', 'user'])->get();
        return view('accessory.index', compact('accessory'));
    }

    public function create()
    {
        // Get departments for dropdown
        $departments = Department::all();
        
        // Get users for dropdown
        $users = User::all();
        
        // Get accessory categories
        $categories = Category::where('type', 'Accessory')->get();
        
        return view('accessory.create', compact('departments', 'users', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'accessory_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'department_id' => 'required|exists:departments,id',
            'serial_no' => 'required|unique:accessories,serial_no',
            'model_no' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'users_id' => 'nullable|exists:users,id',
            'date_purchased' => 'required|date',
            'purchased_from' => 'required|string|max:255',
            'log_note' => 'nullable|string',
        ]);

        Accessory::create($validated);

        return redirect()->route('accessory.index')
            ->with('success', 'Accessory created successfully.');
    }

    public function show($id)
    {
        $accessory = Accessory::with(['department', 'user'])->findOrFail($id);

        // Ensure date_purchased is converted to a Carbon instance if it's not already
        if ($accessory->date_purchased && !$accessory->date_purchased instanceof \Carbon\Carbon) {
            $accessory->date_purchased = \Carbon\Carbon::parse($accessory->date_purchased);
        }

        return view('accessory.show', compact('accessory'));
    }

    public function edit($id)
    {
        $accessory = Accessory::findOrFail($id);
        
        // Get departments for dropdown
        $departments = Department::all();
        
        // Get users for dropdown
        $users = User::all();
        
        // Get accessory categories
        $categories = Category::where('type', 'Accessory')->get();
        
        // If date_purchased is a Carbon object, convert to string
        if ($accessory->date_purchased instanceof \Carbon\Carbon) {
            $accessory->date_purchased = $accessory->date_purchased->format('Y-m-d');
        }

        return view('accessory.edit', compact('accessory', 'departments', 'users', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $accessory = Accessory::findOrFail($id);
        
        $validated = $request->validate([
            'accessory_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'department_id' => 'required|exists:departments,id',
            'serial_no' => 'required|unique:accessories,serial_no,' . $accessory->id,
            'model_no' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'users_id' => 'nullable|exists:users,id',
            'date_purchased' => 'required|date',
            'purchased_from' => 'required|string|max:255',
            'log_note' => 'nullable|string',
        ]);

        $accessory->update($validated);

        return redirect()->route('accessory.index')
            ->with('success', 'Accessory updated successfully');
    }

    public function archive($id)
    {
        $accessory = Accessory::find($id);
        
        if (!$accessory) {
            return back()->with('error', 'Accessory not found');
        }

        $accessory->delete();

        return redirect()->route('accessory.index')->with('success', 'Accessory archived successfully.');
    }
}
