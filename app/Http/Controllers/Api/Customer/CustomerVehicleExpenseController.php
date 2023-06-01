<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleExpense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CustomerVehicleExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/customer/vehicle-expenses",
     *     tags={"vehicle-expenses"},
     *     summary="Returns Customer All Vehicle Expenses",
     *     description="Returns Customer All Vehicle Expenses",
     *     operationId="vehicle-expenses",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             @OA\AdditionalProperties(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         )
     *     ),
     *     security={
     *         {"Bearer token": {}}
     *     }
     * )
     */
    public function index(): Response
    {
        $data = VehicleExpense::with('vehicle')->where('customer_id', Auth::user()->id)->orderBy('id', 'DESC')->get();

        return Response([
            'status'    => true,
            'message'   => 'Customer All Vehicle Expenses',
            'data'      => $data
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/customer/vehicle-expenses",
     *     summary="Store Vehicle Expense",
     *     tags={"vehicle-expenses-store"},
     *     description="Store Vehicle Expenses.",
     *     operationId="vehicle-expenses-store",
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         description="Vehicle Id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="path",
     *         description="Amount",
     *         required=true,
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="time",
     *         in="path",
     *         description="Time",
     *         required=false,
     *         @OA\Schema(
     *             type="time"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="created_by",
     *         in="path",
     *         description="Person Id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="purpose",
     *         in="path",
     *         description="Purpose",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid user supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function store(Request $request): Response
    {
        $validator = validator(
            $request->all(),
            [
                'vehicle_id'    => 'required|exists:vehicles,id',
                'amount'        => 'required|numeric|gt:0',
                'time'          => 'nullable|date_format:H:i',
                'created_by'    => 'nullable|exists:users,id',
            ],
            [
                'vehicle_id.required'       => 'Vehicle is Required',
                'vehicle_id.exists'         => 'Provide Valid Vehicle Info',
                'amount.required'           => 'Expense Amount is Required',
                'amount.numeric'            => 'Provide Valid Expense Amount',
                'amount.gt'                 => 'Provide Valid Expense Amount',
                'time.date_format'          => 'Time Format must be HH:MM',
                'created_by.exists'         => 'Provide Valid Person'
            ]
        );

        if ($request->filled('created_by')) :
            $validator->after(function ($validator) use ($request) {
                $allDivers = User::where('role_id', 5)->where('created_by', Auth::user()->id)->pluck('id')->toArray();
                array_push($allDivers, Auth::user()->id);
                if (!in_array($request->created_by, $allDivers)) {
                    $validator->errors()->add('created_by', 'Provide Valid Person');
                }
            });
        endif;


        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $customerId = Auth::user()->id;

            $newExpense = new VehicleExpense;
            $newExpense->customer_id    = $customerId;
            $newExpense->vehicle_id     = $request->vehicle_id;
            $newExpense->amount         = $request->amount;
            $newExpense->purpose        = $request->purpose;
            $newExpense->time           = $request->time;
            if ($request->filled('created_by')) :
                $newExpense->created_by = $request->created_by;
            endif;
            $newExpense->save();
            $newExpense->load('createdBy');

            return Response([
                'status'    => true,
                'message'   => 'Expenses Created Successfully',
                'data'      => $newExpense
            ], Response::HTTP_CREATED);

        endif;
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/customer/vehicle-expenses/id",
     *     tags={"vehicle-expenses-single"},
     *     summary="Get Perticual Expesse",
     *     description="",
     *     operationId="vehicle-expenses-single",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             @OA\AdditionalProperties(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         )
     *     ),
     *     security={
     *         {"Bearer token": {}}
     *     }
     * )
     */
    public function show(string $id): Response
    {
        $data = VehicleExpense::where('customer_id', Auth::user()->id)->findOrFail($id);

        return Response([
            'status'    => true,
            'message'   => 'Customer All Vehicle Expenses',
            'data'      => $data
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/customer/vehicle-expenses/id",
     *     summary="Store Vehicle Expense",
     *     tags={"vehicle-expenses-update"},
     *     description="Store Vehicle Expenses.",
     *     operationId="vehicle-expenses-update",
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         description="Vehicle Id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="path",
     *         description="Amount",
     *         required=true,
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="time",
     *         in="path",
     *         description="Time",
     *         required=false,
     *         @OA\Schema(
     *             type="time"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="created_by",
     *         in="path",
     *         description="Person Id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="purpose",
     *         in="path",
     *         description="Purpose",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid user supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function update(Request $request, string $id): Response
    {
        $validator = validator(
            $request->all(),
            [
                'vehicle_id'    => 'required|exists:vehicles,id',
                'amount'        => 'required|numeric|gt:0',
                'time'          => 'nullable|date_format:H:i',
                'created_by'    => 'nullable|exists:users,id',
            ],
            [
                'vehicle_id.required'       => 'Vehicle is Required',
                'vehicle_id.exists'         => 'Provide Valid Vehicle Info',
                'amount.required'           => 'Expense Amount is Required',
                'amount.numeric'            => 'Provide Valid Expense Amount',
                'amount.gt'                 => 'Provide Valid Expense Amount',
                'time.date_format'          => 'Time Format must be HH:MM',
                'created_by.exists'         => 'Provide Valid Person'
            ]
        );

        if ($request->filled('created_by')) :
            $validator->after(function ($validator) use ($request) {
                $allDivers = User::where('role_id', 5)->where('created_by', Auth::user()->id)->pluck('id')->toArray();
                array_push($allDivers, Auth::user()->id);
                if (!in_array($request->created_by, $allDivers)) {
                    $validator->errors()->add('created_by', 'Provide Valid Person');
                }
            });
        endif;

        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $data = VehicleExpense::where('customer_id', Auth::user()->id)->findOrFail($id);
            $data->vehicle_id     = $request->vehicle_id;
            $data->amount         = $request->amount;
            $data->purpose        = $request->purpose;
            $data->time           = $request->time;
            if ($request->filled('created_by')) :
                $data->created_by = $request->created_by;
            endif;
            $data->save();
            $data->load('createdBy');

            return Response([
                'status'    => true,
                'message'   => 'Expense Update Successfully',
                'data'      => $data
            ], Response::HTTP_CREATED);
        endif;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        //
    }
}
