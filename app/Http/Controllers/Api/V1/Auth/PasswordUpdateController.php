<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


/**
 *  @group Auth
 */
class PasswordUpdateController extends Controller
{
    public function __invoke(Request $request){

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        auth()->user()->update([
            'password' => Hash::make($request->input('password')),
        ]);
        
        return response()->json([
            'message' => 'Password is updated successfully',
        ], Response::HTTP_ACCEPTED);
    }
}
