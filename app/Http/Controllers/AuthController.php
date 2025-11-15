<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // REGISTER USER & SEND OTP
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Create user with OTP
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp_code' => $otp,
        ]);

        // Send OTP via email
        try {
            Mail::raw("Your Banana Blitz OTP is: $otp", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Verify Your Email - Banana Blitz');
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User created but OTP email could not be sent. Check SMTP settings.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Registration successful. Check your email for the OTP.',
            'user_id' => $user->id
        ], 201);
    }

    // LOGIN USER
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Check if OTP is still pending
        if ($user->otp_code !== null) {
            return response()->json(['message' => 'Account not verified. Please verify OTP first.'], 403);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
        ]);
    }

    // VERIFY OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'otp' => 'required|string',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->otp_code !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        // OTP verified → clear OTP and mark email as verified
        $user->otp_code = null;
        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'OTP verified successfully. You can now login.']);
    }

    // RESEND OTP
    public function resendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->email_verified_at !== null) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->save();

        try {
            Mail::raw("Your new Banana Blitz OTP is: $otp", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Resend OTP - Banana Blitz');
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'OTP could not be sent. Check SMTP settings.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json(['message' => 'OTP resent successfully']);
    }
}
