<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CustomField;
use Illuminate\Support\Facades\Log;
use App\Helpers\ActivityLogger;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $query->where('category', 'like', '%' . $request->search . '%');
        }

        $categories = $query->get();
        
        return view('categories.index', compact('categories'));
    }
    
    public function create()
    {
        // Only fetch custom fields that apply to categories
        $customFields = CustomField::whereJsonContains('applies_to', 'Category')->get();
        
        return view('categories.create', compact('customFields'));
    }

    public function getCustomFields($id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([]);
        }
        
        // If using custom_fields column in categories table
        $customFieldIds = json_decode($category->custom_fields, true) ?? [];
        $customFields = CustomField::whereIn('id', $customFieldIds)->get();
        
        return response()->json($customFields);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'desc' => 'required|string',
        ]);

        // Prepare custom fields data
        $customFieldsJson = null;
        if ($request->has('custom_fields') && !empty($request->custom_fields)) {
            $customFieldsJson = json_encode($request->custom_fields);
        }

        // Create category with custom fields included
        $category = Category::create([
            'category' => $validated['category'],
            'desc' => $validated['desc'],
            'custom_fields' => $customFieldsJson
        ]);

        // Log activity
        ActivityLogger::logCreated('Category', $validated['category']);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $editdata = Category::find($id);
        
        if (!$editdata) {
            return redirect('categories')->with('error', 'Item ID Not Found');
        }
        
        $customFields = CustomField::whereJsonContains('applies_to', 'Category')->get();
        
        // If you need to get the actual CustomField models based on IDs stored in JSON
        $selectedCustomFieldIds = json_decode($editdata->custom_fields, true) ?? [];
        $selectedCustomFields = CustomField::whereIn('id', $selectedCustomFieldIds)->get();

        return view('categories.edit', compact('editdata', 'customFields', 'selectedCustomFields'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'category' => 'required',
            'desc' => 'required',
            'status' => 'required',
        ]);
        
        // Prepare custom fields data
        $customFieldsJson = null;
        if ($request->has('custom_fields') && !empty($request->custom_fields)) {
            $customFieldsJson = json_encode($request->custom_fields);
        }
        
        $category = Category::find($id);
        if ($category) {
            $category->update([
                'category' => $validatedData['category'],
                'desc' => $validatedData['desc'],
                'status' => $validatedData['status'],
                'custom_fields' => $customFieldsJson
            ]);

            // Log activity
            ActivityLogger::logUpdated('Category', $validatedData['category']);

            return redirect('categories')->with('success', 'Category Updated Successfully');
        }
        return redirect('categories')->with('error', 'Category Not Updated');
    }

    public function archive($id)
    {
        $category = Category::find($id);

        if ($category) {
            $categoryName = $category->category;
            $category->delete();
            
            // Log activity
            ActivityLogger::logArchived('Category', $categoryName);
            
            return redirect()->route('categories.index')->with('success', 'Category Archived Successfully');
        }

        return redirect()->route('categories.index')->with('error', 'Category Not Archived');
    }
}
