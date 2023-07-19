<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Report\LiveTrackingResource;
use App\Models\DeviceData;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CustomerVehicleReportController extends Controller
{
    private $time_span = [
        '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'
    ];
    /**
     * @OA\Get(
     *     path="/api/customer/live-tracking",
     *     tags={"live-tracking"},
     *     summary="Returns all vehicle live tracking Info",
     *     description="",
     *     operationId="live-tracking",
     *      @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="shortList",
     *         required=false,
     *      ),
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
    public function liveTracking()
    {
        $data = Vehicle::with([
            'vehicleType',
            'driverInfo.userDetails',
            'vehicleLatestData'
        ])->where('customer_id', Auth::user()->id)->get();

        return Response([
            'status'    => true,
            'message'   => 'Vehicle Life Info',
            'data'      => LiveTrackingResource::collection($data)
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/daily-route",
     *     summary="Vehicle Daily Route",
     *     tags={"daily-route"},
     *     description="Show Daily Vehicle Route.",
     *     operationId="daily-route",
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
     *         name="date",
     *         in="path",
     *         description="Report Date",
     *         required=true,
     *         @OA\Schema(
     *             type="date"
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
    public function dailyRoute(Request $request): Response
    {
        $validator = validator(
            $request->all(),
            [
                'vehicle_id'    => 'required|exists:vehicles,id',
                'date'          => 'required|date_format:Y-m-d'
            ],
            [
                'vehicle_id.required'   => 'Vehicle is Required',
                'vehicle_id.exists'     => 'Provide Valid Vehicle Info',
                'date.required'         => 'Report Date is Required',
                'date.date_format'      => 'Provide Valid Date Format:YYYY-MM-DD'
            ]
        );

        $vehicles = Vehicle::where('customer_id', Auth::user()->id)->pluck('id')->toArray();
        $validator->after(function ($validator) use ($vehicles, $request) {
            if (!in_array($request->vehicle_id, $vehicles)) :
                $validator->errors()->add('vehicle_id', 'Provide Valid Vehicle Info');
            endif;
        });

        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $data = DeviceData::select('latitude', 'longitude', 'engine_status', 'rotation')->where('vehicle_id', $request->vehicle_id)->whereDate('created_at', $request->date)->get();

            return Response([
                'status'    => true,
                'message'   => 'Vehicle Daily Route',
                'data'      => $data
            ], Response::HTTP_OK);
        endif;
    }

    /**
     * @OA\Post(
     *     path="/api/customer/daily-report",
     *     summary="Vehicle Daily Report",
     *     tags={"daily-report"},
     *     description="Show Daily Vehicle Report.",
     *     operationId="daily-report",
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
     *         name="date",
     *         in="path",
     *         description="Report Date",
     *         required=true,
     *         @OA\Schema(
     *             type="date"
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
    public function dailyReport(Request $request): Response
    {
        $validator = validator(
            $request->all(),
            [
                'vehicle_id'    => 'required|exists:vehicles,id',
                'date'          => 'required|date_format:Y-m-d'
            ],
            [
                'vehicle_id.required'   => 'Vehicle is Required',
                'vehicle_id.exists'     => 'Provide Valid Vehicle Info',
                'date.required'         => 'Report Date is Required',
                'date.date_format'      => 'Provide Valid Date Format:YYYY-MM-DD'
            ]
        );

        $vehicles = Vehicle::where('customer_id', Auth::user()->id)->pluck('id')->toArray();
        $validator->after(function ($validator) use ($vehicles, $request) {
            if (!in_array($request->vehicle_id, $vehicles)) :
                $validator->errors()->add('vehicle_id', 'Provide Valid Vehicle Info');
            endif;
        });

        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $dailyData = DeviceData::select('latitude', 'longitude', 'engine_status', 'speed', 'distance', 'fuel_use', 'created_at')->where('vehicle_id', $request->vehicle_id)
                ->whereDate('created_at', $request->date)->get()->groupBy(function ($items) {
                    return Carbon::parse($items->created_at)->format('H');
                });
            $result = [];
            $total_distance = 0.000;
            $total_fuel_use = 0.000;
            foreach ($this->time_span as $span) :
                $data = $dailyData->get($span);
                $hourly_distance = 0.000;
                $hourly_fuel_use = 0.000;
                if (!is_null($data) > 0) :
                    foreach ($data as $key => $value) :
                        $hourly_distance += $value->distance;
                        $hourly_fuel_use += $value->fuel_use;
                    endforeach;

                    $hourly_distance = (float) number_format($hourly_distance, 3, '.');
                    $hourly_fuel_use = (float) number_format($hourly_fuel_use, 3, '.');
                    $total_distance += $hourly_distance;
                    $total_fuel_use += $hourly_fuel_use;

                    array_push($result, array(
                        'hourly_data'       => $data,
                        'hourly_distance'   => $hourly_distance,
                        'hourly_fuel_use'   => $hourly_fuel_use
                    ));
                else :
                    array_push($result, array(
                        'hourly_data'       => null,
                        'hourly_distance'   => $hourly_distance,
                        'hourly_fuel_use'   => $hourly_fuel_use,
                    ));
                endif;
            endforeach;

            return Response([
                'status'    => true,
                'message'   => 'Vehicle Daily Hourly Report',
                'data'      => array(
                    'time_span'         => $result,
                    'total_distance'    => (string) $total_distance,
                    'total_fuel_use'    => (string) $total_fuel_use
                )
            ], Response::HTTP_OK);

        // dd(number_format($dailyData->sum('distance'), 3, '.'));
        // dd($dailyData->keys()->toArray());
        // $slot_differece = array_diff($this->time_span, $dailyData->keys()->toArray());
        // array_merge($dailyData->keys());
        // dd($dailyData->keys()->toArray(), $slot_differece);


        // dd($)

        // $dailyData->map(function ($dailyData, $key) use ($total_distance, $total_fuel_use) {
        //     // dd($this->time_span[0]);
        //     // if ($key == $this->time_span[0]) :
        //     // dd('here');
        //     $distance = (float) number_format($dailyData->sum('distance'), 3, '.');
        //     $total_distance += $distance;
        //     $fuel_use = (float) number_format($dailyData->sum('fuel_use'), 3, '.');
        //     $total_fuel_use += $fuel_use;

        //     $dailyData['hour_distance'] =  $distance;
        //     $dailyData['hour_fuel_use'] = $fuel_use;

        //     // else :
        //     // dd($this->time_span[0]);
        //     // $emptyData = array('name' => 'asbd');
        //     // $dailyData->put($this->time_span[0], $emptyData);
        //     // dd($dailyData);
        //     // return $dailyData;

        //     // endif;
        //     // unset($this->time_span[0]);
        //     // $this->time_span = array_values($this->time_span);
        //     // dd($dailyData);
        //     return $dailyData;
        //     // array_merge($this->time_span);
        //     // dd($this->time_span);
        //     // dd($total_distance, $distance);

        //     // $distance = $data->sum('distance');
        //     // $fuel_use = $data->sum('fuel_use');
        //     // dd($distance, $fuel_use);
        // });

        // $slot_differece = array_diff($this->time_span, $dailyData->keys()->toArray());
        // if (count($slot_differece) > 0) :
        //     $emptyData = array();
        //     foreach ($slot_differece as $dif) :
        //         $dailyData->put($dif, $emptyData);
        //     endforeach;
        // // array_merge($dailyData->keys()->toArray());
        // // dd($dailyData->keys()->toArray());
        // // $dailyData->merge($dailyData->keys());
        // // array_merge($dailyData->keys()->toArray());
        // endif;
        // dd(array_merge($dailyData->toArray()));
        // $rrr = $dailyData->sort();


        // dd($dailyData->keys(), $this->time_span, $dif);
        // dd($dailyData);
        // $dailyData['total_distance'] = $dailyData->sum( function($dailyData ) {
        //     return $distance;
        // }'distance');
        // $dailyData['total_fuel_use'] = $total_fuel_use;

        // if (count($dailyData) > 0) {
        //     dd($dailyData->get('00'));
        //     // foreach ($dailyData as $key => $value) {
        //     //     dd($key, $value);
        //     // }
        // }

        // dd($dailyData->get($span)->sum('distance'));
        // $dailyData->get($span)->map(function ($items) use ($hourly_distance, $hourly_fuel_use) {
        //     $hourly_distance += $items->distance;
        //     $hourly_fuel_use += $items->fuel_use;
        //     // return $hourly_distance;
        //     // dd($hourly_distance, $items->distance);
        //     // $dis = (float) number_format($items->sum('distance'), 3, '.');
        //     // dd($dis);
        // });
        // // $hourly_distance = $data->map(function ($items) {
        // //     return (float) number_format($items->sum('distance'), 3, '.');
        // // });
        // // $distance = $data->get($span)->sum('distance');
        // dd($hourly_distance);
        endif;
    }

    /**
     * @OA\Post(
     *     path="/api/customer/monthly-report",
     *     summary="Vehicle Monthly Report",
     *     tags={"monthly-report"},
     *     description="Show Monthly Vehicle Report.",
     *     operationId="monthly-report",
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
     *         name="date",
     *         in="path",
     *         description="Report Date",
     *         required=true,
     *         @OA\Schema(
     *             type="date"
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
    public function monthlyReport(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'vehicle_id'    => 'required|exists:vehicles,id',
                'date'          => 'required|date_format:Y-m-d'
            ],
            [
                'vehicle_id.required'   => 'Vehicle is Required',
                'vehicle_id.exists'     => 'Provide Valid Vehicle Info',
                'date.required'         => 'Report Date is Required',
                'date.date_format'      => 'Provide Valid Date Format:YYYY-MM-DD'
            ]
        );

        $vehicles = Vehicle::where('customer_id', Auth::user()->id)->pluck('id')->toArray();
        $validator->after(function ($validator) use ($vehicles, $request) {
            if (!in_array($request->vehicle_id, $vehicles)) :
                $validator->errors()->add('vehicle_id', 'Provide Valid Vehicle Info');
            endif;
        });

        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $result = array();
            $dailyData = null;
            $monthly_distance = 0.000;
            $monthly_fuel_use = 0.000;

            $monthlyData = DeviceData::select('latitude', 'longitude', 'engine_status', 'speed', 'distance', 'fuel_use', 'created_at',)
                ->where('vehicle_id', $request->vehicle_id)
                ->whereYear('created_at', date("Y", strtotime($request->date)))
                ->whereMonth('created_at', date("m", strtotime($request->date)))
                ->get()
                ->groupBy(function ($items) {
                    return Carbon::parse($items->created_at)->format('Y-m-d');
                });


            if (count($monthlyData) > 0) :
                foreach ($monthlyData as $key => $value) :
                    $daily_distance = number_format($value->sum('distance'), 3, '.');
                    $daily_fuel_use = number_format($value->sum('fuel_use'), 3, '.');
                    $monthly_distance += $daily_distance;
                    $monthly_fuel_use += $daily_fuel_use;
                    $dailyData[$key] = array(
                        'daily_data'        => $value,
                        'daily_distance'    => $daily_distance,
                        'daily_fuel_use'    => $daily_fuel_use
                    );
                endforeach;
                $result['monthly_data']     = $dailyData;
                $result['monthly_distance'] = (string) $monthly_distance;
                $result['monthly_fuel_use'] = (string) $monthly_fuel_use;
            else :
                $result['monthly_data']     = $dailyData;
                $result['monthly_distance'] = $monthly_distance;
                $result['monthly_fuel_use'] = $monthly_fuel_use;
            endif;

            return Response([
                'status'    => true,
                'message'   => 'Vehicle Monthly Report',
                'data'      => $result
            ], Response::HTTP_OK);
        endif;
    }

    /**
     * @OA\Post(
     *     path="/api/customer/date-wise-distance-report",
     *     summary="Vehicle Date Wise Distance Report",
     *     tags={"date-wise-distance-report"},
     *     description="Vehicle Date Wise Distance Report.",
     *     operationId="date-wise-distance-report",
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
     *         name="start_date",
     *         in="path",
     *         description="Start Date",
     *         required=true,
     *         @OA\Schema(
     *             type="date"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="end_date",
     *         in="path",
     *         description="End Date",
     *         required=true,
     *         @OA\Schema(
     *             type="date"
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
    public function dateWiseDistanceReport(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'vehicle_id'    => 'required|exists:vehicles,id',
                'start_date'    => 'required|date_format:Y-m-d',
                'end_date'      => 'required|date_format:Y-m-d|after:start_date'
            ],
            [
                'vehicle_id.required'       => 'Vehicle is Required',
                'vehicle_id.exists'         => 'Provide Valid Vehicle Info',
                'start_date.required'       => 'Report Start Date is Required',
                'start_date.date_format'    => 'Provide Valid Start Date Format:YYYY-MM-DD',
                'end_date.required'         => 'Report End Date is Required',
                'end_date.date_format'      => 'Provide Valid End Date Format:YYYY-MM-DD',
                'end_date.after'            => 'Provide Valid Start and End Date'
            ]
        );

        $vehicles = Vehicle::where('customer_id', Auth::user()->id)->pluck('id')->toArray();
        $validator->after(function ($validator) use ($vehicles, $request) {
            if (!in_array($request->vehicle_id, $vehicles)) :
                $validator->errors()->add('vehicle_id', 'Provide Valid Vehicle Info');
            endif;
        });

        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $start_date = date("Y-m-d", strtotime('-1 day', strtotime($request->start_date)));
            $end_date = date("Y-m-d", strtotime('+1 day', strtotime($request->end_date)));
            $result = array();
            $dailyData = null;
            $total_distance = 0.000;

            $dateWiseData = DeviceData::select('latitude', 'longitude', 'engine_status', 'speed', 'distance', 'fuel_use', 'created_at',)
                ->where('vehicle_id', $request->vehicle_id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->get()
                ->groupBy(function ($items) {
                    return Carbon::parse($items->created_at)->format('Y-m-d');
                });

            if (count($dateWiseData) > 0) :
                foreach ($dateWiseData as $key => $value) :
                    $daily_distance = number_format($value->sum('distance'), 3, '.');
                    $total_distance += $daily_distance;

                    $dailyData[$key] = array(
                        'daily_data'        => $value,
                        'daily_distance'    => $daily_distance,
                    );
                endforeach;
                $result['date_wise_data']   = $dailyData;
                $result['total_distance']   = (string) $total_distance;
            else :
                $result['date_wise_data']   = $dailyData;
                $result['total_distance']   = $total_distance;
            endif;

            return Response([
                'status'    => true,
                'message'   => 'Vehicle Date Wise Distace Report',
                'data'      => $result
            ], Response::HTTP_OK);
        endif;
    }

    /**
     * @OA\Post(
     *     path="/api/customer/date-wise-fuel-report",
     *     summary="Vehicle Date Wise Fuel Report",
     *     tags={"date-wise-fuel-report"},
     *     description="Vehicle Date Wise Fuel Report.",
     *     operationId="date-wise-fuel-report",
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
     *         name="start_date",
     *         in="path",
     *         description="Start Date",
     *         required=true,
     *         @OA\Schema(
     *             type="date"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="end_date",
     *         in="path",
     *         description="End Date",
     *         required=true,
     *         @OA\Schema(
     *             type="date"
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
    public function dateWiseFuelReport(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'vehicle_id'    => 'required|exists:vehicles,id',
                'start_date'    => 'required|date_format:Y-m-d',
                'end_date'      => 'required|date_format:Y-m-d|after:start_date'
            ],
            [
                'vehicle_id.required'       => 'Vehicle is Required',
                'vehicle_id.exists'         => 'Provide Valid Vehicle Info',
                'start_date.required'       => 'Report Start Date is Required',
                'start_date.date_format'    => 'Provide Valid Start Date Format:YYYY-MM-DD',
                'end_date.required'         => 'Report End Date is Required',
                'end_date.date_format'      => 'Provide Valid End Date Format:YYYY-MM-DD',
                'end_date.after'            => 'Provide Valid Start and End Date'
            ]
        );

        $vehicles = Vehicle::where('customer_id', Auth::user()->id)->pluck('id')->toArray();
        $validator->after(function ($validator) use ($vehicles, $request) {
            if (!in_array($request->vehicle_id, $vehicles)) :
                $validator->errors()->add('vehicle_id', 'Provide Valid Vehicle Info');
            endif;
        });

        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $start_date = date("Y-m-d", strtotime('-1 day', strtotime($request->start_date)));
            $end_date = date("Y-m-d", strtotime('+1 day', strtotime($request->end_date)));
            $result = array();
            $dailyData = null;
            $total_fuel_use = 0.000;

            $dateWiseData = DeviceData::select('latitude', 'longitude', 'engine_status', 'speed', 'distance', 'fuel_use', 'created_at',)
                ->where('vehicle_id', $request->vehicle_id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->get()
                ->groupBy(function ($items) {
                    return Carbon::parse($items->created_at)->format('Y-m-d');
                });

            if (count($dateWiseData) > 0) :
                foreach ($dateWiseData as $key => $value) :
                    $daily_fuel_use = number_format($value->sum('fuel_use'), 3, '.');
                    $total_fuel_use += $daily_fuel_use;
                    $dailyData[$key] = array(
                        'daily_data'        => $value,
                        'daily_fuel_use'    => $daily_fuel_use
                    );
                endforeach;
                $result['date_wise_data']   = $dailyData;
                $result['total_fuel_use']   = (string) $total_fuel_use;
            else :
                $result['date_wise_data']   = $dailyData;
                $result['total_fuel_use'] = $total_fuel_use;
            endif;

            return Response([
                'status'    => true,
                'message'   => 'Vehicle Date Wise Fuel Report',
                'data'      => $result
            ], Response::HTTP_OK);
        endif;
    }
}
