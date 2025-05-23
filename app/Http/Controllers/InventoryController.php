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

    /**
     * Generate a shorter asset tag by removing vowels and using a better format
     */
    public function generateAssetTag() {
        // Get current date in format YYMMDD
        $date = date('ymd');
        
        // Generate a random 4-character alphanumeric string without vowels
        $chars = '0123456789BCDFGHJKLMNPQRSTVWXYZ';
        $randomPart = '';
        for ($i = 0; $i < 4; $i++) {
            $randomPart .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        // Combine to create the asset tag: DATE-RANDOM
        $assetTag = $date . '-' . $randomPart;
        
        // Check if this asset tag already exists, if so, regenerate
        if (Inventory::where('asset_tag', $assetTag)->exists()) {
            return $this->generateAssetTag();
        }
        
        return $assetTag;
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
        // Log the request data for debugging
        Log::info('Inventory store request data:', $request->all());
        
        try {
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
                Log::warning('Inventory validation failed:', [
                    'errors' => $validator->errors()->toArray()
                ]);
                
                return redirect()->back()
                    ->withErrors($validator)  // Send to default error bag
                    ->withErrors($validator, 'inventoryForm')  // Also send to inventoryForm error bag
                    ->withInput();
            }

            // Process image upload if present
            $imagePath = null;
            if ($request->hasFile('asset_image')) {
                $image = $request->file('asset_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('assets', $imageName, 'public');
            }

            // Prepare custom fields
            $customFields = $request->has('custom_fields') ? $request->input('custom_fields') : [];
            
            // Create new inventory item
            $inventory = Inventory::create([
                'item_name' => $request->item_name,
                'category_id' => $request->category_id,
                'department_id' => $request->department_id,
                'users_id' => $request->users_id,
                'serial_no' => $request->serial_no,
                'asset_tag' => $request->asset_tag,
                'model_no' => $request->model_no,
                'manufacturer' => $request->manufacturer,
                'date_purchased' => $request->date_purchased,
                'purchased_from' => $request->purchased_from,
                'image_path' => $imagePath,
                'log_note' => $request->log_note,
                'custom_fields' => $customFields
            ]);

            Log::info('Inventory item created successfully:', [
                'id' => $inventory->id,
                'name' => $inventory->item_name
            ]);

            return redirect()->route('inventory.index')
                ->with('success', 'Asset created successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error creating inventory:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to create asset. Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id) {
        try {
            $inventoryItem = Inventory::findOrFail($id);
            $categories = Category::where('type', 'Asset')->pluck('category', 'id');
            $departments = Department::all();
            $users = User::all();
            
            // Get custom fields for the Asset type
            $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();
            
            // Get custom fields for the specific category
            $categoryCustomFields = [];
            if ($inventoryItem->category_id) {
                $category = Category::find($inventoryItem->category_id);
                if ($category && $category->custom_fields) {
                    $customFieldIds = json_decode($category->custom_fields, true) ?? [];
                    $categoryCustomFields = CustomField::whereIn('id', $customFieldIds)->get();
                }
            }
            
            return view('inventory.edit', compact('inventoryItem', 'categories', 'departments', 'users', 'assetCustomFields', 'categoryCustomFields'));
            
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->route('inventory.index')
                ->with('error', 'Failed to load asset details.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $inventoryItem = Inventory::findOrFail($id);
            
            // Prepare validation rules for standard fields
            $standardRules = [
                'item_name' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'department_id' => 'nullable|exists:departments,id',
                'users_id' => 'nullable|exists:users,id',
                'serial_no' => 'required|unique:inventories,serial_no,' . $id,
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
    
            // Process image upload if present
            if ($request->hasFile('asset_image')) {
                $image = $request->file('asset_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('assets', $imageName, 'public');
                $inventoryItem->image_path = $imagePath;
            }
    
            // Prepare custom fields
            $customFields = $request->has('custom_fields') ? $request->input('custom_fields') : [];
            
            // Update inventory item
            $inventoryItem->update([
                'item_name' => $request->item_name,
                'category_id' => $request->category_id,
                'department_id' => $request->department_id,
                'users_id' => $request->users_id,
                'serial_no' => $request->serial_no,
                'model_no' => $request->model_no,
                'manufacturer' => $request->manufacturer,
                'date_purchased' => $request->date_purchased,
                'purchased_from' => $request->purchased_from,
                'log_note' => $request->log_note,
                'custom_fields' => $customFields
            ]);
    
            return redirect()->route('inventory.index')
                ->with('success', 'Asset updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error updating inventory: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update asset. Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function archive($id)
    {
        try {
            $inventoryItem = Inventory::findOrFail($id);
            $inventoryItem->delete();
            
            return redirect()->route('inventory.index')
                ->with('success', 'Asset archived successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error archiving inventory: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to archive asset. Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $inventoryItem = Inventory::with(['category', 'department', 'user'])->findOrFail($id);
            
            // Get custom fields for the Asset type
            $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();
            
            // Get custom fields for the specific category
            $categoryCustomFields = [];
            if ($inventoryItem->category_id) {
                $category = Category::find($inventoryItem->category_id);
                if ($category && $category->custom_fields) {
                    $customFieldIds = json_decode($category->custom_fields, true) ?? [];
                    $categoryCustomFields = CustomField::whereIn('id', $customFieldIds)->get();
                }
            }
            
            // Merge both custom field collections
            $allCustomFields = $assetCustomFields->merge($categoryCustomFields);
            
            return view('inventory.show', compact('inventoryItem', 'allCustomFields'));
            
        } catch (\Exception $e) {
            Log::error('Error showing inventory details: ' . $e->getMessage());
            return redirect()->route('inventory.index')
                ->with('error', 'Failed to load asset details.');
        }
    }
}
