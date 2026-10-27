# Authentication System Documentation

## Overview
Complete authentication system with login, password reset, and role-based redirection.

## Routes

### Web Routes (routes/web.php)
```php
// Guest routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');

// Protected routes (require auth)
Route::middleware('auth')->group(function () {
    // Role-based dashboard redirects handled in AuthController
});
```

## Controller: AuthController

### Methods
| Method | Route | Description |
|--------|-------|-------------|
| `showLoginForm()` | GET /login | Display login form |
| `login()` | POST /login | Process login credentials |
| `logout()` | POST /logout | Logout user |
| `showForgotPasswordForm()` | GET /forgot-password | Display forgot password form |
| `sendResetLinkEmail()` | POST /forgot-password | Send reset link email |
| `showResetForm($token)` | GET /reset-password/{token} | Display reset form |
| `reset()` | POST /reset-password | Process password reset |

### Login Logic
```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'remember' => 'boolean',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return $this->redirectToDashboard(Auth::user());
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
}
```

### Role-Based Redirection
```php
protected function redirectToDashboard(User $user)
{
    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'manajerial' => redirect()->route('manajerial.dashboard'),
        'pengawas' => redirect()->route('pengawas.dashboard'),
        'kepala_dapur' => redirect()->route('kepala-dapur.dashboard'),
        default => redirect()->route('dashboard'),
    };
}
```

## Views

### Login Form (`resources/views/auth/login.blade.php`)
- **Eye Icon Toggle**: Alpine.js `x-show` for password visibility
- **Loading State**: Button shows spinner during submission
- **Transitions**: `x-transition` for smooth show/hide
- **Accessibility**: Proper labels, ARIA attributes

```blade
<!-- Password field with toggle -->
<div x-data="{ show: false }" class="relative">
    <input 
        type="password" 
        :type="show ? 'text' : 'password'" 
        class="input-field pr-12"
        id="password" 
        name="password"
    >
    <button 
        type="button" 
        @click="show = !show" 
        class="absolute right-4 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] hover:text-[var(--color-primary)]"
        aria-label="Toggle password visibility"
    >
        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7M9.878 9.878l-3.29-3.29m0 0A9.953 9.953 0 0012 19c-4.478 0-8.268-2.943-9.542-7"/></svg>
    </button>
</div>

<!-- Submit button with loading state -->
<button type="submit" class="btn-primary w-full" :disabled="submitting">
    <span x-show="!submitting" x-transition>Masuk</span>
    <span x-show="submitting" x-transition class="flex items-center justify-center gap-2">
        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
        Memproses...
    </span>
</button>
```

### Forgot Password (`resources/views/auth/forgot-password.blade.php`)
- Email input with validation
- Submit button with loading state
- Back to login link

### Reset Password (`resources/views/auth/reset-password.blade.php`)
- Hidden token field
- Email, password, password_confirmation fields
- Password visibility toggles
- Submit with loading state

## Middleware

### `RedirectIfAuthenticated`
Prevents authenticated users from accessing guest routes.

### `VerifyCsrfToken`
CSRF protection on all POST routes.

## Security Features
- **Rate Limiting**: 5 attempts per minute on login
- **Password Hashing**: bcrypt (default Laravel)
- **Session Regeneration**: On login to prevent fixation
- **Remember Token**: Secure, encrypted cookie
- **Email Verification**: Optional (not implemented)

## Testing
```bash
php artisan test --filter=AuthTest
```

### Test Coverage
- Login with valid credentials
- Login with invalid credentials
- Logout functionality
- Password reset request
- Password reset with valid token
- Password reset with invalid token
- Role-based redirection for all 4 roles