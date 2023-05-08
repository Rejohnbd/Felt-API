<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Response([
            'status'    => true,
            'message'   => 'Customer Dashboard',
            'data'      => 'ok'
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/customer/profile",
     *     tags={"getCustomerProfile"},
     *     summary="Returns Customer Profile",
     *     description="Returns Customer Profile Information",
     *     operationId="getCustomerProfile",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             
     *         )
     *     ),
     *     security={
     *         {"Bearer token": {}}
     *     }
     * )
     */
    public function getProfile()
    {
        $userInfo = Auth::user()->load(['userRole', 'userDetails']);
        $data = array(
            'id'                => $userInfo->id,
            'email'             => $userInfo->email,
            'email_optional'    => $userInfo->userDetails->email_optional,
            'first_name'        => $userInfo->userDetails->first_name,
            'last_name'         => $userInfo->userDetails->last_name,
            'phone_number'      => $userInfo->userDetails->phone_number,
            'phone_optional'    => $userInfo->userDetails->phone_optional,
            'address'           => $userInfo->userDetails->address,
            'image'             => $userInfo->userDetails->image,
            'user_status'       => $userInfo->user_status,
            'created_at'        => $userInfo->created_at,
        );
        return Response([
            'status'    => true,
            'message'   => 'Customer Details',
            'data'      => $data
        ], Response::HTTP_OK);
    }

    public function updateProfile(Request $request)
    {
        return Response([
            'status'    => true,
            'message'   => 'Customer Dashboard',
            'data'      => 'ok'
        ], Response::HTTP_OK);
    }
}
