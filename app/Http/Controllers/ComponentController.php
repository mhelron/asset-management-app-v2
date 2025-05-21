<?php

namespace App\Http\Controllers;

use App\Models\Components;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Category;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ComponentController extends Controller
{
    public function index()
    {
        $components = Components::with(['inventory', 'user', 'category'])->get();
        return view('components.index', compact('components'));
    }

    public function getCustomFields($id) {
        try {
            $component = Components::findOrFail($id);
            
            // Get the custom fields that apply to Component
            $componentCustomFields = CustomField::whereJsonContains('applies_to', 'Component')->get();
            
            // If custom_fields is stored as JSON in the database
            $itemCustomFields = is_string($component->custom_fields) 
                ? json_decode($component->custom_fields, true) 
                : $component->custom_fields;
            
            // Format for display
            $formattedFields = [];
            foreach ($componentCustomFields as $field) {
                $fieldName = $field->name;
                $fieldValue = $itemCustomFields[$fieldName] ?? '-';
                
                $formattedFields[] = [
                    'name' => $fieldName,
                    'type' => $field->type,
                    'is_required' => $field->is_required,
                    'value' => is_array($fieldValue) ? 
                        (isset($fieldValue['original_name']) ? $fieldValue['original_name'] : json_encode($fieldValue)) : 
                        $fieldValue
                ];
            }
            
            return response()->json($formattedFields);
        } catch (\Exception $e) {
            Log::error('Error fetching custom fields: ' . $e->getMessage());
            return response()->json([], 404);
        }
    }

    public function getCategoryFields($id)
    {
        try {
            $category = Category::find($id);
            
            if (!$category) {
                return response()->json(['error' => 'Category not found'], 404);
            }
    
            // Get the custom field IDs
            $customFieldIds = json_decode($category->custom_fields, true) ?? [];
            
            // Fetch the actual custom field objects
            $customFields = CustomField::whereIn('id', $customFieldIds)->get();
            
            return response()->json($customFields);
        } catch (\Exception $e) {
            Log::error('Error fetching category fields: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        // Get all active inventory items (assets) for the dropdown
        $assets = Inventory::where('status', 'Active')->get();
        
        // Get all users for the assigned dropdown
        $users = User::all();
        
        // Get categories for the dropdown (filtered by type 'Component')
        $categories = Category::where('type', 'Component')->get();
        
        // Get component custom fields
        $componentCustomFields = CustomField::whereJsonContains('applies_to', 'Component')->get();
        
        return view('components.create', compact('assets', 'users', 'categories', 'componentCustomFields'));
    }

    public function store(Request $request)
    {
        // Prepare validation rules for standard fields
        $standardRules = [
            'component_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'serial_no' => 'required|unique:components,serial_no',
            'model_no' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'users_id' => 'nullable|exists:users,id',
            'date_purchased' => 'required|date',
            'purchased_from' => 'required|string|max:255',
            'log_note' => 'nullable|string',
            'inventory_id' => 'nullable|exists:inventories,id'
        ];

        // Get custom fields that apply to Component
        $componentCustomFields = CustomField::whereJsonContains('applies_to', 'Component')->get();
        
        // Get custom fields specific to the selected category
        $categoryCustomFields = [];
        if ($request->category_id) {
            $category = Category::find($request->category_id);
            if ($category) {
                $customFieldIds = json_decode($category->custom_fields, true) ?? [];
                $categoryCustomFields = CustomField::whereIn('id', $customFieldIds)->get();
            }
        }
        
        // Merge both custom field collections
        $allCustomFields = $componentCustomFields->merge($categoryCustomFields);

        // Prepare dynamic validation rules
        $customValidationRules = [];
        $customValidationMessages = [];

        foreach ($allCustomFields as $field) {
            $fieldName = $field->name;
            $rules = [];
            $messages = [];

            // Required validation
            if ($field->is_required) {
                $rules[] = 'required';
                $messages["custom_fields.{$fieldName}.required"] = 
                    "The {$fieldName} field is required.";
            }

            // Add rules if not empty
            if (!empty($rules)) {
                $customValidationRules["custom_fields.{$fieldName}"] = $rules;
            }
            
            // Add corresponding error messages
            if (!empty($messages)) {
                $customValidationMessages = array_merge($customValidationMessages, $messages);
            }
        }

        // Merge standard and custom validation rules
        $allRules = array_merge($standardRules, $customValidationRules);
        $validator = Validator::make($request->all(), $allRules, $customValidationMessages);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'componentForm')
                ->withInput();
        }

        try {
            // Process and save the component
            $customFields = $request->input('custom_fields', []);
            
            // Handle file uploads
            if ($request->hasFile('custom_fields_files')) {
                foreach ($request->file('custom_fields_files') as $field => $file) {
                    $path = $file->store('components', 'public');
                    $customFields[$field] = [
                        'path' => $path, 
                        'original_name' => $file->getClientOriginalName()
                    ];
                }
            }

            Components::create([
                'component_name' => $request->component_name,
                'category_id' => $request->category_id,
                'serial_no' => $request->serial_no,
                'model_no' => $request->model_no,
                'manufacturer' => $request->manufacturer,
                'users_id' => $request->users_id,
                'date_purchased' => $request->date_purchased,
                'purchased_from' => $request->purchased_from,
                'log_note' => $request->log_note,
                'inventory_id' => $request->inventory_id,
                'custom_fields' => $customFields,
            ]);

            return redirect()->route('components.index')
                ->with('success', 'Component created successfully.');
        } catch (\Exception $e) {
            Log::error('Error adding component: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $component = Components::with(['inventory', 'user', 'category'])->findOrFail($id);

        // Ensure date_purchased is converted to a Carbon instance if it's not already
        if ($component->date_purchased && !$component->date_purchased instanceof \Carbon\Carbon) {
            $component->date_purchased = \Carbon\Carbon::parse($component->date_purchased);
        }

        // Get all custom fields for components
        $componentCustomFields = CustomField::whereJsonContains('applies_to', 'Component')->get();

        return view('components.show', compact('component', 'componentCustomFields'));
    }

    public function edit($id)
    {
        $component = Components::findOrFail($id);
        
        // If date_purchased is a Carbon object, convert to string
        if ($component->date_purchased instanceof \Carbon\Carbon) {
            $component->date_purchased = $component->date_purchased->format('Y-m-d');
        }

        // Get all active inventory items (assets) for the dropdown
        $assets = Inventory::where('status', 'Active')->get();
        
        // Get all users for the assigned dropdown
        $users = User::all();
        
        // Get categories for the dropdown (filtered by type 'Component')
        $categories = Category::where('type', 'Component')->get();
        
        // Get component custom fields
        $componentCustomFields = CustomField::whereJsonContains('applies_to', 'Component')->get();

        return view('components.edit', compact('component', 'assets', 'users', 'categories', 'componentCustomFields'));
    }

    public function update(Request $request, $id)
    {
        $component = Components::findOrFail($id);
        
        // Prepare validation rules for standard fields
        $standardRules = [
            'component_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'serial_no' => 'required|unique:components,serial_no,' . $component->id,
            'model_no' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'users_id' => 'nullable|exists:users,id',
            'date_purchased' => 'required|date',
            'purchased_from' => 'required|string|max:255',
            'log_note' => 'nullable|string',
            'inventory_id' => 'nullable|exists:inventories,id'
        ];

        // Get custom fields that apply to Component
        $componentCustomFields = CustomField::whereJsonContains('applies_to', 'Component')->get();
        
        // Get custom fields specific to the selected category
        $categoryCustomFields = [];
        if ($request->category_id) {
            $category = Category::find($request->category_id);
            if ($category) {
                $customFieldIds = json_decode($category->custom_fields, true) ?? [];
                $categoryCustomFields = CustomField::whereIn('id', $customFieldIds)->get();
            }
        }
        
        // Merge both custom field collections
        $allCustomFields = $componentCustomFields->merge($categoryCustomFields);

        // Prepare dynamic validation rules
        $customValidationRules = [];
        $customValidationMessages = [];

        foreach ($allCustomFields as $field) {
            $fieldName = $field->name;
            $rules = [];
            $messages = [];

            // Required validation
            if ($field->is_required) {
                $rules[] = 'required';
                $messages["custom_fields.{$fieldName}.required"] = 
                    "The {$fieldName} field is required.";
            }

            // Add rules if not empty
            if (!empty($rules)) {
                $customValidationRules["custom_fields.{$fieldName}"] = $rules;
            }
            
            // Add corresponding error messages
            if (!empty($messages)) {
                $customValidationMessages = array_merge($customValidationMessages, $messages);
            }
        }

        // Merge standard and custom validation rules
        $allRules = array_merge($standardRules, $customValidationRules);
        $validator = Validator::make($request->all(), $allRules, $customValidationMessages);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'componentForm')
                ->withInput();
        }

        try {
            // Get existing custom fields
            $customFields = is_string($component->custom_fields) 
                ? json_decode($component->custom_fields, true) 
                : ($component->custom_fields ?? []);

            // Update with new form values
            if ($request->has('custom_fields')) {
                foreach ($request->input('custom_fields') as $key => $value) {
                    $customFields[$key] = $value;
                }
            }

            // Handle file uploads
            if ($request->hasFile('custom_fields_files')) {
                foreach ($request->file('custom_fields_files') as $field => $file) {
                    $path = $file->store('components', 'public');
                    $customFields[$field] = [
                        'path' => $path, 
                        'original_name' => $file->getClientOriginalName()
                    ];
                }
            }

            // Update the component
            $component->update([
                'component_name' => $request->component_name,
                'category_id' => $request->category_id,
                'serial_no' => $request->serial_no,
                'model_no' => $request->model_no,
                'manufacturer' => $request->manufacturer,
                'users_id' => $request->users_id,
                'date_purchased' => $request->date_purchased,
                'purchased_from' => $request->purchased_from,
                'log_note' => $request->log_note,
                'inventory_id' => $request->inventory_id,
                'custom_fields' => $customFields,
            ]);

            return redirect()->route('components.index')
                ->with('success', 'Component updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating component: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function archive($id)
    {
        $component = Components::find($id);

        if ($component) {
            $component->delete(); // Soft delete
            return redirect()->route('components.index')->with('success', 'Component archived successfully.');
        }

        return redirect()->route('components.index')->with('error', 'Component not found.');
    }
    
    /**
     * Get components that are not associated with any asset
     */
    public function getAvailableComponents()
    {
        try {
            // Debug info
            Log::info('getAvailableComponents method called');
            
            // Get components not associated with any asset
            $components = Components::with('category')
                ->whereNull('inventory_id')
                ->get();
                
            Log::info('Available components count: ' . $components->count());
            
            return response()->json($components);
        } catch (\Exception $e) {
            Log::error('Error fetching available components: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Associate a component with an asset
     */
    public function associateWithAsset(Request $request, $id)
    {
        try {
            Log::info('associateWithAsset method called for component ID: ' . $id);
            Log::info('Request data:', $request->all());
            
            $component = Components::findOrFail($id);
            $inventoryId = $request->inventory_id;
            
            Log::info('Inventory ID from request: ' . $inventoryId);
            
            // Verify that the inventory exists
            $inventory = Inventory::findOrFail($inventoryId);
            
            // Update the component with the inventory ID
            $component->update([
                'inventory_id' => $inventoryId
            ]);
            
            Log::info('Component associated successfully with asset ID: ' . $inventoryId);
            
            return response()->json([
                'success' => true,
                'message' => 'Component associated with asset successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error associating component: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Quick add a component and associate it with an asset
     */
    public function quickAdd(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'component_name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'serial_no' => 'required|unique:components,serial_no',
                'model_no' => 'required|string|max:255',
                'manufacturer' => 'required|string|max:255',
                'date_purchased' => 'required|date',
                'purchased_from' => 'required|string|max:255',
                'inventory_id' => 'required|exists:inventories,id',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Create the component
            $component = Components::create([
                'component_name' => $request->component_name,
                'category_id' => $request->category_id,
                'serial_no' => $request->serial_no,
                'model_no' => $request->model_no,
                'manufacturer' => $request->manufacturer,
                'date_purchased' => $request->date_purchased,
                'purchased_from' => $request->purchased_from,
                'inventory_id' => $request->inventory_id,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Component created and associated successfully',
                'component' => $component
            ]);
        } catch (\Exception $e) {
            Log::error('Error quick adding component: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}