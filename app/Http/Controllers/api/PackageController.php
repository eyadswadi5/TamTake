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

class PackageController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::all();
        return response()->json($this->responseTamplate(true, null, null, ["packages" => $packages]), 200);
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
            return response()->json($this->responseTamplate(false, "Credential validation error", $validator->errors()));

        if (!Shipment::find($request->shipment_id))
            return response()->json($this->responseTamplate(false, "Failed to create package", [["message" => "shipment not found"]]));
        
        $data = $request->only('shipment_id','weight','dimensions','value','package_type','is_fragile','hazardous_materials','barcode','special_instruction');
        
        try {
            $package = Package::create($data);
            return response()->json($this->responseTamplate(true, "package created successfully"), 201);
        } catch (QueryException $e) {
            return response()->json($this->responseTamplate(false, "Failed to create package", [["message"=>"A database error occurred."]]), 500);
        } catch (Exception $e) {
            return response()->json($this->responseTamplate(false, "Failed to create package", [["message"=>"An unknown error occurred."]]), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $package = Package::findOrFail($id);
            return response()->json($this->responseTamplate(true, null,null, ["package" => $package]), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json($this->responseTamplate(false, "Failed to get package", [["message"=>"package not found"]]), 404);
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
            return response()->json($this->responseTamplate(false, "Failed to create package", [["message"=>"package not found"]]), 404);
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
            return response()->json($this->responseTamplate(false, "Credential validation error", $validator->errors()));
        
        $data = $request->only('weight','dimensions','value','package_type','is_fragile','hazardous_materials','barcode','special_instruction');
    
        try {
            $package->update($data);
            return response()->json($this->responseTamplate(true, "package updated successfully"), 201);
        } catch (QueryException $e) {
            return response()->json($this->responseTamplate(false, "Failed to update package", [["message"=>"A database error occurred."]]), 500);
        } catch (Exception $e) {
            return response()->json($this->responseTamplate(false, "Failed to update package", [["message"=>"A unknown error occurred."]]), 500);
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
            return response()->json($this->responseTamplate(true, "package deleted successfully"), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json($this->responseTamplate(false, "Failed to delete package", [["message"=>"package not found"]]), 404);
        } catch (Exception $e) {
            return response()->json($this->responseTamplate(false, "Failed to delete package", [["message"=>"A unknown error occurred."]]), 500);
        }
    }
}
