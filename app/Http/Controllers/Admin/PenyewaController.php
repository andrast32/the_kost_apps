<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\UserFormRequest;
use Illuminate\Http\Request;
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
            'jumlahSampah'  => User::onlyTrashed()->count(),
        ];

        return view('pages.admins.data-user.penyewa.data-penyewa', $data);

    }

    public function trash()
    {
        view()->share('title', 'Data Penyewa');

        $data = [
            'users'         => User::onlyTrashed()->latest()->get(),
            'jumlahSampah'  => User::onlyTrashed()->count(),
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
            // 1. Ambil nama dan bersihkan
            $name = trim($request->name);
            $nameParts = explode(' ', $name);
            
            // Ambil nama depan
            $firstName = Str::slug($nameParts[0], '');
            
            // Ambil nama belakang (jika tidak ada, gunakan nama depan)
            $lastName = (count($nameParts) > 1) ? Str::slug(end($nameParts), '') : $firstName;

            $defaultPassword = '4n4k_k0st.2026';

            // 2. Generate email unik
            do {
                $randomNumber = rand(10, 999);
                $autoEmail = $firstName . $lastName . $randomNumber . '@kost.com';
            } while (User::where('email', $autoEmail)->exists());

            // 3. Simpan data
            User::create([
                'name'     => $name,
                'email'    => $autoEmail,
                'password' => Hash::make($defaultPassword),
                'role'     => 'User'
            ]);

            // 4. Kirim data email & password ke view agar bisa ditampilkan/disalin
            return redirect()->back()->with('success_copy', [
                'name'     => $name,
                'email'    => $autoEmail,
                'password' => $defaultPassword,
            ])->with('alert', [
                'icon'  => 'success',
                'title' => 'Penyewa telah berhasil ditambahkan!'
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
