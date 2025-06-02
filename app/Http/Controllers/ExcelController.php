<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\AssetType;
use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExcelController extends Controller
{
    /**
     * Show the export/import page
     */
    public function index()
    {
        $categories = Category::where('type', 'Asset')->get();
        return view('excel.index', compact('categories'));
    }

    /**
     * Generate a template with all categories
     */
    public function generateTemplate()
    {
        try {
            // Get all active categories
            $categories = Category::where('type', 'Asset')->get();
            
            if ($categories->isEmpty()) {
                return redirect()->back()->with('error', 'No categories found. Please create at least one category first.');
            }
            
            // Create new spreadsheet
            $spreadsheet = new Spreadsheet();
            
            // Create instructions sheet
            $infoSheet = $spreadsheet->getActiveSheet();
            $infoSheet->setTitle('Instructions');
            
            // Instructions header
            $infoSheet->setCellValue('A1', 'Asset Inventory Import Template');
            $infoSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            $infoSheet->setCellValue('A3', 'Instructions:');
            $infoSheet->getStyle('A3')->getFont()->setBold(true);
            
            $infoSheet->setCellValue('A4', '1. This template contains a separate worksheet for each asset category.');
            $infoSheet->setCellValue('A5', '2. Each worksheet has the appropriate fields for that category, including custom fields.');
            $infoSheet->setCellValue('A6', '3. Fields marked with * are required.');
            $infoSheet->setCellValue('A7', '4. Date format should be YYYY-MM-DD (e.g., 2023-06-01)');
            $infoSheet->setCellValue('A8', '5. Use dropdown lists for Department, Location, and User fields.');
            $infoSheet->setCellValue('A9', '6. Do not modify any fields marked "DO NOT MODIFY".');
            
            // List all departments, locations and users for reference
            $infoSheet->setCellValue('A11', 'Reference Data:');
            $infoSheet->getStyle('A11')->getFont()->setBold(true);
            
            // Get data for dropdowns
            $departments = Department::pluck('name')->toArray();
            $locations = Location::pluck('name')->toArray();
            $users = User::select(DB::raw("first_name || ' ' || last_name AS full_name"))->pluck('full_name')->toArray();
            
            // Add reference data to Instructions sheet
            $infoSheet->setCellValue('A13', 'Departments:');
            $infoSheet->getStyle('A13')->getFont()->setBold(true);
            $row = 14;
            foreach ($departments as $dept) {
                $infoSheet->setCellValue('A' . $row, $dept);
                $row++;
            }
            
            $infoSheet->setCellValue('C13', 'Locations:');
            $infoSheet->getStyle('C13')->getFont()->setBold(true);
            $row = 14;
            foreach ($locations as $loc) {
                $infoSheet->setCellValue('C' . $row, $loc);
                $row++;
            }
            
            $infoSheet->setCellValue('E13', 'Users:');
            $infoSheet->getStyle('E13')->getFont()->setBold(true);
            $row = 14;
            foreach ($users as $user) {
                $infoSheet->setCellValue('E' . $row, $user);
                $row++;
            }
            
            // Create reference lists for data validation
            $refSheet = $spreadsheet->createSheet();
            $refSheet->setTitle('ReferenceData');
            
            // Add department list to reference sheet
            $refSheet->setCellValue('A1', 'Departments');
            $refSheet->getStyle('A1')->getFont()->setBold(true);
            for ($i = 0; $i < count($departments); $i++) {
                $refSheet->setCellValue('A' . ($i + 2), $departments[$i]);
            }
            
            // Add location list to reference sheet
            $refSheet->setCellValue('B1', 'Locations');
            $refSheet->getStyle('B1')->getFont()->setBold(true);
            for ($i = 0; $i < count($locations); $i++) {
                $refSheet->setCellValue('B' . ($i + 2), $locations[$i]);
            }
            
            // Add user list to reference sheet
            $refSheet->setCellValue('C1', 'Users');
            $refSheet->getStyle('C1')->getFont()->setBold(true);
            for ($i = 0; $i < count($users); $i++) {
                $refSheet->setCellValue('C' . ($i + 2), $users[$i]);
            }
            
            // Define named ranges for data validation
            $deptLastRow = count($departments) + 1;
            $locLastRow = count($locations) + 1;
            $userLastRow = count($users) + 1;
            
            $spreadsheet->addNamedRange(
                new \PhpOffice\PhpSpreadsheet\NamedRange('Departments', $refSheet, 'A2:A' . $deptLastRow)
            );
            $spreadsheet->addNamedRange(
                new \PhpOffice\PhpSpreadsheet\NamedRange('Locations', $refSheet, 'B2:B' . $locLastRow)
            );
            $spreadsheet->addNamedRange(
                new \PhpOffice\PhpSpreadsheet\NamedRange('Users', $refSheet, 'C2:C' . $userLastRow)
            );
            
            // Hide the reference sheet
            $refSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
            
            // Create a sheet for each category
            foreach ($categories as $index => $category) {
                // Create a new worksheet for this category
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle(substr($category->category, 0, 31)); // Excel has a 31 char limit for sheet names
                
                // Get custom fields for this category
                $customFieldIds = json_decode($category->custom_fields, true) ?? [];
                $categoryCustomFields = CustomField::whereIn('id', $customFieldIds)->get();
                
                // Get general asset custom fields
                $generalCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')
                    ->whereNotIn('id', $customFieldIds)
                    ->get();
                
                // Combine both sets of custom fields
                $allCustomFields = $categoryCustomFields->merge($generalCustomFields);
                
                // First create dropdown data in hidden columns
                // Department list
                $sheet->setCellValue('AA1', 'DEPARTMENTS');
                $deptRow = 2;
                foreach ($departments as $dept) {
                    $sheet->setCellValue('AA' . $deptRow, $dept);
                    $deptRow++;
                }
                $deptDataRange = 'AA2:AA' . ($deptRow - 1);
                
                // Location list
                $sheet->setCellValue('AB1', 'LOCATIONS');
                $locRow = 2;
                foreach ($locations as $loc) {
                    $sheet->setCellValue('AB' . $locRow, $loc);
                    $locRow++;
                }
                $locDataRange = 'AB2:AB' . ($locRow - 1);
                
                // User list
                $sheet->setCellValue('AC1', 'USERS');
                $userRow = 2;
                foreach ($users as $user) {
                    $sheet->setCellValue('AC' . $userRow, $user);
                    $userRow++;
                }
                $userDataRange = 'AC2:AC' . ($userRow - 1);
                
                // Hide these columns
                $sheet->getColumnDimension('AA')->setVisible(false);
                $sheet->getColumnDimension('AB')->setVisible(false);
                $sheet->getColumnDimension('AC')->setVisible(false);
                
                // Standard columns for all categories
                $columns = [
                    'A' => 'Item Name*',
                    'B' => 'Serial Number*',
                    'C' => 'Model Number*',
                    'D' => 'Manufacturer*',
                    'E' => 'Date Purchased (YYYY-MM-DD)*',
                    'F' => 'Purchased From*',
                    'G' => 'Department',
                    'H' => 'Location',
                    'I' => 'Assigned User',
                    'J' => 'Notes',
                    'K' => 'Category ID (DO NOT MODIFY)*'
                ];
                
                // Add custom field headers
                $col = 'L';
                foreach ($allCustomFields as $field) {
                    $columns[$col] = $field->name . ($field->is_required ? '*' : '');
                    $col = chr(ord($col) + 1);
                }
                
                // Set headers
                foreach ($columns as $col => $header) {
                    $sheet->setCellValue($col . '1', $header);
                    $sheet->getStyle($col . '1')->getFont()->setBold(true);
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                
                // Add category ID in the first row (for the import to know which category this item belongs to)
                $sheet->setCellValue('K2', $category->id);
                
                // Add a sample row
                $sheet->setCellValue('A2', 'Sample ' . $category->category);
                $sheet->setCellValue('B2', 'SN-' . strtoupper(substr(md5($category->category), 0, 8)));
                $sheet->setCellValue('C2', 'MDL-' . strtoupper(substr(md5($category->category), 8, 8)));
                $sheet->setCellValue('D2', 'Sample Manufacturer');
                $sheet->setCellValue('E2', date('Y-m-d'));
                $sheet->setCellValue('F2', 'Sample Vendor');
                
                if (count($departments) > 0) {
                    $sheet->setCellValue('G2', $departments[0]);
                }
                
                if (count($locations) > 0) {
                    $sheet->setCellValue('H2', $locations[0]);
                }
                
                if (count($users) > 0) {
                    $sheet->setCellValue('I2', $users[0]);
                }
                
                $sheet->setCellValue('J2', 'Sample notes for ' . $category->category);
                
                // Apply data validation to cells using explicit ranges
                // Department dropdown (column G)
                for ($i = 2; $i <= 20; $i++) {
                    $objValidation = $sheet->getCell('G' . $i)->getDataValidation();
                    $objValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $objValidation->setAllowBlank(true);
                    $objValidation->setShowInputMessage(true);
                    $objValidation->setShowErrorMessage(true);
                    $objValidation->setShowDropDown(true);
                    $objValidation->setErrorTitle('Invalid Department');
                    $objValidation->setError('Please select a department from the dropdown list');
                    $objValidation->setPromptTitle('Select Department');
                    $objValidation->setPrompt('Choose a department from the dropdown');
                    $objValidation->setFormula1($deptDataRange);
                }
                
                // Location dropdown (column H)
                for ($i = 2; $i <= 20; $i++) {
                    $objValidation = $sheet->getCell('H' . $i)->getDataValidation();
                    $objValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $objValidation->setAllowBlank(true);
                    $objValidation->setShowInputMessage(true);
                    $objValidation->setShowErrorMessage(true);
                    $objValidation->setShowDropDown(true);
                    $objValidation->setErrorTitle('Invalid Location');
                    $objValidation->setError('Please select a location from the dropdown list');
                    $objValidation->setPromptTitle('Select Location');
                    $objValidation->setPrompt('Choose a location from the dropdown');
                    $objValidation->setFormula1($locDataRange);
                }
                
                // User dropdown (column I)
                for ($i = 2; $i <= 20; $i++) {
                    $objValidation = $sheet->getCell('I' . $i)->getDataValidation();
                    $objValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $objValidation->setAllowBlank(true);
                    $objValidation->setShowInputMessage(true);
                    $objValidation->setShowErrorMessage(true);
                    $objValidation->setShowDropDown(true);
                    $objValidation->setErrorTitle('Invalid User');
                    $objValidation->setError('Please select a user from the dropdown list');
                    $objValidation->setPromptTitle('Select User');
                    $objValidation->setPrompt('Choose a user from the dropdown');
                    $objValidation->setFormula1($userDataRange);
                }
                
                // Add highlight to dropdown columns to make them more visible
                $sheet->getStyle('G1:I1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD6EAF8'); // Light blue background
                
                // Add note about dropdown fields
                $sheet->setCellValue('L1', 'NOTE: Columns G, H, and I have dropdown lists. Click a cell and use the dropdown arrow.');
                $sheet->getStyle('L1')->getFont()->setBold(true);
                $sheet->getStyle('L1')->getFont()->getColor()->setARGB('FF0000FF'); // Blue text
                $sheet->getColumnDimension('L')->setWidth(50);
                
                // Add metadata about custom fields at the bottom of the sheet
                $row = 5;
                $sheet->setCellValue('A' . $row, '--- Custom Fields Information (DO NOT MODIFY) ---');
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;
                
                $sheet->setCellValue('A' . $row, 'Column');
                $sheet->setCellValue('B' . $row, 'Field Name');
                $sheet->setCellValue('C' . $row, 'Required');
                $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
                $row++;
                
                $col = 'L';
                foreach ($allCustomFields as $field) {
                    $sheet->setCellValue('A' . $row, $col);
                    $sheet->setCellValue('B' . $row, $field->name);
                    $sheet->setCellValue('C' . $row, $field->is_required ? 'Yes' : 'No');
                    $row++;
                    $col = chr(ord($col) + 1);
                }
            }
            
            // Set the active sheet to the instructions
            $spreadsheet->setActiveSheetIndex(0);
            
            // Create filename
            $filename = 'inventory_import_template_' . date('Y-m-d') . '.xlsx';
            
            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            
            // Set additional options to prevent corruption
            $writer->setOffice2003Compatibility(false);
            $writer->setPreCalculateFormulas(false);
            
            $path = storage_path('app/public/excel_templates/' . $filename);
            
            // Make sure the directory exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            // Save the file
            $writer->save($path);
            
            return response()->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Excel template generation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating template: ' . $e->getMessage());
        }
    }
    
    /**
     * Process Excel import
     */
    public function import(Request $request)
    {
        // Validate request
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx',
        ]);
        
        try {
            $file = $request->file('import_file');
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($file->getPathname());
            
            $rowsProcessed = 0;
            $rowsFailed = 0;
            $errors = [];
            
            // Process each sheet (skip the first sheet which is Instructions)
            for ($sheetIndex = 1; $sheetIndex < $spreadsheet->getSheetCount(); $sheetIndex++) {
                $sheet = $spreadsheet->getSheet($sheetIndex);
                $highestRow = $sheet->getHighestRow();
                
                // Skip if only header row or empty
                if ($highestRow <= 1) {
                    continue;
                }
                
                // Get the category ID from the sheet (from cell K2)
                $categoryId = $sheet->getCell('K2')->getValue();
                if (!$categoryId) {
                    $errors[] = "Sheet '{$sheet->getTitle()}': Missing category ID. Skipping sheet.";
                    continue;
                }
                
                // Verify the category exists
                $category = Category::find($categoryId);
                if (!$category) {
                    $errors[] = "Sheet '{$sheet->getTitle()}': Category with ID {$categoryId} not found. Skipping sheet.";
                    continue;
                }
                
                // Find custom field mapping info at the bottom of the sheet
                $customFieldMap = [];
                // Start from row 6 where the custom fields info starts
                $row = 6;
                while ($row < $highestRow) {
                    if ($sheet->getCell('A' . $row)->getValue() == 'Column') {
                        $row++;
                        break;
                    }
                    $row++;
                }
                
                // Now read the custom field mappings
                while ($row <= $highestRow) {
                    $column = $sheet->getCell('A' . $row)->getValue();
                    $name = $sheet->getCell('B' . $row)->getValue();
                    $required = $sheet->getCell('C' . $row)->getValue() === 'Yes';
                    
                    if ($column && $name) {
                        $customFieldMap[$column] = [
                            'name' => $name,
                            'is_required' => $required
                        ];
                    } else {
                        break; // End of custom field mappings
                    }
                    $row++;
                }
                
                // Process data rows (skip header row and sample row)
                for ($row = 3; $row <= $highestRow; $row++) {
                    // Skip empty rows or rows with no item name
                    if (empty($sheet->getCell('A' . $row)->getValue())) {
                        continue;
                    }
                    
                    // Skip rows after the custom fields section
                    if ($sheet->getCell('A' . $row)->getValue() == '--- Custom Fields Information (DO NOT MODIFY) ---') {
                        break;
                    }
                    
                    // Extract standard fields
                    $itemData = [
                        'item_name' => $sheet->getCell('A' . $row)->getValue(),
                        'serial_no' => $sheet->getCell('B' . $row)->getValue(),
                        'model_no' => $sheet->getCell('C' . $row)->getValue(),
                        'manufacturer' => $sheet->getCell('D' . $row)->getValue(),
                        'date_purchased' => $sheet->getCell('E' . $row)->getValue(),
                        'purchased_from' => $sheet->getCell('F' . $row)->getValue(),
                        'department_id' => null,
                        'location_id' => null,
                        'users_id' => null,
                        'log_note' => $sheet->getCell('J' . $row)->getValue(),
                        'category_id' => $categoryId,
                        'asset_type_id' => AssetType::where('has_quantity', false)->first()->id, // Default to non-quantity asset type
                        'custom_fields' => [],
                        'asset_tag' => $this->generateAssetTag(),
                    ];
                    
                    // Process department
                    $departmentName = $sheet->getCell('G' . $row)->getValue();
                    if ($departmentName) {
                        $department = Department::where('name', $departmentName)->first();
                        if ($department) {
                            $itemData['department_id'] = $department->id;
                        }
                    }
                    
                    // Process location
                    $locationName = $sheet->getCell('H' . $row)->getValue();
                    if ($locationName) {
                        $location = Location::where('name', $locationName)->first();
                        if ($location) {
                            $itemData['location_id'] = $location->id;
                        }
                    }
                    
                    // Process user
                    $userName = $sheet->getCell('I' . $row)->getValue();
                    if ($userName) {
                        // Try to match the full name (first_name + last_name)
                        $nameParts = explode(' ', $userName);
                        if (count($nameParts) > 1) {
                            $lastName = array_pop($nameParts);
                            $firstName = implode(' ', $nameParts);
                            
                            $user = User::where(DB::raw("first_name || ' ' || last_name"), $userName)
                                ->orWhere(function($query) use ($firstName, $lastName) {
                                    $query->where('first_name', 'like', $firstName.'%')
                                          ->where('last_name', 'like', $lastName.'%');
                                })
                                ->first();
                            
                            if ($user) {
                                $itemData['users_id'] = $user->id;
                            }
                        }
                    }
                    
                    // Process custom fields
                    foreach ($customFieldMap as $col => $fieldInfo) {
                        $value = $sheet->getCell($col . $row)->getValue();
                        if ($value !== null && $value !== '') {
                            $itemData['custom_fields'][$fieldInfo['name']] = $value;
                        }
                    }
                    
                    // Validate the data
                    $rules = [
                        'item_name' => 'required|string',
                        'serial_no' => 'required|unique:inventories,serial_no',
                        'model_no' => 'required|string',
                        'manufacturer' => 'required|string',
                        'date_purchased' => 'required|date',
                        'purchased_from' => 'required|string',
                        'category_id' => 'required|exists:categories,id',
                        'asset_type_id' => 'required|exists:asset_types,id',
                    ];
                    
                    // Add custom field validation rules
                    foreach ($customFieldMap as $col => $fieldInfo) {
                        if ($fieldInfo['is_required']) {
                            $rules["custom_fields.{$fieldInfo['name']}"] = 'required';
                        }
                    }
                    
                    $validator = Validator::make($itemData, $rules);
                    
                    if ($validator->fails()) {
                        $rowsFailed++;
                        $errors[] = "Sheet '{$sheet->getTitle()}', Row {$row}: " . implode(', ', $validator->errors()->all());
                        continue;
                    }
                    
                    // Create the inventory item
                    try {
                        Inventory::create($itemData);
                        $rowsProcessed++;
                        
                        // Log the activity
                        ActivityLogger::logCreated('Inventory Item (Import)', $itemData['item_name']);
                    } catch (\Exception $e) {
                        $rowsFailed++;
                        $errors[] = "Sheet '{$sheet->getTitle()}', Row {$row}: " . $e->getMessage();
                    }
                }
            }
            
            // Prepare response message
            $message = "Import completed. {$rowsProcessed} items imported successfully.";
            if ($rowsFailed > 0) {
                $message .= " {$rowsFailed} items failed to import.";
            }
            
            return redirect()->route('inventory.index')->with([
                'success' => $message,
                'import_errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            Log::error('Excel import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error processing import: ' . $e->getMessage());
        }
    }
    
    /**
     * Export all inventory items to Excel
     */
    public function export()
    {
        try {
            // Get all categories
            $categories = Category::where('type', 'Asset')->get();
            
            if ($categories->isEmpty()) {
                return redirect()->back()->with('error', 'No categories found. Please create at least one category first.');
            }
            
            // Create new spreadsheet
            $spreadsheet = new Spreadsheet();
            
            // Remove the default sheet
            $spreadsheet->removeSheetByIndex(0);
            
            foreach ($categories as $category) {
                // Get inventory items for this category
                $items = Inventory::where('category_id', $category->id)
                    ->with(['category', 'department', 'user', 'location'])
                    ->get();
                
                // Skip if no items
                if ($items->isEmpty()) {
                    continue;
                }
                
                // Create sheet for this category
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle(substr($category->category, 0, 31)); // Excel has a 31 char limit for sheet names
                
                // Get custom fields for this category
                $customFieldIds = json_decode($category->custom_fields, true) ?? [];
                $categoryCustomFields = CustomField::whereIn('id', $customFieldIds)->get();
                
                // Get general asset custom fields
                $generalCustomFields = CustomField::whereJsonContains('applies_to', 'Asset')
                    ->whereNotIn('id', $customFieldIds)
                    ->get();
                
                // Combine both sets of custom fields
                $allCustomFields = $categoryCustomFields->merge($generalCustomFields);
                
                // Set column headers
                $columns = [
                    'A' => 'Item Name',
                    'B' => 'Serial Number',
                    'C' => 'Model Number',
                    'D' => 'Manufacturer',
                    'E' => 'Date Purchased',
                    'F' => 'Purchased From',
                    'G' => 'Department',
                    'H' => 'Location',
                    'I' => 'Assigned User',
                    'J' => 'Notes'
                ];
                
                // Add custom field headers
                $col = 'K';
                foreach ($allCustomFields as $field) {
                    $columns[$col] = $field->name;
                    $col = chr(ord($col) + 1);
                }
                
                // Set headers
                foreach ($columns as $col => $header) {
                    $sheet->setCellValue($col . '1', $header);
                    $sheet->getStyle($col . '1')->getFont()->setBold(true);
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                
                // Fill data rows
                $row = 2;
                foreach ($items as $item) {
                    $sheet->setCellValue('A' . $row, $item->item_name);
                    $sheet->setCellValue('B' . $row, $item->serial_no);
                    $sheet->setCellValue('C' . $row, $item->model_no);
                    $sheet->setCellValue('D' . $row, $item->manufacturer);
                    $sheet->setCellValue('E' . $row, $item->date_purchased);
                    $sheet->setCellValue('F' . $row, $item->purchased_from);
                    $sheet->setCellValue('G' . $row, $item->department ? $item->department->name : '');
                    $sheet->setCellValue('H' . $row, $item->location ? $item->location->name : '');
                    $sheet->setCellValue('I' . $row, $item->user ? $item->user->first_name . ' ' . $item->user->last_name : '');
                    $sheet->setCellValue('J' . $row, $item->log_note);
                    
                    // Fill custom fields
                    $col = 'K';
                    foreach ($allCustomFields as $field) {
                        $fieldName = $field->name;
                        $value = '';
                        
                        if (isset($item->custom_fields[$fieldName])) {
                            $value = $item->custom_fields[$fieldName];
                            
                            // Handle array values (like checkboxes)
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }
                        }
                        
                        $sheet->setCellValue($col . $row, $value);
                        $col = chr(ord($col) + 1);
                    }
                    
                    $row++;
                }
            }
            
            // Create filename
            $filename = 'inventory_export_' . date('Y-m-d') . '.xlsx';
            
            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $path = storage_path('app/public/excel_exports/' . $filename);
            
            // Make sure the directory exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            // Save the file
            $writer->save($path);
            
            // Log the activity
            ActivityLogger::logGeneric('Exported all inventory items');
            
            return response()->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Excel export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error processing export: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate a new asset tag
     */
    private function generateAssetTag()
    {
        // Get the last used asset tag
        $lastAsset = Inventory::orderBy('id', 'desc')->first();
        $lastTag = $lastAsset ? $lastAsset->asset_tag : null;
        
        // Extract the numeric part if it exists
        $numericPart = 0;
        if ($lastTag && preg_match('/AST-(\d+)/', $lastTag, $matches)) {
            $numericPart = (int)$matches[1];
        }
        
        // Increment the numeric part
        $numericPart++;
        
        // Format the new asset tag
        $newTag = 'AST-' . str_pad($numericPart, 6, '0', STR_PAD_LEFT);
        
        return $newTag;
    }
} 