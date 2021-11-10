<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\InviteUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function invite_user(Request $request)
    {   
        $request->validate([
            'email' => ['required']
        ]);
        Mail::to($request->email)->send(new InviteUser($request->email));
        return response()->json([
            'status' => 200,
            'message' => 'User Successfully invited !'
        ]);
    }
    
    public function confirm_code(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
            'code' => ['required']
        ]);
        $user = User::find($request->user_id);   
        $message = 'Code is wrong';
        if($user->confirm_code == $request->code)
        {
            $user->verified_at = Carbon::now();
            $user->save();
            $message = 'You are successfully verified !';
        }

        return response()->json([
            'status' => 200,
            'message' => $message
        ]);
    }

    public function update_profile(Request $request)
    {
        $request->validate([
            'name' => ['nullable'],
            'avatar' => ['nullable', 'dimensions:min_width=256,min_height=256', 'mimes:jpeg,png,jpg,gif,svg'],
            'email' => ['nullable', 'email']
        ]);
        $path = null;
        
        if($request->hasFile('avatar'))
        {
            $imageName = time().'.'.$request->avatar->extension();  
            $path = 'users/'.$imageName; 
            $request->avatar->move(public_path('users'), $imageName);
            if(!is_null(Auth::user()->avatar))
            {
                unlink(public_path(Auth::user()->avatar));
            }
        }
        User::where('id', Auth::id())->update([
            'name' => $request->name,
            'email' => $request->email,
            'avatar' => $path
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Profile updated successfully',
            'user' => Auth::user()
        ]);

    }
}
