<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\Kamar;
use App\Http\Requests\Admin\KamarFormRequest;
use Illuminate\Support\Facades\Storage;

class KamarController extends Controller
{

    public function index()
    {

        view()->share('title','Data Kamar');

        $data = [
            'kamars'        => Kamar::orderBy('kode', 'ASC')->get(),
            'jumlahSampah'  => Kamar::onlyTrashed()->count(),

            // --- LOGIKA GENERATE KODE OTOMATIS ---
            'nextA'         => Kamar::generateNextCode('A-'),
            'nextB'         => Kamar::generateNextCode('B-'),
            'nextC'         => Kamar::generateNextCode('C-'),

        ];

        return view('pages.admins.data-kost.kamar.data-kamar', $data);
    }

    public function store(KamarFormRequest $request)
    {

        $data = $request->validated();

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $path = $request->file('foto')->store('uploads/kamar', 'public');
            $data['foto'] = basename($path);
        }

        Kamar::create($data);

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Kamar telah berhasil ditambahkan!'
        ]);

    }

    public function update(KamarFormRequest $request, $id)
    {
        $kamar = Kamar::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            if ($kamar->foto && Storage::disk('public')->exists('uploads/kamar/' . $kamar->foto)) {
                Storage::disk('public')->delete('uploads/kamar/' . $kamar->foto);
            }

            $path = $request->file('foto')->store('uploads/kamar', 'public');
            $data['foto'] = basename($path);
        }

        $kamar->update($data);

        $icon = $kamar->wasChanged() ? 'success' : 'info';

        return redirect()->back()->with('alert', [
            'icon'  => $icon,
            'title' => 'Kamar telah berhasil diperbarui!'
        ]);

    }

    public function destroy(string $id)
    {
        Kamar::findOrFail($id)->delete();

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Data kamar telah berhasil dihapus. dan dipindahkan ke sampah!'
        ]);
    }

    public function trash()
    {

        view()->share('title','Data kamar');

        $data = [
            'kamars'        => Kamar::onlyTrashed()->latest()->get(),
            'jumlahSampah'  => Kamar::onlyTrashed()->count(),
        ];

        return view('pages.admins.data-kost.kamar.sampah-kamar', $data);
    }

    public function restore(string $id)
    {
        Kamar::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Data kamar telah berhasil dikembalikan!'
        ]);
    }

    public function forceDelete(string $id)
    {
        Kamar::onlyTrashed()->findOrFail($id)->forceDelete();

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Data kamar Dihapus permanen dan tidak dapat dikembalikan!',
        ]);
    }

}
