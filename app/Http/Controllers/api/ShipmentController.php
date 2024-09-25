<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShipmentResource;
use App\Models\Address;
use App\Models\Destination;
use App\Models\Package;
use App\Models\Shipment;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;


class ShipmentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shipments = Shipment::all();
        return response()->json($this->responseTemplate(true, null, null, ["shipments" => $shipments]), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origin_address.street_address' => 'required|string|max:255',
            'origin_address.apartment_number' => 'nullable|string|max:50',
            'origin_address.country' => 'required|string|max:100',
            'origin_address.city' => 'required|string|max:100',
            'origin_address.region' => 'required|string|max:100',
            'origin_address.zip_code' => 'required|string|max:20',
            'origin_address.longitude' => 'nullable|numeric',
            'origin_address.latitude' => 'nullable|numeric',

            'destination_address.street_address' => 'required|string|max:255',
            'destination_address.apartment_number' => 'nullable|string|max:50',
            'destination_address.country' => 'required|string|max:100',
            'destination_address.city' => 'required|string|max:100',
            'destination_address.region' => 'required|string|max:100',
            'destination_address.zip_code' => 'required|string|max:20',
            'destination_address.longitude' => 'nullable|numeric',
            'destination_address.latitude' => 'nullable|numeric',

            'packages' => 'required|array|min:1',
            'packages.*.weight' => 'required|string',
            'packages.*.dimensions' => 'required|string',
            'packages.*.value' => 'required|numeric',
            'packages.*.package_type' => 'required|string',
            'packages.*.is_fragile' => 'required|boolean',
            'packages.*.hazardous_materials' => 'required|boolean',
            'packages.*.barcode' => 'nullable|string',
            'packages.*.special_instruction' => 'nullable|string',

            'shipment_date' => 'required|date',
            'delivery_date' => 'required|date',
            'shipping_method' => 'required|string',
            'total_weight' => 'required|numeric',
            'total_value' => 'required|numeric',
            'insurance' => 'required|boolean',
            'special_instructions' => 'nullable|string'
        ]);

        if ($validator->fails())
            return response()->json($this->responseTemplate(false, "Credential validation error", $validator->errors()), 422);

        $customerId = JWTAuth::user()->id;

        $originAddressData = $request->only('origin_address.street_address','origin_address.apartment_number','origin_address.country','origin_address.city','origin_address.region','origin_address.zip_code','origin_address.longitude','origin_address.latitude');
        $originAddressData["origin_address"] += array("customer_id" => $customerId) ;
        $destAddressData = $request->only('destination_address.street_address','destination_address.apartment_number','destination_address.country','destination_address.city','destination_address.region','destination_address.zip_code','destination_address.longitude','destination_address.latitude');
        $packagesData = $request->only('packages');
        
        try {
            $originAddress = Address::create($originAddressData["origin_address"]);
            $destAddress = Destination::create($destAddressData["destination_address"]);
            
            $shipmentData = $request->only('shipment_date','delivery_date','shipping_method','tracking_number','total_weight','total_value','insurance','courier_id','special_instructions');
            $shipmentData += array("origin_address_id" => $originAddress->id,"destination_address_id" => $destAddress->id, "customer_id" => $customerId, "tracking_number" => Shipment::generateUniqueTrackingNumber());
            $shipment = Shipment::create($shipmentData);

            $packagesRecords = collect($packagesData["packages"])->map(function($package) use ($shipment) {
                return [
                    "id" => Str::uuid()->toString(),
                    'shipment_id' => $shipment->id,
                    "weight" => $package['weight'],
                    "dimensions" => $package['dimensions'],
                    "value" => $package['value'],
                    "package_type" => $package['package_type'],
                    "is_fragile" => $package['is_fragile'],
                    "hazardous_materials" => $package['hazardous_materials'],
                    "barcode" => $package['barcode'],
                    "special_instruction" => $package['special_instruction']
                ];
            });
            
            $packages = Package::insert($packagesRecords->toArray());
    
            return response()->json($this->responseTemplate(true, "shipment created successfully"), 201);

        } catch (QueryException $e) {
            return response()->json($this->responseTemplate(false, "Failed to create shipment", [["message"=>"A database error occurred."]]), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $shipment = Shipment::with(['originAddress', 'destinationAddress', 'packages'])->findOrFail($id);
            return response()->json([$this->responseTemplate(true), "shipment" => new ShipmentResource($shipment)] );
        } catch (QueryException $e) {
            return response()->json($this->responseTemplate(false, "Failed to get shipment", [["message"=>"shipment not found"]]), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
        
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
