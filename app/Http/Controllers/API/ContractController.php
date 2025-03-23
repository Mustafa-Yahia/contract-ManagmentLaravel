<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contract;
use Illuminate\Support\Facades\Validator;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = Contract::with(['user', 'device'])->get();  
        $data = [
            'status' => 200,
            'contracts' => $contracts
        ];
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => 'required|exists:users,id',  
    //         'device_id' => 'required|exists:devices,id',  
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after:start_date',  
    //         'approved_by' => 'required|string|max:255',
    //         'status' => 'required|in:active,completed,pending,canceled',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 422,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $contract = new Contract();
    //     $contract->user_id = $request->user_id;
    //     $contract->device_id = $request->device_id;
    //     $contract->start_date = $request->start_date;
    //     $contract->end_date = $request->end_date;
    //     $contract->approved_by = $request->approved_by;
    //     $contract->status = $request->status;
        
    //     $contract->save();

    //     return response()->json([
    //         'status' => 201,
    //         'message' => 'Contract created successfully',
    //         'contract' => $contract
    //     ], 201);
    // }

    public function store(Request $request)
    {
        \Log::info('Request Data:', $request->all());
    
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',  
            'device_id' => 'required|exists:devices,id',  
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',  
            'status' => 'required|in:active,completed,pending,canceled',
        ]);
    
        if ($validator->fails()) {
            \Log::error('Validation Errors:', $validator->errors()->toArray());
    
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }
    
        \Log::info('Validated Data:', [
            'user_id' => $request->user_id,
            'device_id' => $request->device_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);
    
        $contract = new Contract();
        $contract->user_id = $request->user_id;
        $contract->device_id = $request->device_id;
        $contract->start_date = $request->start_date;
        $contract->end_date = $request->end_date;
        $contract->status = $request->status;
        $contract->save();
    
        \Log::info('Contract Created:', $contract->toArray());
    
        return response()->json([
            'status' => 201,
            'message' => 'Contract created successfully',
            'contract' => [
                'id' => $contract->id,
                'user_id' => $contract->user_id,
                'device_id' => $contract->device_id,
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                'status' => $contract->status,
            ]
        ], 201);
        
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contract = Contract::with(['user', 'device'])->find($id); 
        if (!$contract) {
            return response()->json([
                'status' => 404,
                'message' => "Contract not found"
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'contract' => $contract
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contract = Contract::find($id);
        if (!$contract) {
            return response()->json([
                'status' => 404,
                'message' => 'Contract not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'device_id' => 'sometimes|required|exists:devices,id',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'approved_by' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:active,completed,pending,canceled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        // $contract->user_id = $request->input('user_id', $contract->user_id);
        // $contract->device_id = $request->input('device_id', $contract->device_id);
        // $contract->start_date = $request->input('start_date', $contract->start_date);
        // $contract->end_date = $request->input('end_date', $contract->end_date);
        // $contract->approved_by = $request->input('approved_by', $contract->approved_by);
        $contract->status = $request->input('status', $contract->status);


        $contract->save();

        return response()->json([
            'status' => 200,
            'message' => 'Contract updated successfully',
            'contract' => $contract
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contract = Contract::find($id);
        if (!$contract) {
            return response()->json([
                'status' => 404,
                'message' => 'Contract not found'
            ], 404);
        }

        $contract->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Contract deleted successfully'
        ], 200);
    }
}