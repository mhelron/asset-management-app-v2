<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class CustomFieldsController extends Controller
{
    /**
     * Display a listing of the custom fields.
     */
    public function index()
    {
        $customFields = CustomField::all(); // Fetch all custom fields
        return view('customfields.index', compact('customFields'));
    }

    /**
     * Show the form for creating a new custom field.
     */
    public function create()
    {
        return view('customfields.create');
    }

    /**
     * Store a newly created custom field in storage.
     */
    public function store(Request $request)
    {
        $request->validateWithBag('inventoryForm', [
            'name' => 'required|string|max:255',
            'is_required' => 'required|boolean',
            'applies_to' => 'required|array',
            'desc' => 'required|string',
            'type' => 'required|string|in:Text,Checkbox,Radio,Select',
            'text_type' => 'required_if:type,Text|string|in:Any,Email,Image,Date,Alpha-Dash,Numeric,Custom',
            'custom_regex' => 'required_if:text_type,Custom|nullable|string',
            'options' => 'required_if:type,Checkbox,Radio,Select|array|min:1',
            'options.*' => 'required|max:255|distinct',
        ],[
            'text_type.required_if' => 'The format type is required.',
            'text_type.in' => 'Invalid format type selected.',
            'options.*.required' => 'The option field is required.',
            'options.*.distinct' => 'Each option must be unique.',
            'options.*.max' => 'Each option cannot exceed 255 characters.',
            'custom_regex.required_if' => 'Custom regex pattern is required.',
        ]);

        $customField = CustomField::create([
            'name' => $request->name,
            'type' => $request->type,
            'desc' => $request->desc,
            'text_type' => $request->text_type,
            'custom_regex' => $request->text_type == 'Custom' ? $request->custom_regex : null,
            'is_required' => $request->is_required,
            'options' => in_array($request->type, ['Checkbox', 'Radio', 'Select']) 
                ? json_encode(array_filter($request->options)) 
                : null,
            'applies_to' => $request->applies_to,
        ]);

        // Log activity
        ActivityLogger::logCreated('Custom Field', $request->name);

        return redirect()->route('customfields.index')->with('success', 'Custom Field added successfully!');
    }

    /**
     * Display the specified custom field.
     */
    public function show($id)
    {
        $customField = CustomField::findOrFail($id);
        
        // Ensure options are properly decoded if they exist and are a JSON string
        if (is_string($customField->options) && !empty($customField->options)) {
            $customField->options = json_decode($customField->options, true);
        }
        
        return view('customfields.show', compact('customField'));
    }

    /**
     * Show the form for editing the specified custom field.
     */
    public function edit($id)
    {
        $customField = CustomField::findOrFail($id);
        
        // Ensure we only decode if it's a JSON string
        if (is_string($customField->options) && !empty($customField->options)) {
            $customField->options = json_decode($customField->options, true);
        } else {
            $customField->options = [];
        }
    
        return view('customfields.edit', compact('customField'));
    }    


    /**
     * Update the specified custom field in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_required' => 'required|boolean',
            'applies_to' => 'required|array',
            'desc' => 'required|string',
            'type' => 'required|string|in:Text,Checkbox,Radio,Select',
            'text_type' => 'required_if:type,Text|string|in:Any,Email,Image,Date,Alpha-Dash,Numeric,Custom',
            'custom_regex' => 'required_if:text_type,Custom|nullable|string',
            'options' => 'required_if:type,Checkbox,Radio,Select|array|min:1',
            'options.*' => 'required|max:255|distinct',
        ],[
            'text_type.required_if' => 'The format type is required.',
            'text_type.in' => 'Invalid format type selected.',
            'options.*.required' => 'The option field is required.',
            'options.*.distinct' => 'Each option must be unique.',
            'options.*.max' => 'Each option cannot exceed 255 characters.',
            'custom_regex.required_if' => 'Custom regex pattern is required.',
        ]);

        $customField = CustomField::findOrFail($id);

        $customField->update([
            'name' => $request->name,
            'type' => $request->type,
            'desc' => $request->desc,
            'text_type' => $request->text_type,
            'custom_regex' => $request->text_type == 'Custom' ? $request->custom_regex : null,
            'is_required' => $request->is_required,
            'options' => in_array($request->type, ['Checkbox', 'Radio', 'Select']) 
                ? json_encode(array_filter($request->options)) 
                : null,
            'applies_to' => $request->applies_to,
        ]);

        // Log activity
        ActivityLogger::logUpdated('Custom Field', $request->name);

        return redirect()->route('customfields.index')->with('success', 'Custom field updated successfully'); 
    }

    /**
     * Remove the specified custom field from storage.
     */
    public function archive($id)
    {
        try {
            $customField = CustomField::findOrFail($id);
            $fieldName = $customField->name;
            
            $customField->delete();
            
            // Log activity
            ActivityLogger::logArchived('Custom Field', $fieldName);
            
            return redirect()->route('customfields.index')->with('success', 'Custom field archived successfully');
        } catch (\Exception $e) {
            return redirect()->route('customfields.index')->with('error', 'Failed to archive custom field: ' . $e->getMessage());
        }
    }
}
