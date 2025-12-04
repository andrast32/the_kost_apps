<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\Fasilitas;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    public function index()
    {

        view()->share('title', 'Data Fasilitas');

        $data = [
            'fasilitas'     => Fasilitas::orderBy('kode', 'ASC')->get(),
            'jumlahSampah'  => Fasilitas::onlyTrashed()->count(),
        ];

        return view('pages.admins.data-kost.fasilitas.data-fasilitas', $data);
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
