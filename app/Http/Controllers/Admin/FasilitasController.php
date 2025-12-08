<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\Fasilitas;

// request
use App\Http\Requests\Admin\FasilitasFormRequest;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FasilitasController extends Controller
{
    public function index()
    {

        view()->share('title', 'Data Fasilitas');

        $data = [
            'fasilitas'     => Fasilitas::orderBy('kode', 'ASC')->get(),
            'jumlahSampah'  => Fasilitas::onlyTrashed()->count(),

            'nextCode'      => Fasilitas::generateCode('D-'),
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

    public function update(FasilitasFormRequest $request, $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            if ($fasilitas->foto && Storage::disk('public')->exists('uploads/fasilitas/' . $fasilitas->foto)) {
                Storage::disk('public')->delete('uploads/fasilitas/' . $fasilitas->foto);
            }

            $path = $request->file('foto')->store('uploads/fasilitas', 'public');
            $data['foto'] = basename($path);
        }

        $fasilitas->update($data);

        $icon = $fasilitas->wasChanged() ? 'success' : 'info';

        return redirect()->back()->with('alert', [
            'icon'  => $icon,
            'title' => 'Fasilitas telah berhasil diperbarui!'
        ]);

    }

    public function destroy(string $id)
    {
        Fasilitas::findOrFail($id)->delete();

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Fasilitas telah berhasil dihapus. dan dipindahkan ke sampah!'
        ]);
    }

    public function trash()
    {}

    public function restore(string $id)
    {}

    public function forceDelete(string $id)
    {}

}
