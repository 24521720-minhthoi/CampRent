<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    private const RESET_RESPONSE = 'If the email exists in our system, a password reset link will be sent.';

    public function changePassword(Request $request)
    {
        $user = $request->user();

        if ($user->google_id || $user->facebook_id) {
            return response()->json([
                'message' => 'Social login accounts cannot change password here.',
            ], 403);
        }

        $rules = [
            'password' => 'required|string|min:8|confirmed',
        ];

        if ($user->password) {
            $rules['current_password'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($user->password && ! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => ['current_password' => ['Current password is incorrect.']],
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || $user->google_id || $user->facebook_id) {
            return response()->json(['message' => self::RESET_RESPONSE]);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        Mail::to($user->email)->send(new ResetPasswordMail($user, $token));

        return response()->json(['message' => self::RESET_RESPONSE]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $resetRecord || ! Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['errors' => ['email' => ['Invalid or expired token.']]], 400);
        }

        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['errors' => ['email' => ['Token has expired.']]], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['errors' => ['email' => ['Invalid or expired token.']]], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password reset successfully.',
        ]);
    }
}
