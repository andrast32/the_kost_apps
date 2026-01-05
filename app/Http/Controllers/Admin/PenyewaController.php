<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\UserFormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
            $nameParts = explode(' ', trim($request->name));
            $firstName = Str::slug($nameParts[0], '');
        } catch (\Throwable $th) {
            //throw $th;
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
