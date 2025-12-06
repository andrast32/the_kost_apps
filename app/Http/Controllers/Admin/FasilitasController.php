<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\Fasilitas;
use App\http\Requests\Admin\FasilitasFormRequest;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    public function index()
    {

        view()->share('title', 'Data Fasilitas');

        $data = [
            'fasilitas'     => Fasilitas::orderBy('kode', 'ASC')->get(),
            'jumlahSampah'  => Fasilitas::onlyTrashed()->count(),

            'nextCode'      => Fasilitas::generateCode('F-'),
        ];

        return view('pages.admins.data-kost.fasilitas.data-fasilitas', $data);
    }

    public function store(FasilitasFormRequest $request)
    {

        $data = $request->validated();

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $path = $request->file('foto')->store('uploads/fasilitas', 'public');
            $data['foto'] = basename($path);
        }

        Fasilitas::create($data);

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Fasilitas telah berhasil ditambahkan!'
        ]);

    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function trash()
    {}

    public function restore(string $id)
    {}

    public function forceDelete(string $id)
    {}


}
