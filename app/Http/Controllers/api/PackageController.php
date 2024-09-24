<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Shipment;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::all();
        return response()->json($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipment_id' => 'required|string|max:255|exists:shipments,id',
            'weight' => 'required|numeric|min:0',
            'dimensions' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'package_type' => 'required|string|max:255',
            'is_fragile' => 'required|boolean',
            'hazardous_materials' => 'required|boolean',
            'barcode' => 'nullable|string|max:255',
            'special_instruction' => 'nullable|string|max:1000'
        ]);
        if ($validator->fails())
            return response()->json(["errors" => $validator->errors()]);

        if (!Shipment::find($request->shipment_id))
            return response()->json(["error" => "Failed to create package: shipment not found"]);
        
        $data = $request->only('shipment_id','weight','dimensions','value','package_type','is_fragile','hazardous_materials','barcode','special_instruction');
        
        try {
            $package = Package::create($data);
            return response()->json(["message" => "package created successfully"], 201);
        } catch (QueryException $e) {
            return response()->json(["error" => "Failed to create package: database error happend"], 500);
        } catch (Exception $e) {
            return response()->json(["error" => "Failed to create package: unknown error happend"], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $package = Package::findOrFail($id);
            return response()->json(["package" => $package], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "can't find package"], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $package = Package::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "can't find package"], 404);
        }

        $validator = Validator::make($request->all(), [
            'weight' => 'required|numeric|min:0',
            'dimensions' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'package_type' => 'required|string|max:255',
            'is_fragile' => 'required|boolean',
            'hazardous_materials' => 'required|boolean',
            'barcode' => 'required|string|max:255',
            'special_instruction' => 'nullable|string|max:1000'
        ]);
        if ($validator->fails())
            return response()->json(["errors" => $validator->errors()]);
        
        $data = $request->only('weight','dimensions','value','package_type','is_fragile','hazardous_materials','barcode','special_instruction');
    
        try {
            $package->update($data);
            return response()->json(["message" => "package updated successfully"], 201);
        } catch (QueryException $e) {
            return response()->json(["error" => "Failed to update package: database error happend"], 500);
        } catch (Exception $e) {
            return response()->json(["error" => "Failed to update package: unknown error happend"], 500);
        }
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $package = Package::findOrFail($id);
            $package->delete();
            return response()->json(["message" => "deleted successfully"], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "deleting package failed: can't find package"], 404);
        } catch (Exception $e) {
            return response()->json(["error" => "deleting package failed: unknown error"]);
        }
    }
}
