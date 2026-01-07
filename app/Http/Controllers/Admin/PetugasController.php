<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Admin\UserFormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
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
