<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if user exists and is not soft deleted
        $user = User::withTrashed()->where('email', $credentials['email'])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        if ($user->trashed()) {
            throw ValidationException::withMessages([
                'email' => 'Akun ini telah dinonaktifkan. Silakan hubungi administrator.',
            ]);
        }

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        $user = Auth::user();

        // Pengawas hanya boleh login via aplikasi mobile
        $isMobileApp = str_contains($request->header('User-Agent'), 'GentaCapacitor')
            || $request->cookies->get('capacitor_app') === '1';
        if ($user->isPengawas() && ! $isMobileApp) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akun pengawas hanya dapat login melalui aplikasi mobile.',
            ]);
        }

        // Regenerate session AFTER role check to prevent session fixation
        $request->session()->regenerate();

        // Force login with user object to ensure session is saved with user_id
        Auth::login($user, true);

        // Explicitly save session to database to ensure user_id is persisted
        $request->session()->put('login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d', $user->id);
        $request->session()->save();

        return redirect()->intended(route('dashboard'));
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::withTrashed()->where('email', $request->email)->first();

        if ($user && $user->trashed()) {
            return back()->with(['status' => 'Jika email terdaftar, tautan reset password akan dikirim.']);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::withTrashed()->where('email', $request->email)->first();

        if ($user && $user->trashed()) {
            return back()->withErrors(['email' => 'Akun ini telah dinonaktifkan. Silakan hubungi administrator.']);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    // Register hanya untuk admin (membuat user lain)

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
