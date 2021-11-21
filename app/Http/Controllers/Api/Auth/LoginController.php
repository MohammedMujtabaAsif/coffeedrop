<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponseTrait;
use Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use ResponseTrait;

    /**
     * Attempt to login a user with their email and password
     *
     * @param Request $request
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Search the db for the user
        $user = User::where('email', $request->email)->first();

        // If the user is found
        if ($user) {
            // Attempt to login using the provided credentials
            if (Auth::attempt($data)) {
                // If successful

                // Create an access token
                $token = $user->createToken('appToken')->accessToken;

                // Return the access token and user model
                return $this->responseWithData([
                    'token' => $token,
                    'user' => $user,
                ]);
            }
        }

        // If the user is not found or the credentials are incorrect
        // Return a generic failed login message to stop attempts to check registered email addresses
        return $this->responseWithMessageFailed('Invalid credentials.', 401);

    }

    /**
     * Attempt to logout the user
     *
     * @param Request $request
     */
    public function logout(Request $request)
    {
        // Attempt to revoke the api user's token
        if ($request->user('api')->token()->revoke()) {
            // If the token was revoked,alert the user that they have been logged out
            return $this->responseWithMessageSuccess('You have been logged out.');
        }

        // If the token was not revoked, alert the user they could not be logged out
        return $this->responseWithMessageFailed('Failed to logout user.', 401);
    }
}
