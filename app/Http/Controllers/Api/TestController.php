<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestController extends Controller
{
    public function requestData(Request $request)
    {
        $newTestData = new Test;
        $newTestData->all_request = json_encode($request->all());
        $newTestData->save();

        return Response([
            'status'    => true,
            'message'   => 'Store Successflly',
            'data'      => $newTestData
        ], Response::HTTP_CREATED);
    }
}
