<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::query()
            ->where('email', $request->email)
            ->first();

        if ($user) {

            // standart user
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('web-user');
                return response()->json([
                    'token' => $token->plainTextToken
                ]);
            }
        }

        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
        
    }

    public function logout()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout'
        ]);
    }
}
