<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
	use ResetsPasswords;

	/**
	 * Takes token, validates it
	 * @param  string $token
	 * @return
	 */
	public function validateToken($token)
	{
		//get user from password_resets table
		$user = User::whereNotNull('password_resets.token')
			->join('password_resets', 'users.email', '=', 'password_resets.email')->first();

		//validate user
		if ($this->broker()->tokenExists($user, $token)) {
			return response()->json(['isValid' => true, 'token' => $token, 'user' => $user], 200);
		}
		return response()->json(['code' => 500, 'message' => 'Invalid Token ID', 'error' => []], 500);
	}

	/**
	 * Get the password reset validation rules.
	 *
	 * @return array
	 */
	protected function rules()
	{
		return [
			'resetToken' => 'required',
			'email' => 'required|email',
			'password' => 'required|min:10',
		];
	}

	/**
	 * Get the password reset credentials from the request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	protected function credentials(Request $request)
	{
		$credentials = $request->only(
			'email', 'password', 'resetToken'
		);
		$credentials['token'] = $credentials['resetToken'];
		$credentials['password_confirmation'] = $credentials['password'];
		unset($credentials['resetToken']);
		return $credentials;
	}

	/**
	 * Get the response for a successful password reset.
	 *
	 * @param  string  $response
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	protected function sendResetResponse($response)
	{
		return response()->json(['isValid' => true], 200);
	}

	/**
	 * Get the response for a failed password reset.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $response
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	protected function sendResetFailedResponse(Request $request, $response)
	{
		$message = ($response == Password::INVALID_USER) ? 'User does not exist' : 'Reset token is invalid';
		return response()->json(['code' => 500, 'message' => trans($message), 'error' => []], 500);
	}
}
