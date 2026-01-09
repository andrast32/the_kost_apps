<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\UserFormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class PetugasController extends Controller
{
    
    public function index()
    {
        view()->share('title', 'Data Petugas');

        $data = [
            'users'         => User::where('role', 'Admin')->orderBy('name', 'ASC')->get(),
            'jumlahSampah'  => User::where('role', 'Admin')->onlyTrashed()->count(),
        ];

        return view('pages.admins.data-user.petugas.data-petugas', $data);
    }

    public function trash()
    {
        view()->share('title', 'Sampah Data Petugas');

        $data = [
            'users'         => User::where('role', 'Admin')->onlyTrashed()->latest()->get(),
            'jumlahSampah'  => User::where('role', 'Admin')->onlyTrashed()->count(),
        ];

        return view('pages.admins.data-user.petugas.sampah-petugas', $data);
    }

    public function store(UserFormRequest $request)
    {
        try {

            $name               = trim($request->name);
            $parts              = explode(' ', $name);
            $first              = Str::slug($parts[0], '');
            $last               = (count($parts) > 1) ? Str::slug(end($parts), '') : $first;
            $defaultPassword    = 'admin_4n4k_k0st.2026';

            $lastUser = User::where('role', 'Admin')
            ->withTrashed()
            ->whereBetween('id', [1, 999999])
            ->orderBy('id', 'desc')
            ->first();

            $newId = $lastUser ? ($lastUser->id + 1) : 1;

            if ($newId > 999999) {
                throw new Exception("Kapasitas admin sudah penuh!");
            }

            do {
                $random = rand(1, 999);
                $autoEmail = $first . $random . '@admin_kost.com';
                $exists = User::where('email', $autoEmail)->exists();
            } while ($exists);

            User::create([
                'id'        => $newId,
                'name'      => $name,
                'email'     => $autoEmail,
                'password'  => Hash::make($defaultPassword),
                'role'      => 'Admin'
            ]);

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Petugas berhasil ditambahkan.'
            ]);

        } catch (Exception $e) {
            Log::error("Gagal tambah petugas: " . $e->getMessage());
            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => $e->getMessage() ?: 'Gagal menambahkan petugas!',
            ]);
        }
    }

    public function update(Request $request, string $id)
    {
        try {

            $user = User::findOrFail($id);

            $user->update([
                'password' => Hash::make('admin_4n4k_k0st.2026')
            ]);

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Password direset menjadi (admin_4n4k_k0st.2026)!'
            ]);

        } catch (Exception $e) {
            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal reset password!',
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {

            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Petugas telah dihapus dan dipindahkan ke sampah.'
            ]);

        } catch (Exception $e) {
            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal menghapus data!',
            ]);
        }
    }

    public function restore(string $id)
    {
        try {
            User::where('role', 'Admin')->onlyTrashed()->where('id', $id)->restore();
            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Data petugas berhasil dipulihkan.'
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('alert', [
                'icon' => 'error', 
                'title' => 'Data petugas gagal dipulihkan.'
            ]);
        }
    }

    public function forceDelete(string $id)
    {
        try {
            User::where('role', 'Admin')->onlyTrashed()->where('id', $id)->forceDelete();
            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Data petugas telah dihapus permanen dan tidak dapat dikembalikan!'
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('alert', [
                'icon' => 'error', 
                'title' => 'Gagal menghapus permanen.'
            ]);
        }
    }

}
