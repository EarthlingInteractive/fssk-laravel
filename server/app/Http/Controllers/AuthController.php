<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
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
		$message = $response;
		if ($response == Password::INVALID_USER) {
			$message = 'User does not exist';
		}
		return response()->json(['code' => 500, 'message' => $message, 'error' => []], 500);
	}
}
