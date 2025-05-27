<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Department;
use App\Models\User;
use App\Models\Inventory;
use App\Models\CustomField;
use App\Models\AssetType;
use App\Models\Location;
use App\Models\AssetNote;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category_filter = $request->input('category');
        $department_filter = $request->input('department');
        $date_filter = $request->input('date_added');
        $type_filter = $request->input('type');
        $owner_filter = $request->input('owner');
        
        $query = Inventory::query();
        
        // Apply search filter if search term is provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'LIKE', "%{$search}%")
                  ->orWhere('serial_no', 'LIKE', "%{$search}%")
                  ->orWhere('model_no', 'LIKE', "%{$search}%")
                  ->orWhere('asset_tag', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply category filter
        if ($category_filter) {
            $query->where('category_id', $category_filter);
        }
        
        // Apply department filter
        if ($department_filter) {
            $query->where('department_id', $department_filter);
        }
        
        // Apply owner filter (user)
        if ($owner_filter) {
            $query->where('users_id', $owner_filter);
        }
        
        // Apply asset type filter
        if ($type_filter) {
            $query->where('asset_type_id', $type_filter);
        }
        
        // Apply date filter
        if ($date_filter) {
            if ($date_filter === 'today') {
                $query->whereDate('created_at', today());
            } elseif ($date_filter === 'this_week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($date_filter === 'this_month') {
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            } elseif ($date_filter === 'this_year') {
                $query->whereYear('created_at', now()->year);
            }
        }
        
        $inventory = $query->with(['category', 'department', 'user', 'assetType', 'location'])->paginate(10);
        
        // Maintain filter parameters in pagination links
        $inventory->appends([
            'search' => $search,
            'category' => $category_filter,
            'department' => $department_filter,
            'date_added' => $date_filter,
            'type' => $type_filter,
            'owner' => $owner_filter
        ]);
        
        $categories = Category::where('type', 'Asset')->get();
        $departments = Department::all();
        $users = User::all();
        $assetTypes = AssetType::where('status', 'Active')->get();
        
        // Fetch custom fields that apply to Assets
        $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();

        return view('inventory.index', compact(
            'inventory', 
            'categories', 
            'departments',
            'users',
            'assetTypes',
            'assetCustomFields',
            'search',
            'category_filter',
            'department_filter',
            'date_filter',
            'type_filter',
            'owner_filter'
        ));
    }

    public function getInventoryData(Request $request)
    {
        try {
            $search = $request->input('search');
            $category = $request->input('category');
            $department = $request->input('department');
            $date_added = $request->input('date_added');
            $type = $request->input('type');
            $owner = $request->input('owner');
            
            // Log filter parameters for debugging
            Log::info('Inventory filter parameters:', [
                'search' => $search,
                'category' => $category,
                'department' => $department,
                'date_added' => $date_added,
                'type' => $type,
                'owner' => $owner
            ]);
            
            $query = Inventory::query();
            
            // Eager load relationships
            $query->with(['category', 'department', 'user', 'assetType', 'location']);
            
            // Apply search filter if search term is provided
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('item_name', 'LIKE', "%{$search}%")
                      ->orWhere('serial_no', 'LIKE', "%{$search}%")
                      ->orWhere('model_no', 'LIKE', "%{$search}%")
                      ->orWhere('asset_tag', 'LIKE', "%{$search}%");
                });
            }
            
            // Apply category filter
            if ($category) {
                $query->where('category_id', $category);
            }
            
            // Apply department filter
            if ($department) {
                $query->where('department_id', $department);
            }
            
            // Apply owner filter (user)
            if ($owner) {
                $query->where('users_id', $owner);
            }
            
            // Apply asset type filter
            if ($type) {
                $query->where('asset_type_id', $type);
            }
            
            // Apply date filter
            if ($date_added) {
                if ($date_added === 'today') {
                    $query->whereDate('created_at', today());
                } elseif ($date_added === 'this_week') {
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($date_added === 'this_month') {
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                } elseif ($date_added === 'this_year') {
                    $query->whereYear('created_at', now()->year);
                }
            }
            
            // Get the page from the request, but reset to page 1 when filters change
            $page = $request->input('reset_pagination') ? 1 : $request->input('page', 1);
            
            $inventory = $query->paginate(10);
            
            // Count filtered results for debugging
            Log::info('Filtered inventory count: ' . $inventory->total());
            
            return response()->json([
                'inventory' => $inventory,
                'links' => $inventory->links()->toHtml(),
                'current_page' => $inventory->currentPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getInventoryData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while fetching inventory data: ' . $e->getMessage()], 500);
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
        $assetTypes = AssetType::where('status', 'Active')->get();
        $locations = Location::where('status', 'Active')->get();
        
        $assetTag = old('asset_tag') ?: $this->generateAssetTag();
        
        $assetCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')->get();
    
        return view('inventory.create', compact('categories', 'departments', 'users', 'assetTag', 'assetCustomFields', 'assetTypes', 'locations'));
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
                'asset_type_id' => 'required|exists:asset_types,id',
                'department_id' => 'nullable|exists:departments,id',
                'location_id' => 'nullable|exists:locations,id',
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
                'asset_type_id' => $request->asset_type_id,
                'department_id' => $request->department_id,
                'location_id' => $request->location_id,
                'users_id' => $request->users_id,
                'asset_tag' => $request->asset_tag,
                'serial_no' => $request->serial_no,
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
            $assetTypes = \App\Models\AssetType::where('status', 'Active')->get();
            $locations = \App\Models\Location::where('status', 'Active')->get();
            
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
            
            return view('inventory.edit', compact('inventoryItem', 'categories', 'departments', 'users', 'assetCustomFields', 'categoryCustomFields', 'assetTypes', 'locations'));
            
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
            
            // Debug logging for request data
            Log::info('Inventory update request data:', [
                'id' => $id,
                'asset_type_id' => $request->asset_type_id,
                'item_name' => $request->item_name,
                'category_id' => $request->category_id,
                'all_data' => $request->all(),
                'request_method' => $request->method(),
                'request_path' => $request->path(),
                'has_asset_type_id' => $request->has('asset_type_id'),
                'asset_type_id_in_array' => array_key_exists('asset_type_id', $request->all())
            ]);
            
            // Debug logging for current inventory data
            Log::info('Current inventory data:', [
                'id' => $inventoryItem->id,
                'asset_type_id' => $inventoryItem->asset_type_id,
                'item_name' => $inventoryItem->item_name,
                'category_id' => $inventoryItem->category_id,
                'fillable' => $inventoryItem->getFillable()
            ]);
            
            // Prepare validation rules for standard fields
            $standardRules = [
                'item_name' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'asset_type_id' => 'required|exists:asset_types,id',
                'department_id' => 'nullable|exists:departments,id',
                'location_id' => 'nullable|exists:locations,id',
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
            
            // Debug log before update
            Log::info('Inventory update data:', [
                'item_name' => $request->item_name,
                'asset_type_id' => $request->asset_type_id,
                'category_id' => $request->category_id
            ]);
            
            // Create update data array explicitly
            $updateData = [
                'item_name' => $request->item_name,
                'category_id' => $request->category_id,
                'asset_type_id' => $request->asset_type_id,
                'department_id' => $request->department_id,
                'location_id' => $request->location_id,
                'users_id' => $request->users_id,
                'serial_no' => $request->serial_no,
                'model_no' => $request->model_no,
                'manufacturer' => $request->manufacturer,
                'date_purchased' => $request->date_purchased,
                'purchased_from' => $request->purchased_from,
                'log_note' => $request->log_note,
                'custom_fields' => $customFields
            ];
            
            Log::info('Update data array:', $updateData);
            
            // Update inventory item
            $inventoryItem->update($updateData);
            
            // Verify asset_type_id is updated directly
            if ($request->asset_type_id != $inventoryItem->asset_type_id) {
                Log::warning('Asset type ID mismatch after update - forcing direct update', [
                    'requested' => $request->asset_type_id,
                    'actual' => $inventoryItem->asset_type_id
                ]);
                
                // Direct update as fallback
                $inventoryItem->asset_type_id = $request->asset_type_id;
                $inventoryItem->save();
            }
            
            // Debug log after update
            Log::info('Inventory after update:', [
                'id' => $inventoryItem->id,
                'asset_type_id' => $inventoryItem->asset_type_id,
                'item_name' => $inventoryItem->item_name,
                'category_id' => $inventoryItem->category_id,
                'reload_check' => Inventory::find($id)->asset_type_id
            ]);
    
            return redirect()->route('inventory.index')
                ->with('success', 'Asset updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error updating inventory: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
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
            $inventoryItem = Inventory::with(['category', 'department', 'user', 'notes.user', 'assetType', 'location'])->findOrFail($id);
            
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
            
            // Get asset notes
            $assetNotes = $inventoryItem->notes()->orderBy('created_at', 'desc')->get();
            
            // Generate QR code if asset type requires it
            $qrCode = null;
            if ($inventoryItem->assetType && $inventoryItem->assetType->requires_qr_code) {
                $qrContent = route('inventory.show', $id);
                $qrCode = QrCode::size(200)->generate($qrContent);
            }
            
            return view('inventory.show', compact('inventoryItem', 'allCustomFields', 'assetNotes', 'qrCode'));
            
        } catch (\Exception $e) {
            Log::error('Error showing inventory details: ' . $e->getMessage());
            return redirect()->route('inventory.index')
                ->with('error', 'Failed to load asset details.');
        }
    }

    /**
     * Add a note to an asset.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addNote(Request $request, $id)
    {
        try {
            $request->validate([
                'note_content' => 'required|string',
            ]);
            
            // Process note content to normalize line breaks and remove unnecessary whitespace
            $content = trim($request->note_content);
            // Replace consecutive line breaks with a single line break
            $content = preg_replace('/\r\n|\r|\n/', "\n", $content); // Normalize line breaks
            $content = preg_replace('/\n{3,}/', "\n\n", $content); // Limit to max 2 consecutive line breaks
            
            $inventoryItem = Inventory::findOrFail($id);
            
            // Create the note
            $note = new AssetNote([
                'content' => $content,
                'user_id' => Auth::id(),
            ]);
            
            $inventoryItem->notes()->save($note);
            
            return redirect()->route('inventory.show', $id)
                ->with('success', 'Note added successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error adding note: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to add note. Error: ' . $e->getMessage());
        }
    }

    /**
     * Update a note.
     *
     * @param  Request  $request
     * @param  int  $inventoryId
     * @param  int  $noteId
     * @return \Illuminate\Http\Response
     */
    public function updateNote(Request $request, $inventoryId, $noteId)
    {
        try {
            $request->validate([
                'note_content' => 'required|string',
            ]);
            
            $note = AssetNote::findOrFail($noteId);
            
            // Ensure the user owns this note
            if (Auth::id() != $note->user_id) {
                return redirect()->route('inventory.show', $inventoryId)
                    ->with('error', 'You do not have permission to edit this note.');
            }
            
            // Process note content to normalize line breaks and remove unnecessary whitespace
            $content = trim($request->note_content);
            // Replace consecutive line breaks with a single line break
            $content = preg_replace('/\r\n|\r|\n/', "\n", $content); // Normalize line breaks
            $content = preg_replace('/\n{3,}/', "\n\n", $content); // Limit to max 2 consecutive line breaks
            
            $note->update([
                'content' => $content,
            ]);
            
            return redirect()->route('inventory.show', $inventoryId)
                ->with('success', 'Note updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error updating note: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update note. Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete a note.
     *
     * @param  int  $inventoryId
     * @param  int  $noteId
     * @return \Illuminate\Http\Response
     */
    public function deleteNote($inventoryId, $noteId)
    {
        try {
            $note = AssetNote::findOrFail($noteId);
            
            // Ensure the user owns this note
            if (Auth::id() != $note->user_id) {
                return redirect()->route('inventory.show', $inventoryId)
                    ->with('error', 'You do not have permission to delete this note.');
            }
            
            $note->delete();
            
            return redirect()->route('inventory.show', $inventoryId)
                ->with('success', 'Note deleted successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error deleting note: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete note. Error: ' . $e->getMessage());
        }
    }

    /**
     * Test a QR code redirection
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function testQRCode($id)
    {
        try {
            $inventoryItem = Inventory::findOrFail($id);
            
            return view('inventory.qr-test-success', compact('inventoryItem'));
                
        } catch (\Exception $e) {
            Log::error('Error testing QR code: ' . $e->getMessage());
            return redirect()->route('inventory.index')
                ->with('error', 'Failed to load asset for QR test.');
        }
    }
}
