<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Otp\GenerateOtpRequest;
use App\Http\Requests\Auth\Otp\OTPCodeRequest;
use App\Http\Requests\Auth\RegisterReqesut;
use App\Lib\Sms\SmsIr;
use App\Mail\VerifyCode;
use App\Models\OtpCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use function Laravel\Prompts\error;

class AuthController extends Controller
{
    public function register(RegisterReqesut $request)
    {
        $create = $request->only(['name']);
        if ($request['type'] === User::EMAIL_Type)
            $create['email'] = $request['login'];
        else
            $create['phone'] = $request['login'];
        $create['password'] = Hash::make($request['password']);
        $user = User::create($create);
        if ($user) {
            $code = rand(11111, 99999);
            OtpCode::create([
                'code' => $code,
                'login' => $request['login'],
                'expired_at' => Carbon::now()->addMinutes(2),
            ]);
            if ($request['type'] === User::EMAIL_Type && env('MAIL_STATUS'))
                Mail::to($user->email)->send(new VerifyCode($code));
            if ($request['type'] === User::PHONE_Type && env('SMS_STATUS')) {
                $sms = new SmsIr();
                $sms->verifyCode($request['login'], $code);
            }
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => [
                    'success' => $request['type'] === User::EMAIL_Type ? 'کد تایید به ایمیل شما ارسال شد' : 'کد تایید به شماره همراه شما ارسال شد.'
                ],
            ]);
        } else
            return response()->json([
                'success' => false,
            ], 422);
    }

    public function verifyCode(OTPCodeRequest $request)
    {
        if ($request['type'] === User::EMAIL_Type)
            $user = User::firstWhere('email', $request['login']);
        else
            $user = User::firstWhere('phone', $request['login']);
        if ($user) {
            $code = OtpCode::firstWhere('login', $request['login']);
            if ($code->code == $request['code']) {
                if ($code->expired_at > Carbon::now()) {
                    $token = $user->createToken('token')->plainTextToken;
                    $code->delete();
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'token' => $token,
                            'user' => $user->only(['email', 'phone', 'name', 'created_at'])
                        ]
                    ]);
                } else
                    return response()->json([
                        'success' => false,
                        'message' => [
                            'expired' => 'کد تایید ارسالی فاقد اعتبار می باشد.'
                        ]
                    ]);
            } else
                return response()->json([
                    'success' => false,
                    'message' => [
                        'wrong' => 'کد تایید ارسال شده اشتباه می باشد.'
                    ]
                ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => [
                    'not_found' => 'کاربر مورد نظر یافت نشد.'
                ]
            ]);
        }
    }

    public function generateOtp(GenerateOtpRequest $request)
    {
        if ($request['type'] === User::EMAIL_Type)
            $user = User::firstWhere('email', $request['login']);
        else
            $user = User::firstWhere('phone', $request['login']);
        if ($user) {
            $code = rand(11111, 99999);
            $otp = OtpCode::firstWhere('login', $request['login']);
            if ($otp) {
                $otp->update([
                    'attempt' => $otp->attempt + 1,
                    'code' => $code
                ]);
            } else {
                OtpCode::create([
                    'code' => $code,
                    'login' => $request['login'],
                    'expired_at' => Carbon::now()->addMinutes(2),
                ]);
            }
            if ($request['type'] === User::EMAIL_Type && env('MAIL_STATUS'))
                Mail::to($user->email)->send(new VerifyCode($code));
            if ($request['type'] === User::PHONE_Type && env('SMS_STATUS')) {
                $sms = new SmsIr();
                $sms->verifyCode($request['login'], $code);
            }
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => [
                    'success' => $request['type'] === User::EMAIL_Type ? 'کد تایید به ایمیل شما ارسال شد' : 'کد تایید به شماره همراه شما ارسال شد.'
                ],
            ]);
        } else
            return response()->json([
                'success' => false,
                'message' => [
                    'not_found' => 'کاربر یافت نشد.'
                ]
            ], 422);
    }
}
