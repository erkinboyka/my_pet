<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Список всех пользователей.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        if ($search = trim((string) $request->query('q'))) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        return view('admin.users.index', [
            'users' => $query->orderByDesc('created_at')->paginate(20)->withQueryString(),
        ]);
    }

    /**
     * Заблокировать / разблокировать пользователя.
     */
    public function toggleBlock(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Нельзя заблокировать самого себя.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with(
            'success',
            $user->is_active
                ? "Пользователь «{$user->name}» разблокирован."
                : "Пользователь «{$user->name}» заблокирован."
        );
    }

    /**
     * Изменить роль пользователя.
     */
    public function changeRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,moderator,client'],
        ]);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Нельзя изменить роль самого себя.');
        }

        $user->update(['role' => $data['role']]);

        // Снимаем назначенные лоты, если человек больше не модератор
        if ($data['role'] !== 'moderator') {
            Auction::where('moderator_id', $user->id)->update(['moderator_id' => null]);
        }

        return back()->with('success', "Роль пользователя «{$user->name}» изменена: {$data['role']}.");
    }

    /**
     * Удалить пользователя (только если у него нет ставок и лотов).
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Нельзя удалить самого себя.');
        }

        if ($user->isAdmin()) {
            return back()->with('error', 'Администраторов удалять нельзя.');
        }

        if ($user->bids()->exists() || $user->createdAuctions()->exists()) {
            return back()->with('error', 'У пользователя есть история (ставки или лоты). Используйте блокировку вместо удаления.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Пользователь «{$name}» удалён.");
    }
}
