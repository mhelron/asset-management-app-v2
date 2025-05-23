<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CustomField;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        // Apply type filter if provided
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $query->where('category', 'like', '%' . $request->search . '%');
        }

        $categories = $query->get();
        
        // Get a list of possible category types for the filter dropdown
        $categoryTypes = ['Asset'];
        
        return view('categories.index', compact('categories', 'categoryTypes'));
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
            'type' => 'nullable|string|in:Asset'
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
            'type' => $validated['type'],
            'custom_fields' => $customFieldsJson
        ]);

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
            'type' => 'nullable|string|in:Asset'
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
                'type' => $validatedData['type'],
                'custom_fields' => $customFieldsJson
            ]);
            return redirect('categories')->with('success', 'Category Updated Successfully');
        }
        return redirect('categories')->with('error', 'Category Not Updated');
    }

    public function archive($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Category Archived Successfully');
        }

        return redirect()->route('categories.index')->with('error', 'Category Not Archived');
    }
    
    /**
     * Get categories by type
     */
    public function getCategoriesByType($type)
    {
        try {
            $categories = Category::where('type', $type)
                ->where('status', 'Active')
                ->get();
                
            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Error fetching categories by type: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
