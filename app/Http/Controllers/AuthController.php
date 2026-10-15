<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Показать форму входа.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Обработать вход пользователя.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Заблокированные пользователи войти не могут
        if (! Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.invalid'),
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Каждая роль попадает "свою" часть сайта
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'moderator' => redirect()->route('moderator.dashboard'),
            default => redirect()->route('auctions.index'),
        };
    }

    /**
     * Показать форму регистрации.
     */
    public function register(): View
    {
        return view('auth.register');
    }

    /**
     * Обработать регистрацию. Новый пользователь всегда client.
     */
    public function storeRegister(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'agree' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => 'client',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('auctions.index')
            ->with('success', __('auth.registered'));
    }

    /**
     * Выйти из аккаунта.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
