<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = User::with([
            'userRole',
            'userDetails',
            'createdByUser'
        ]);
        if ($request->type == 'admin') :
            $query->where('role_id', 2);
        elseif ($request->type == 'employee') :
            $query->where('role_id', 3);
        elseif ($request->type == 'customer') :
            $query->where('role_id', 4);
        elseif ($request->type == 'driver') :
            $query->where('role_id', 5);
        elseif ($request->type == 'all' || is_null($request->type)) :
            $query->whereIn('role_id', [2, 3, 4, 5]);
        else :
            $query->where('role_id', 1);
        endif;
        $data = $query->orderBy('id', 'DESC')->get();
        return Response([
            'status'    => true,
            'message'   => 'All Users',
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
    public function store(Request $request): Response
    {
        $validator = validator(
            $request->all(),
            [
                'first_name'        => 'required',
                'email'             => 'required|email:rfc,dns|max:255|unique:users,email',
                'phone_number'      => 'required|min:11|max:11|unique:user_details,phone_number',
                'password'          => 'required|string|min:8',
                'user_role'         => 'required|numeric|exists:user_roles,id',
                'email_optional'    => 'nullable|email:rfc,dns|max:255',
                'phone_optional'    => 'nullable|min:11|max:11',
                'company_name'      => $request->user_role == 3 ? 'required' : 'nullable',
                'designation'       => $request->user_role == 3 ? 'required' : 'nullable',
                'address'           => $request->user_role == 4 ? 'required' : 'nullable',
                'image'             => 'nullable|mimes:jpeg,jpg,png',
                'user_status'       => 'required|numeric|in:0,1,2',
            ],
            [
                'first_name.required'   => 'User First Name is Required',
                'email.required'        => 'User Email is Required',
                'email.email'           => 'Provide Valid Email',
                'email.max'             => 'Provide Valid Email',
                'email.unique'          => 'User Email Already Exists',
                'phone_number.required' => 'User Phone Number is Required',
                'phone_number.required' => 'User Phone Number is Required',
                'phone_number.min'      => 'User Phone Number not Less than 11',
                'phone_number.max'      => 'User Phone Number not Greater than 11',
                'phone_number.unique'   => 'User Phone Number Already Exist',
                'password.required'     => 'User Password required',
                'password.min'          => 'User Password not less than 8',
                'user_role.required'    => 'User Role Required',
                'user_role.numeric'     => 'Select Valid User Role',
                'user_role.exists'      => 'Select Valid User Role',
                'email_optional.email'  => 'Provide Valid Optional Email',
                'email_optional.max'    => 'Provide Valid Optional Email',
                'phone_optional.min'    => 'Optional Phone Number not Less than 11',
                'phone_optional.max'    => 'Optional Phone Number not Greater than 11',
                'company_name.required' => 'Company Name Required',
                'designation.required'  => 'User Designation Required',
                'address.required'      => 'User Address Required',
                'image.mimes'           => 'User Image only jpg, jpeg & png format',
                'user_status.required'  => 'User Status Required',
                'user_status.numeric'   => 'Select Valid User Status',
                'user_status.in'        => 'Select Valid User Status'
            ]
        );
        if ($validator->fails()) :
            return Response([
                'status'    => false,
                'message'   => $validator->getMessageBag()->first(),
                'errors'    => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            DB::beginTransaction();

            try {
                $newUser = new User;
                $newUser->role_id       = $request->user_role;
                $newUser->email         = $request->email;
                $newUser->user_status   = $request->user_status;
                $newUser->password      = Hash::make($request->password);
                if ($request->user_role == 5) :
                    $newUser->created_by = $request->customer;
                else :
                    $newUser->created_by = auth()->user()->id;
                endif;
                $newUser->save();

                $newUserDetails = new UserDetails;
                $newUserDetails->user_id        = $newUser->id;
                $newUserDetails->first_name     = $request->first_name;
                $newUserDetails->last_name      = $request->last_name;
                $newUserDetails->company_name   = $request->company_name;
                $newUserDetails->designation    = $request->designation;
                $newUserDetails->email_optional = $request->email_optional;
                $newUserDetails->phone_number   = $request->phone_number;
                $newUserDetails->phone_optional = $request->phone_optional;
                $newUserDetails->address        = $request->address;
                $newUserDetails->reference      = $request->reference;
                $newUserDetails->notes          = $request->notes;

                if ($request->hasFile('image')) :
                    $file = $request->file('image');
                    $fileExtension = $request->image->extension();
                    $fileName = $newUser->email . "_" . Str::random(5) . "_" . date('his') . '.' . $fileExtension;
                    $folderpath = public_path() . '/user_image';
                    $file->move($folderpath, $fileName);
                    $newUserDetails->image = '/user_image/' . $fileName;
                endif;

                $newUserDetails->save();

                DB::commit();

                return Response([
                    'status'    => true,
                    'message'   => 'User Created Successfully',
                    'data'      => $newUser
                ], Response::HTTP_CREATED);
            } catch (\Exception $e) {
                DB::rollback();
                return Response([
                    'status'    => false,
                    'message'   => $e->getMessage(),
                    'errors'    => []
                ], Response::HTTP_BAD_REQUEST);
            }
        endif;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $userInfo = User::with(['userRole', 'userDetails'])->find($id);
        if (!is_null($userInfo)) :
            return Response([
                'status'    => true,
                'message'   => 'User Info.',
                'data'      => $userInfo
            ], Response::HTTP_OK);
        else :
            return Response([
                'status'    => false,
                'message'   => 'User Not Found.',
                'data'      => $userInfo
            ], Response::HTTP_NOT_FOUND);
        endif;
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
    public function update(Request $request): Response
    {
        $userInfo = User::with(['userRole', 'userDetails'])->find($request->id);
        if (!is_null($userInfo)) :
            $validator = validator(
                $request->all(),
                [
                    'first_name'        => 'required',
                    'email'             => 'required|email:rfc,dns|max:255|unique:users,email,' . $userInfo->id,
                    'phone_number'      => 'required|min:11|max:11|unique:user_details,phone_number,' . $userInfo->userDetails->id,
                    'password'          => 'nullable|string|min:8',
                    'user_role'         => 'required|numeric|exists:user_roles,id',
                    'email_optional'    => 'nullable|email:rfc,dns|max:255',
                    'phone_optional'    => 'nullable|min:11|max:11',
                    'company_name'      => $request->user_role == 3 ? 'required' : 'nullable',
                    'designation'       => $request->user_role == 3 ? 'required' : 'nullable',
                    'address'           => $request->user_role == 4 ? 'required' : 'nullable',
                    'image'             => 'nullable|mimes:jpeg,jpg,png',
                    'user_status'       => 'required|numeric|in:0,1,2',
                ],
                [
                    'first_name.required'   => 'User First Name is Required',
                    'email.required'        => 'User Email is Required',
                    'email.email'           => 'Provide Valid Email',
                    'email.max'             => 'Provide Valid Email',
                    'email.unique'          => 'User Email Already Exists',
                    'phone_number.required' => 'User Phone Number is Required',
                    'phone_number.required' => 'User Phone Number is Required',
                    'phone_number.min'      => 'User Phone Number not Less than 11',
                    'phone_number.max'      => 'User Phone Number not Greater than 11',
                    'phone_number.unique'   => 'User Phone Number Already Exist',
                    'password.min'          => 'User Password not less than 8',
                    'user_role.required'    => 'User Role Required',
                    'user_role.numeric'     => 'Select Valid User Role',
                    'user_role.exists'      => 'Select Valid User Role',
                    'email_optional.email'  => 'Provide Valid Optional Email',
                    'email_optional.max'    => 'Provide Valid Optional Email',
                    'phone_optional.min'    => 'Optional Phone Number not Less than 11',
                    'phone_optional.max'    => 'Optional Phone Number not Greater than 11',
                    'company_name.required' => 'Company Name Required',
                    'designation.required'  => 'User Designation Required',
                    'address.required'      => 'User Address Required',
                    'image.mimes'           => 'User Image only jpg, jpeg & png format',
                    'user_status.required'  => 'User Status Required',
                    'user_status.numeric'   => 'Select Valid User Status',
                    'user_status.in'        => 'Select Valid User Status'
                ]
            );

            if ($validator->fails()) :
                return Response([
                    'status'    => false,
                    'message'   => $validator->getMessageBag()->first(),
                    'errors'    => $validator->getMessageBag()
                ], Response::HTTP_BAD_REQUEST);
            else :
                // return Response([
                //     'status'    => true,
                //     'message'   => $request->all(),
                //     'data'      => $userInfo
                // ], Response::HTTP_CREATED);
                DB::beginTransaction();

                try {
                    $userInfo->role_id      = $request->user_role;
                    $userInfo->email        = $request->email;
                    $userInfo->user_status  = $request->user_status;
                    if ($request->filled('password')) :
                        $userInfo->password = Hash::make($request->password);
                    endif;
                    if ($request->user_role == 5) :
                        $userInfo->created_by = $request->customer;
                    else :
                        $userInfo->created_by = auth()->user()->id;
                    endif;
                    $userInfo->save();

                    // $userInfo->user_id        = $userInfo->id;
                    $userInfo->userDetails->first_name     = $request->first_name;
                    $userInfo->userDetails->last_name      = $request->last_name;
                    if ($request->filled('company_name') && $request->company_name != 'null') :
                        $userInfo->userDetails->company_name    = $request->company_name;
                    endif;
                    if ($request->filled('designation') && $request->designation != 'null') :
                        $userInfo->userDetails->designation     = $request->designation;
                    endif;
                    $userInfo->userDetails->email_optional = $request->email_optional;
                    $userInfo->userDetails->phone_number   = $request->phone_number;
                    $userInfo->userDetails->phone_optional = $request->phone_optional;
                    if ($request->filled('address') && $request->address != 'null') :
                        $userInfo->userDetails->address = $request->address;
                    endif;
                    $userInfo->userDetails->reference      = $request->reference;
                    $userInfo->userDetails->notes          = $request->notes;

                    if ($request->hasFile('image')) :
                        $image_path = public_path($userInfo->userDetails->image);
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                        $file = $request->file('image');
                        $fileExtension = $request->image->extension();
                        $fileName = $userInfo->email . "_" . Str::random(5) . "_" . date('his') . '.' . $fileExtension;
                        $folderpath = public_path() . '/user_image';
                        $file->move($folderpath, $fileName);
                        $userInfo->userDetails->image = '/user_image/' . $fileName;
                    endif;

                    $userInfo->userDetails->save();

                    DB::commit();

                    return Response([
                        'status'    => true,
                        'message'   => 'User Update Successfully',
                        'data'      => $request->all()
                    ], Response::HTTP_CREATED);
                } catch (\Exception $e) {
                    DB::rollback();
                    return Response([
                        'status'    => false,
                        'message'   => $e->getMessage(),
                        'errors'    => []
                    ], Response::HTTP_BAD_REQUEST);
                }
            endif;
        else :
            return Response([
                'status'    => false,
                'message'   => 'User Not Found.',
                'data'      => $userInfo
            ], Response::HTTP_NOT_FOUND);
        endif;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        //
    }

    public function customerUsers(): Response
    {
        $data = User::with(['userRole', 'userDetails'])->where('role_id', 4)->orderBy('id', 'DESC')->get();

        return Response([
            'status'    => true,
            'message'   => 'All Customer Users',
            'data'      => $data
        ], Response::HTTP_OK);
    }
}
