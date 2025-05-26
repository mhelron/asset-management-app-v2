<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use Illuminate\Http\Request;

class AssetTypeController extends Controller
{
    public function index()
    {
        $assetTypes = AssetType::whereNull('deleted_at')->get();
        return view('asset-types.index', compact('assetTypes'));
    }

    public function create()
    {
        return view('asset-types.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:asset_types',
            'desc' => 'required|string',
        ]);

        AssetType::create([
            'name' => $validatedData['name'],
            'desc' => $validatedData['desc'],
            'status' => 'Active',
        ]);

        return redirect('asset-types')->with('success', 'Asset Type Added Successfully');
    }

    public function edit($id)
    {
        $assetType = AssetType::findOrFail($id);
        return view('asset-types.edit', compact('assetType'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:asset_types,name,'.$id,
            'desc' => 'required|string',
            'status' => 'required|string',
        ]);

        $assetType = AssetType::findOrFail($id);
        $assetType->update($validatedData);

        return redirect('asset-types')->with('success', 'Asset Type Updated Successfully');
    }

    public function archive($id)
    {
        $assetType = AssetType::findOrFail($id);
        $assetType->delete(); // Soft delete (archives the asset type)

        return redirect('asset-types')->with('success', 'Asset Type Archived Successfully');
    }
}
