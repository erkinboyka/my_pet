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
    // все пользователи сайта
    public function index(Request $request): View
    {
        $query = User::query();

        $search = trim((string) $request->query('q'));
        if ($search) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $role = $request->query('role');
        if ($role) {
            $query->where('role', $role);
        }

        return view('admin.users.index', [
            'users' => $query->orderByDesc('created_at')->paginate(20)->withQueryString(),
        ]);
    }

    // заблокировать / разблокировать
    public function toggleBlock(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('admin.flash.selfBlock'));
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with(
            'success',
            $user->is_active
                ? __('admin.flash.userUnblocked', ['name' => $user->name])
                : __('admin.flash.userBlocked', ['name' => $user->name])
        );
    }

    // поменять роль
    public function changeRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,moderator,client'],
        ]);

        if ($user->id === auth()->id()) {
            return back()->with('error', __('admin.flash.selfRole'));
        }

        $user->update(['role' => $data['role']]);

        // снимаем назначенные лоты, если человек больше не модератор
        if ($data['role'] !== 'moderator') {
            Auction::where('moderator_id', $user->id)->update(['moderator_id' => null]);
        }

        return back()->with('success', __('admin.flash.roleChanged', ['name' => $user->name, 'role' => $data['role']]));
    }

    // удалить юзера, но только если у него нет ставок и лотов
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('admin.flash.selfDelete'));
        }

        if ($user->isAdmin()) {
            return back()->with('error', __('admin.flash.adminDelete'));
        }

        if ($user->bids()->exists() || $user->createdAuctions()->exists()) {
            return back()->with('error', __('admin.flash.hasHistory'));
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', __('admin.flash.userDeleted', ['name' => $name]));
    }
}
