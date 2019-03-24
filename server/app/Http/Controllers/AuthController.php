<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends Controller
{
	use SendsPasswordResetEmails;

	/**
	 * Return logged in user
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		if (Auth::guard('api')->check()) {
			return response()->json(["user" => Auth::guard('api')->user()]);
		}
		return response()->json(['user' => false], 200);
	}

	/**
	 * Register a new user
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function register(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'name' => 'required|string',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string',
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 400);
		}

		$data = $request->all();

		$user = User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
		]);
		return response()->json(['user' => $user], 201);
	}

	/**
	 * Log in
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function login(Request $request)
	{
		$input = $request->all();
		$validator = Validator::make($input, [
			'email' => 'required',
			'password' => 'required',
		]);
		if ($validator->fails()) {
			return response()->json(['error' => 'missing required field.'], 400);
		}


		if (Auth::guard('api')->attempt(['email' => $input['email'], 'password' => $input['password']])) {
			return response()->json(['user' => Auth::guard('api')->user()]);
		}

		return response()->json(['error' => 'log in failed.'], 401);
	}

	/**
	 * Log out
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function logout(Request $request)
	{
		Auth::guard('api')->logout();
		return response(null, 204);
	}

	/**
	 * Takes email address, creates a token and sends email
	 * @param  \Illuminate\Http\Request  $request
	 * @return
	 */
	public function forgotPassword(Request $request)
	{
		return $this->sendResetLinkEmail($request);
	}

	/**
	 * Get the response for a successful password reset link.
	 *
	 * @param  string  $response
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	protected function sendResetLinkResponse($response)
	{
		return response()->json(['isValid' => true], 200);
	}

	/**
	 * Get the response for a failed password reset link.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $response
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
	 */
	protected function sendResetLinkFailedResponse(Request $request, $response)
	{
		return response()->json(['error' => $response], 400);
	}

	/**
	 * Takes token, validates it
	 * @param  string $token
	 * @return
	 */
	public function validateToken($token)
	{
		//get user from token
		$user = User::whereNotNull('password_resets.token')
			->join('password_resets', 'users.email', '=', 'password_resets.email')->first();

		if ($this->broker()->tokenExists($user, $token)) {
			return response()->json(['isValid' => true, 'token' => $token, 'user' => $user], 200);
		}
		return response()->json(['code' => 500, 'message' => 'Invalid Token ID', 'error' => []], 500);
	}
}
