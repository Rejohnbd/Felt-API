<?php

namespace App\Http\Controllers\Api\Web\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class CustomerInfoController extends Controller
{
    public function getProfile()
    {
        $userInfo = Auth::user()->load(['userRole', 'userDetails']);
        $data = $this->formatCustomerInfo($userInfo);
        return Response([
            'status'    => true,
            'message'   => 'Customer Details',
            'data'      => $data
        ], Response::HTTP_OK);
    }


    private function formatCustomerInfo($userInfo)
    {
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
        return $data;
    }
}
