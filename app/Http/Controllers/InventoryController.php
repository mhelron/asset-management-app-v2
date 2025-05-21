<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Department;
use App\Models\User;
use App\Models\Inventory;
use App\Models\CustomField;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::with('category')->get();
        $categories = Category::where('type', 'Asset')->get();
        
        // Fetch custom fields that apply to Assets
        $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();

        return view('inventory.index', compact('inventory', 'categories', 'assetCustomFields'));
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

    public function getItemDetails($id)
    {
        try {
            $inventoryItem = Inventory::with('category')->findOrFail($id);

            return response()->json([
                'item_name' => $inventoryItem->item_name,
                'category' => $inventoryItem->category->category ?? 'N/A',
                'status' => $inventoryItem->status,
                'custom_fields' => $inventoryItem->custom_fields ?? [],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching inventory details: ' . $e->getMessage());
            return response()->json(['error' => 'Item not found'], 404);
        }
    }

    public function getCustomFields($id) {
        try {
            $item = Inventory::findOrFail($id);
            
            // Get the custom fields that apply to Asset
            $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();
            
            // If custom_fields is stored as JSON in the database
            $itemCustomFields = is_string($item->custom_fields) 
                ? json_decode($item->custom_fields, true) 
                : $item->custom_fields;
            
            // Format for display
            $formattedFields = [];
            foreach ($assetCustomFields as $field) {
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

    public function generateAssetTag($length = 10) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
        
    public function create() {
        $categories = Category::where('type', 'Asset')->get();
        $departments = Department::all();
        $users = User::all();
        
        $assetTag = old('asset_tag') ?: $this->generateAssetTag();
        
        $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();
    
        return view('inventory.create', compact('categories', 'departments', 'users', 'assetTag', 'assetCustomFields'));
    }

    public function store(Request $request)
    {
        $existingItem = Inventory::withTrashed()
            ->where('serial_no', $request->serial_no)
            ->first();
        
        if ($existingItem && $existingItem->trashed()) {
            $existingItem->forceDelete();
        }
        
        // Prepare validation rules for standard fields
        $standardRules = [
            'item_name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'department_id' => 'nullable|exists:departments,id',
            'users_id' => 'nullable|exists:users,id',
            'serial_no' => 'required|unique:inventories,serial_no',
            'model_no' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'date_purchased' => 'required|date',
            'purchased_from' => 'required|string|max:255',
            'asset_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'log_note' => 'nullable|string'
        ];

        // Get custom fields that apply to Asset
        $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();
        
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
        $allCustomFields = $assetCustomFields->merge($categoryCustomFields);

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

            // Type-specific validations
            switch ($field->type) {
                case 'Text':
                    // Add specific text type validations
                    switch ($field->text_type) {
                        case 'Email':
                            $rules[] = 'email';
                            $messages["custom_fields.{$fieldName}.email"] = 
                                "The {$fieldName} must be a valid email address.";
                            break;
                        case 'Numeric':
                            $rules[] = 'numeric';
                            $messages["custom_fields.{$fieldName}.numeric"] = 
                                "The {$fieldName} must be a number.";
                            break;
                        case 'Date':
                            $rules[] = 'date';
                            $messages["custom_fields.{$fieldName}.date"] = 
                                "The {$fieldName} must be a valid date.";
                            break;
                        case 'Custom':
                            if ($field->custom_regex) {
                                $rules[] = "regex:{$field->custom_regex}";
                                $messages["custom_fields.{$fieldName}.regex"] = 
                                    "The {$fieldName} format is invalid.";
                            }
                            break;
                    }
                    break;

                case 'Select':
                    $options = json_decode($field->options, true);
                    if ($options) {
                        $rules[] = 'in:' . implode(',', $options);
                        $messages["custom_fields.{$fieldName}.in"] = 
                            "The selected {$fieldName} is invalid.";
                    }
                    break;

                case 'Checkbox':
                    $rules[] = 'array';
                    $messages["custom_fields.{$fieldName}.array"] = 
                        "The {$fieldName} must be a valid selection.";
                    break;
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
                ->withErrors($validator, 'inventoryForm')
                ->withInput();
        }

        try {
            // Now that validation has passed, we can safely fetch the category
            $category = Category::findOrFail($request->category_id);
            
            // Process and save the inventory item
            $customFields = $request->input('custom_fields', []);
            
            // Handle file uploads
            if ($request->hasFile('custom_fields_files')) {
                foreach ($request->file('custom_fields_files') as $field => $file) {
                    $path = $file->store('inventory', 'public');
                    $customFields[$field] = [
                        'path' => $path, 
                        'original_name' => $file->getClientOriginalName()
                    ];
                }
            }

            // Handle asset image upload
            $imagePath = null;
            if ($request->hasFile('asset_image')) {
                $imagePath = $request->file('asset_image')->store('assets', 'public');
            }

            $validatedData['asset_tag'] = $this->generateAssetTag();
            
            Inventory::create([
                'item_name' => $request->item_name,
                'category_id' => $request->category_id,
                'department_id' => $request->department_id,
                'users_id' => $request->users_id,
                'asset_tag' => $request->asset_tag,
                'serial_no' => $request->serial_no,
                'model_no' => $request->model_no,
                'manufacturer' => $request->manufacturer,
                'date_purchased' => $request->date_purchased,
                'purchased_from' => $request->purchased_from,
                'image_path' => $imagePath,
                'log_note' => $request->log_note,
                'custom_fields' => $customFields,
                'status' => 'Active',
            ]);

            return redirect()->route('inventory.index')->with('success', 'Item added successfully');
        } catch (\Exception $e) {
            Log::error('Error adding inventory: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id) {
        $inventoryItem = Inventory::with('category')->findOrFail($id);
        $categories = Category::where('type', 'Asset')->pluck('category', 'id');
        $departments = Department::all();
        $users = User::all();
        $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();
        
        return view('inventory.edit', compact('inventoryItem', 'categories', 'assetCustomFields', 'id', 'departments', 'users'));
    }

    public function update(Request $request, $id)
    {
        $inventoryItem = Inventory::findOrFail($id);
        
        // Prepare validation rules for standard fields
        $standardRules = [
            'item_name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'department_id' => 'nullable|exists:departments,id',
            'users_id' => 'nullable|exists:users,id',
            'asset_tag' => 'required',
            'serial_no' => 'required|unique:inventories,serial_no,'.$id,
            'model_no' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'date_purchased' => 'required|date',
            'purchased_from' => 'required|string|max:255',
            'asset_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'log_note' => 'nullable|string'
        ];

        // Get custom fields that apply to Asset
        $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();
        
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
        $allCustomFields = $assetCustomFields->merge($categoryCustomFields);

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

            // Type-specific validations
            switch ($field->type) {
                case 'Text':
                    // Add specific text type validations
                    switch ($field->text_type) {
                        case 'Email':
                            $rules[] = 'email';
                            $messages["custom_fields.{$fieldName}.email"] = 
                                "The {$fieldName} must be a valid email address.";
                            break;
                        case 'Numeric':
                            $rules[] = 'numeric';
                            $messages["custom_fields.{$fieldName}.numeric"] = 
                                "The {$fieldName} must be a number.";
                            break;
                        case 'Date':
                            $rules[] = 'date';
                            $messages["custom_fields.{$fieldName}.date"] = 
                                "The {$fieldName} must be a valid date.";
                            break;
                        case 'Custom':
                            if ($field->custom_regex) {
                                $rules[] = "regex:{$field->custom_regex}";
                                $messages["custom_fields.{$fieldName}.regex"] = 
                                    "The {$fieldName} format is invalid.";
                            }
                            break;
                    }
                    break;

                case 'Select':
                    $options = json_decode($field->options, true);
                    if ($options) {
                        $rules[] = 'in:' . implode(',', $options);
                        $messages["custom_fields.{$fieldName}.in"] = 
                            "The selected {$fieldName} is invalid.";
                    }
                    break;

                case 'Checkbox':
                    $rules[] = 'array';
                    $messages["custom_fields.{$fieldName}.array"] = 
                        "The {$fieldName} must be a valid selection.";
                    break;
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
                ->withErrors($validator, 'inventoryForm')
                ->withInput();
        }

        try {
            // Get existing custom fields
            $customFields = is_string($inventoryItem->custom_fields) 
                ? json_decode($inventoryItem->custom_fields, true) 
                : ($inventoryItem->custom_fields ?? []);

            // Update with new form values
            if ($request->has('custom_fields')) {
                foreach ($request->input('custom_fields') as $key => $value) {
                    $customFields[$key] = $value;
                }
            }

            // Handle file uploads
            if ($request->hasFile('custom_fields_files')) {
                foreach ($request->file('custom_fields_files') as $field => $file) {
                    $path = $file->store('inventory', 'public');
                    $customFields[$field] = [
                        'path' => $path, 
                        'original_name' => $file->getClientOriginalName()
                    ];
                }
            }
            
            // Handle asset image upload
            $imagePath = $inventoryItem->image_path;
            if ($request->hasFile('asset_image')) {
                $imagePath = $request->file('asset_image')->store('assets', 'public');
            }
            
            // Update the inventory item with all fields
            $inventoryItem->update([
                'item_name' => $request->item_name,
                'category_id' => $request->category_id,
                'department_id' => $request->department_id,
                'users_id' => $request->users_id,
                'asset_tag' => $request->asset_tag,
                'serial_no' => $request->serial_no,
                'model_no' => $request->model_no,
                'manufacturer' => $request->manufacturer,
                'date_purchased' => $request->date_purchased,
                'purchased_from' => $request->purchased_from,
                'image_path' => $imagePath,
                'log_note' => $request->log_note,
                'custom_fields' => $customFields,
            ]);

            return redirect()->route('inventory.index')->with('success', 'Item updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating inventory: ' . $e->getMessage());
            return redirect()->route('inventory.edit', $id)->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function archive($id)
    {
        $item = Inventory::find($id);

        if ($item) {
            $item->delete(); // Soft delete
            return redirect()->route('inventory.index')->with('success', 'Inventory item archived successfully.');
        }

        return redirect()->route('inventory.index')->with('error', 'Inventory item not found.');
    }

    public function show($id)
    {
        try {
            $inventory = Inventory::with(['category', 'components', 'user', 'department'])->findOrFail($id);
            
            // Get all custom fields for assets
            $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();
            
            return view('inventory.show', compact('inventory', 'assetCustomFields'));
        } catch (\Exception $e) {
            Log::error('Error viewing inventory details: ' . $e->getMessage());
            return redirect()->route('inventory.index')->with('error', 'Error viewing asset details: ' . $e->getMessage());
        }
    }

}
