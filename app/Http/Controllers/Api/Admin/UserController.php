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
    public function index(): Response
    {
        $data = User::with([
            'userRole',
            'userDetails',
            'createdByUser'
        ])->orderBy('id', 'DESC')->get();
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
                'user_status.numeric'   => 'Select Valid User Status 2',
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
        //
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
    public function update(Request $request, string $id): RedirectResponse
    {
        //
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
