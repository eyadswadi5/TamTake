<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::all();

        return response()->json($addresses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string|exists:users,id',
            'street_address' => 'required|string|max:255',
            'apartment_number' => 'nullable|string|max:50',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_primary' => 'boolean'
        ]);

        if ($validator->fails())
            return response()->json(["errors" => $validator->errors()], 422);


        $data = $request->only('customer_id','street_address','apartment_number','country','city','region','zip_code','latitude','longitude','is_primary');

        try {            
            $address = Address::create($data);
            return response()->json([$address, "message" => "address assigned successfully" ], 200);
        } catch (QueryException $e) {
            return response()->json(["error" => "Failed to create address: database error happend"], 500);
        } catch (Exception $e) {
            return response()->json(["error" => "Failed to create address: unknown error happend"], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $address = Address::findOrFail($id);
            return response()->json(["address" => $address], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "can't find address"]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $address = Address::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "can't find address"]);
        }

        $validator = Validator::make($request->all(), [
            'street_address' => 'required|string|max:255',
            'apartment_number' => 'nullable|string|max:50',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_primary' => 'boolean'
        ]);
        if ($validator->fails())
            return response()->json(["errors" => $validator->errors()], 422);

        $data = $request->only('street_address','apartment_number','country','city','region','zip_code','latitude','longitude','is_primary');

        try {
            $address->update($data);
            return response()->json(["message" => "address updated successfully"], 200);
        } catch (QueryException $e) {
            return response()->json(["error" => "Failed to update address: database error happend"], 500);
        } catch (Exception $e) {
            return response()->json(["error" => "Failed to update address: unknown error happend"], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $address = Address::findOrFail($id);
            $address->delete();
            return response()->json(["message" => "address deleted successfully"], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "Faild to delete address: address not found"]);
        } catch (Exception $e) {
            return response()->json(["error" => "Faild to delete address: unknown error happend"]);
        }
    }
}
