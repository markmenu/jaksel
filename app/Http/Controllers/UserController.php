<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Ambil parameter untuk sortir dari request, dengan nilai default
        $sortColumn = $request->input('sort', 'name'); // Default sortir berdasarkan nama
        $sortDirection = $request->input('direction', 'asc'); // Default arah menaik (A-Z)

        // Validasi untuk memastikan hanya kolom yang aman yang bisa disortir
        $allowedSortColumns = ['name', 'email', 'role'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'name';
        }

        $query = User::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        // Terapkan logika orderBy ke query
        $query->orderBy($sortColumn, $sortDirection);

        $users = $query->paginate(10);

        // Kirim semua variabel yang dibutuhkan ke view
        return view('superadmin.users_index', compact('users', 'search', 'sortColumn', 'sortDirection'));
    }

    public function create()
    {
        return view('superadmin.users_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', Rule::in(['superadmin', 'ketua_tim', 'anggota_tim', 'kepala'])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        return view('superadmin.users_show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('superadmin.users_edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['superadmin', 'ketua_tim', 'anggota_tim', 'kepala'])],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('superadmin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Tambahkan logika untuk mencegah user menghapus dirinya sendiri (opsional tapi penting)
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
