<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Piyush\PassportAuth\Contracts\AuthenticationServiceInterface;
use Throwable;

class AuthController extends Controller
{
    public function __construct(
        protected AuthenticationServiceInterface $authenticationService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Login Page
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        if (session()->has('user_token')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | Register Page
    |--------------------------------------------------------------------------
    */

    public function showRegister()
    {
        if (session()->has('user_token')) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }
/*
|--------------------------------------------------------------------------
| Reset Password Page
|--------------------------------------------------------------------------
*/

public function showResetPassword(Request $request)
{
    if (session()->has('user_token')) {
        return redirect()->route('dashboard');
    }

    return view('auth.reset-password', [
        'token' => $request->query('token'),
        'email' => $request->query('email'),
    ]);
}
    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        try {

            $this->authenticationService->register($validated);

            $request->session()->forget([
                'user_token',
                'user',
                'device_id',
                'user_email',
                'user_name',

                'pending_access_token',
                'pending_user',
                'pending_user_email',
                'pending_user_name',
            ]);

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Registration successful. Please login to continue.'
                );

        } catch (Throwable $e) {

            report($e);

            return back()
                ->withInput(
                    $request->except([
                        'password',
                        'password_confirmation',
                    ])
                )
                ->with(
                    'error',
                    $this->safeError(
                        $e,
                        'Unable to create your account. Please try again.'
                    )
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | Login only creates a temporary authentication state.
    |
    | Device verification decides whether the web session
    | can finally be created.
    |
    */

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],
        ]);

        try {

            /*
            |--------------------------------------------------------------------------
            | Device ID
            |--------------------------------------------------------------------------
            */

            $deviceId =
                $request->header('X-Device-ID')
                ?? $request->input('device_id')
                ?? session('device_id');

            /*
            |--------------------------------------------------------------------------
            | Passport Authentication
            |--------------------------------------------------------------------------
            */

            $result = $this->authenticationService->login([
                'email' => $validated['email'],
                'password' => $validated['password'],
                'device_id' => $deviceId,
            ]);

            $user = $result['user'] ?? null;

            $tokens = $result['tokens'] ?? [];

            $accessToken =
                $tokens['access_token']
                ?? null;

            if (!$user || !$accessToken) {
                throw new \RuntimeException(
                    'Authentication token could not be generated.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Regenerate Session
            |--------------------------------------------------------------------------
            */

            $request->session()->regenerate();

            /*
            |--------------------------------------------------------------------------
            | Temporary Authentication
            |--------------------------------------------------------------------------
            */

            $request->session()->put([
                'pending_access_token' => $accessToken,

                'pending_user' => $user,

                'pending_user_email' =>
                    $user->email ?? $validated['email'],

                'pending_user_name' =>
                    $user->name ?? '',
            ]);

            if ($deviceId) {
                $request->session()->put(
                    'device_id',
                    $deviceId
                );
            }

            $request->session()->save();

            /*
            |--------------------------------------------------------------------------
            | ALWAYS GO TO DEVICE CHECK
            |--------------------------------------------------------------------------
            |
            | Existing DeviceVerificationController already knows:
            |
            | - device exists
            | - device trusted
            | - device blocked
            | - OTP required
            |
            | Therefore AuthController does NOT duplicate that logic.
            |
            */

            return redirect()
                ->route('verify.device');

        } catch (Throwable $e) {

            report($e);

            $request->session()->forget([
                'pending_access_token',
                'pending_user',
                'pending_user_email',
                'pending_user_name',
            ]);

            return back()
                ->withInput(
                    $request->except('password')
                )
                ->with(
                    'error',
                    $this->safeError(
                        $e,
                        'Unable to login. Please check your credentials.'
                    )
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Device Verification Page
    |--------------------------------------------------------------------------
    */

    public function verifyDevice()
    {
        if (!session()->has('pending_access_token')) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Please login to continue.'
                );
        }

        return view('auth.device-verification');
    }

    /*
    |--------------------------------------------------------------------------
    | Establish Web Session
    |--------------------------------------------------------------------------
    |
    | This method is called only after:
    |
    | - trusted device was detected
    | OR
    | - OTP verification succeeded.
    |
    | The DeviceIdentificationMiddleware provides current_device.
    |
    */

public function establishSession(Request $request)
{
    try {

        /*
        |--------------------------------------------------------------------------
        | Access Token
        |--------------------------------------------------------------------------
        */

        $token =
            $request->bearerToken()
            ?? session('pending_access_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token is required.',
            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | Device ID
        |--------------------------------------------------------------------------
        */

        $deviceId =
            $request->header('X-Device-ID')
            ?? $request->input('device_id')
            ?? session('device_id');

        if (!$deviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Device ID is required.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Passport User
        |--------------------------------------------------------------------------
        */

        $request->headers->set(
            'Authorization',
            'Bearer ' . $token
        );

        $guard = \Illuminate\Support\Facades\Auth::guard('api');

        $guard->setRequest($request);

        $user = $guard->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired authentication token.',
            ], 401);
        }


        /*
        |--------------------------------------------------------------------------
        | Device
        |--------------------------------------------------------------------------
        */

        $device = \Illuminate\Support\Facades\DB::table(
            'passport_auth_devices'
        )
            ->where('user_id', $user->getKey())
            ->where('device_id', $deviceId)
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Device Not Found
        |--------------------------------------------------------------------------
        */

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found. Please verify this device.',
                'device_verification_required' => true,
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Revoked Device
        |--------------------------------------------------------------------------
        */

        if ($device->revoked_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'This device has been revoked.',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Update Device Activity
        |--------------------------------------------------------------------------
        */

        \Illuminate\Support\Facades\DB::table(
            'passport_auth_devices'
        )
            ->where('id', $device->id)
            ->update([
                'last_active_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'updated_at' => now(),
            ]);


        /*
        |--------------------------------------------------------------------------
        | Create Web Session
        |--------------------------------------------------------------------------
        */

        $this->createAuthenticatedWebSession(
            $request,
            $token,
            $user,
            $deviceId
        );


        return response()->json([
            'success' => true,
            'message' => 'Web session established.',
            'device_verified' => true,
        ]);

    } catch (\Throwable $e) {

        report($e);

        \Illuminate\Support\Facades\Log::error(
            'WEB SESSION ESTABLISHMENT FAILED',
            [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'device_id' =>
                    $request->header('X-Device-ID')
                    ?? $request->input('device_id')
                    ?? session('device_id'),
            ]
        );

        return response()->json([
            'success' => false,
            'message' => 'Unable to establish web session.',
        ], 500);
    }
}
    /*
    |--------------------------------------------------------------------------
    | Create Authenticated Web Session
    |--------------------------------------------------------------------------
    */

    protected function createAuthenticatedWebSession(
        Request $request,
        string $accessToken,
        mixed $user,
        ?string $deviceId = null
    ): void {

        $request->session()->regenerate();

        $request->session()->put([
            'user_token' => $accessToken,

            'user' => $user,

            'user_email' =>
                $user->email ?? '',

            'user_name' =>
                $user->name ?? '',
        ]);

        if ($deviceId) {
            $request->session()->put(
                'device_id',
                $deviceId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Clear Temporary Authentication
        |--------------------------------------------------------------------------
        */

        $request->session()->forget([
            'pending_access_token',
            'pending_user',
            'pending_user_email',
            'pending_user_name',
        ]);

        $request->session()->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Me
    |--------------------------------------------------------------------------
    */

    public function me()
    {
        $user = session('user');

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request): RedirectResponse
    {
        try {

            $token = $request
                ->session()
                ->get('user_token');

            if ($token) {

                $request->headers->set(
                    'Authorization',
                    'Bearer ' . $token
                );

                $guard = Auth::guard('api');

                $guard->setRequest($request);

                $user = $guard->user();

                if ($user) {
                    $this->authenticationService->logout();
                }
            }

        } catch (Throwable $e) {

            report($e);
        }

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | Logout only removes web authentication.
        |
        | Device trust remains.
        |
        */

        $request->session()->forget([
            'user_token',
            'user',
            'device_id',
            'user_email',
            'user_name',

            'pending_access_token',
            'pending_user',
            'pending_user_email',
            'pending_user_name',
        ]);

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'You have been logged out successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Safe Error
    |--------------------------------------------------------------------------
    */

    protected function safeError(
        Throwable $e,
        string $fallback
    ): string {

        if (
            $e instanceof
            \Illuminate\Validation\ValidationException
        ) {
            return $e->getMessage();
        }

        return $fallback;
    }
}