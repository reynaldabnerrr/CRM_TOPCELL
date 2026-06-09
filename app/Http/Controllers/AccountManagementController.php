<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountManagementController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'superadmin')->orderBy('name')->get();
        return view('account-management.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::min(6)],
        ]);

        User::create([
            'name'                 => $request->name,
            'email'                => $request->email,
            'password'             => Hash::make($request->password),
            'role'                 => 'user',
            'can_access_followup'  => $request->boolean('can_access_followup', true),
            'can_access_aftercare' => $request->boolean('can_access_aftercare', true),
        ]);

        return redirect()->route('account-management.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('account-management.index')
                ->with('error', 'Akun superadmin tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->route('account-management.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    public function toggleFollowup(User $user)
    {
        if ($user->isSuperAdmin()) {
            return response()->json(['error' => 'Tidak dapat mengubah akses superadmin.'], 403);
        }

        $user->update(['can_access_followup' => !$user->can_access_followup]);

        return response()->json([
            'success' => true,
            'value'   => $user->can_access_followup,
        ]);
    }

    public function toggleAftercare(User $user)
    {
        if ($user->isSuperAdmin()) {
            return response()->json(['error' => 'Tidak dapat mengubah akses superadmin.'], 403);
        }

        $user->update(['can_access_aftercare' => !$user->can_access_aftercare]);

        return response()->json([
            'success' => true,
            'value'   => $user->can_access_aftercare,
        ]);
    }
}
