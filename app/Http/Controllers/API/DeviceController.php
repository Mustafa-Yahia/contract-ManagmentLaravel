<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devices = Device::with('category')->get(); 
        $data = [
            'status' => 200,
            'devices' => $devices
        ];
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',  
            'description' => 'required|string',
            'price' => 'required|numeric',
            'availability_status' => 'required|in:available,out_of_stock',
            'img' => 'nullable|string', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a new device
        $device = new Device();
        $device->name = $request->name;
        $device->category_id = $request->category_id;
        $device->description = $request->description;
        $device->price = $request->price;
        $device->availability_status = $request->availability_status;
        $device->img = $request->img;
        
        $device->save();

        return response()->json([
            'status' => 201,
            'message' => 'Device created successfully',
            'device' => $device
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $device = Device::with('category')->find($id);  
        if (!$device) {
            return response()->json([
                'status' => 404,
                'message' => "Device not found"
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'device' => $device
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json([
                'status' => 404,
                'message' => 'Device not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',  
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'availability_status' => 'sometimes|required|in:available,out_of_stock',
            'img' => 'nullable|string',  
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $device->name = $request->input('name', $device->name);
        $device->category_id = $request->input('category_id', $device->category_id);
        $device->description = $request->input('description', $device->description);
        $device->price = $request->input('price', $device->price);
        $device->availability_status = $request->input('availability_status', $device->availability_status);
        $device->img = $request->input('img', $device->img);

        $device->save();

        return response()->json([
            'status' => 200,
            'message' => 'Device updated successfully',
            'device' => $device
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json([
                'status' => 404,
                'message' => 'Device not found'
            ], 404);
        }

        $device->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Device deleted successfully'
        ], 200);
    }
}
