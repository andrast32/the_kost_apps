<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\UserFormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class PenyewaController extends Controller
{

    public function index()
    {

        view()->share('title', 'Data Penyewa');

        $data = [
            'users'         => User::where('role', 'User')->orderBy('name', 'ASC')->get(),
            'jumlahSampah'  => User::where('role', 'User')->onlyTrashed()->count(),
        ];

        return view('pages.admins.data-user.penyewa.data-penyewa', $data);

    }

    public function trash()
    {
        view()->share('title', 'Sampah Data Penyewa');

        $data = [
            'users'         => User::where('role', 'User')->onlyTrashed()->latest()->get(),
            'jumlahSampah'  => User::where('role', 'User')->onlyTrashed()->count(),
        ];

        return view('pages.admins.data-user.penyewa.sampah-penyewa', $data);
    }

    public function show(User $user)
    {

        view()->share('title', 'Biodata Penyewa');

        return view('pages.admins.data-user.penyewa.biodata.data-bio', compact('user'));
    }

    public function laporan()
    {

        view()->share('title', 'Laporan Data Penyewa');

        $data = [
            'users'         => User::where('role', 'User')->orderBy('name', 'ASC')->get()
        ];

        return view('pages.admins.data-user.penyewa.lap-penyewa', $data);

    }

    public function store(UserFormRequest $request)
    {
        try {

            $name               = trim($request->name);
            $parts              = explode(' ', $name);
            $first              = Str::slug($parts[0], '');
            $last               = (count($parts) > 1) ? Str::slug(end($parts), '') : $first;
            $defaultPassword    = '4n4k_k0st.2026';

            $lastUser = User::where('role', 'User')
            ->withTrashed()
            ->whereBetween('id', [1000000, 1999999])
            ->orderBy('id', 'desc')
            ->first();

            $newId = $lastUser ? ($lastUser->id + 1) : 1000000;

            if ($newId > 1999999) {
                throw new Exception("Kapasitas penyewa sudah penuh!");
            }

            do {
                $random = rand(1, 999);
                $autoEmail = $first . $last . $random . '@kost.com';
                $exists = User::where('email', $autoEmail)->exists();
            } while ($exists);

            User::create([
                'id'       => $newId,
                'name'     => $name,
                'email'    => $autoEmail,
                'password' => Hash::make($defaultPassword),
                'role'     => 'User'
            ]);

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Penyewa berhasil ditambahkan.'
            ]);

        } catch (Exception $e) {
            Log::error("Gagal tambah penyewa: " . $e->getMessage());
            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => $e->getMessage() ?: 'Gagal menambahkan penyewa!',
            ]);
        }
    }

    public function update(string $id)
    {
        try {

            $user = User::findOrFail($id);

            $user->update([
                'password' => Hash::make('4n4k_k0st.2026')
            ]);

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Password Direset menjadi (4n4k_k0st.2026)!'
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
                'title' => 'Penyewa telah dihapus dan dipindahkan ke sampah.'
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
            User::where('role', 'User')->onlyTrashed()->where('id', $id)->restore();
            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Data penyewa berhasil dipulihkan.'
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('alert', [
                'icon' => 'error', 
                'title' => 'Data penyewa gagal dipulihkan.'
            ]);
        }
    }

    public function forceDelete(string $id) 
    {
        try {
            User::where('role', 'User')->onlyTrashed()->where('id', $id)->forceDelete();
            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Data penyewa telah dihapus permanen dan tidak dapat dikembalikan!'
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('alert', [
                'icon' => 'error', 
                'title' => 'Gagal menghapus permanen.'
            ]);
        }
    }

}
