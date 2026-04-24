<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->withCount('borrowings');

        if ($request->filled('search')) {
            $search = $request->string('search')->trim();

            $query->where(function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('status')) {
            $query->where('account_status', $request->string('status'));
        }

        match ($request->string('sort', 'latest')->toString()) {
            'name_asc' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $users = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => User::count(),
            'active' => User::where('account_status', 'aktif')->count(),
            'inactive' => User::where('account_status', 'nonaktif')->count(),
            'borrowers' => User::where('role', 'peminjam')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function show(User $user): View
    {
        $user->load([
            'borrowings' => function ($query) {
                $query->with(['book', 'fine'])->latest()->limit(10);
            },
        ]);

        $user->loadCount('borrowings');

        $userStats = [
            'active_borrowings' => $user->borrowings()
                ->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])
                ->count(),
            'unpaid_fines' => $user->fines()
                ->where('fines.status', 'belum_lunas')
                ->sum('fines.amount'),
        ];

        return view('admin.users.show', compact('user', 'userStats'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($user->id === auth()->id() && $data['account_status'] === 'nonaktif') {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        if ($user->isAdmin() && $user->role !== $data['role'] && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Admin terakhir tidak boleh diturunkan rolenya.');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah status akun sendiri dari tabel.');
        }

        $targetStatus = $user->account_status === 'aktif' ? 'nonaktif' : 'aktif';

        $user->update([
            'account_status' => $targetStatus,
        ]);

        return back()->with('success', "Status akun {$user->name} diubah menjadi {$targetStatus}.");
    }

    public function resetPassword(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Gunakan halaman profil untuk mengubah password akun sendiri.');
        }

        $temporaryPassword = Str::password(10, true, true, false, false);

        $user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        return back()->with('success', "Password sementara untuk {$user->name}: {$temporaryPassword}");
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'bulk_action' => ['required', 'in:delete,deactivate,activate,change_role'],
            'bulk_role' => ['nullable', 'in:admin,peminjam'],
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();

        if ($users->contains('id', auth()->id())) {
            return back()->with('error', 'Bulk action tidak bisa diterapkan ke akun Anda sendiri.');
        }

        if ($validated['bulk_action'] === 'delete') {
            $adminIds = $users->where('role', 'admin')->pluck('id');

            if ($adminIds->isNotEmpty() && User::where('role', 'admin')->count() === $adminIds->count()) {
                return back()->with('error', 'Admin terakhir tidak boleh dihapus.');
            }

            $hasActiveBorrowings = $users->contains(function (User $user) {
                return $user->borrowings()
                    ->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])
                    ->exists();
            });

            if ($hasActiveBorrowings) {
                return back()->with('error', 'Ada pengguna yang masih memiliki transaksi aktif.');
            }

            foreach ($users as $user) {
                $user->delete();
            }

            return back()->with('success', $users->count() . ' pengguna berhasil dihapus.');
        }

        if ($validated['bulk_action'] === 'change_role') {
            if (! $request->filled('bulk_role')) {
                return back()->with('error', 'Pilih role target terlebih dahulu.');
            }

            if ($validated['bulk_role'] === 'peminjam') {
                $adminIds = $users->where('role', 'admin')->pluck('id');

                if ($adminIds->isNotEmpty() && User::where('role', 'admin')->count() === $adminIds->count()) {
                    return back()->with('error', 'Admin terakhir tidak boleh diturunkan rolenya.');
                }
            }

            User::whereIn('id', $validated['user_ids'])->update([
                'role' => $validated['bulk_role'],
            ]);

            return back()->with('success', 'Role pengguna terpilih berhasil diperbarui.');
        }

        User::whereIn('id', $validated['user_ids'])->update([
            'account_status' => $validated['bulk_action'] === 'activate' ? 'aktif' : 'nonaktif',
        ]);

        return back()->with('success', 'Status pengguna terpilih berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users.index')->with('error', 'Admin terakhir tidak boleh dihapus.');
        }

        $activeBorrowingExists = $user->borrowings()
            ->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])
            ->exists();

        if ($activeBorrowingExists) {
            return redirect()->route('admin.users.index')->with('error', 'Pengguna tidak dapat dihapus karena masih memiliki transaksi aktif.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
