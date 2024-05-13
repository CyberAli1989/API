<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterReqesut;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterReqesut $reqesut)
    {
        $create = $reqesut->only(['email', 'name']);
        $create['password'] = Hash::make($reqesut['password']);
        $user = User::create($create);
        if ($user) {
            $token = $user->createToken('register')->plainTextToken;
            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'user' => $user->only([
                        'name',
                        'email',
                        'created_at'
                    ])
                ]
            ]);
        } else
            return response()->json([
                'success' => false,
            ], 422);
    }
}
