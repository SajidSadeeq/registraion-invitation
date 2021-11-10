<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Mail\ConfirmCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_name' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'token' => ['required', 'string'],
            'role_id' => ['required', 'integer'],
        ]);
        
        $email = base64_decode($request->token);
        $code = Str::random(6);
        $user = User::create([
            'user_name' => $request->user_name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'confirm_code' => $code,
            'user_role' => $request->role_id,
        ]);
        Mail::to($email)->send(new ConfirmCode($code));
        return response()->json([
            'message' => 'You are successfully registered!',
            'user_id' => $user->id
        ]);
    }
}
