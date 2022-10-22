<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    //REGISTER
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'password' => 'required|confirmed',
        ]);

        // CHECK VALIDATE
        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ],
                Response::HTTP_UNAUTHORIZED
            );
        } else {
            // CREATE USER
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json(
                [
                    'message' => "Created Success"
                ],
                Response::HTTP_CREATED
            );
        }
    }
    // LOGIN
    public function login(Request $request)
    {

        // $message = [
        //     'email.email' => 'Error Email',
        // ];

        $validate = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required',
        ]);

        // CHECK VALIDATE LOGIN FIELD

        if ($validate->fails()) {
            return response()->json(
                [
                    'message' => $validate->errors()->first(),
                    'errors' => $validate->errors(),
                ],
                Response::HTTP_UNAUTHORIZED
            );
        } else {

            // CHECK LOGIN AFTER ENTER FULL FIELD

            $user = User::where(['email' => $request->email])->first();
            if (!$user) {
                return response()->json(
                    [
                        'message' => 'User not exist!',
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            } elseif (!Hash::check($request->password, $user->password, [])) {
                return response()->json(
                    [
                        'message' => 'Wrong password!',
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            } else {
                // CREATE TOKEN
                $token = $user->createToken('AuthToken')->plainTextToken;

                return response()->json(
                    [
                        'access_token' => $token,
                        'type_token' => 'Bearer',
                    ],
                    Response::HTTP_OK

                );
            };
        }
    }

    //GET USER AFTER LOGIN
    public function user()
    {
        return Auth::user();
    }
}
