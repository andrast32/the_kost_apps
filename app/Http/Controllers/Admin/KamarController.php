<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KamarController extends Controller
{

    public function index()
    {
        view()->share('title', 'Data Kamar');

        $kamars = Kamar::latest()->get();

        $jumlahSampah = Kamar::onlyTrashed()->count();

        return view('pages.admins.data-kost.kamar.data-kamar', compact('kamars', 'jumlahSampah'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'kode_kamar'    => 'required|unique:kamars,kode_kamar|max:50',
            'deskripsi'     => 'nullable|string',
            'harga'         => 'required|numeric|min:0',
            'status'        => 'required|in:Kosong,Terisi,Dalam Perbaikan',
            'khusus'        => 'required|in:Laki-Laki,Perempuan,Keluarga',
            'foto'          => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('uploads.kamar', 'public');
        }

        Kamar::create([
            'kode_kamar'    => $request->kode_kamar,
            'slug_kamar'    => Str::slug($request->kode_kamar, '-'),
            'deskripsi'     => $request->deskripsi,
            'harga'         => str_replace('Rp,', '.', '', $request->harga),
            'status'        => $request->status,
            'khusus'        => $request->khusus,
            'foto'          => $fotoPath,
        ]);

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Data kamar telah berhasil ditambahkan!'
        ]);

    }
    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        $request->validate([
            'kode_kamar'    => 'required|max:50|unique:kamars,kode_kamar,' . $kamar->id,
            'deskripsi'     => 'nullable|string',
            'harga'         => 'required|numeric|min:0',
            'status'        => 'required|in:Kosong,Terisi,Dalam Perbaikan',
            'khusus'        => 'required|in:Laki-Laki,Perempuan,Keluarga',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($kamar->foto && Storage::disk('public')->exists($kamar->foto)) {
                Storage::disk('public')->delete($kamar->foto);
            }

            $fotoPath = $request->file('foto')->store('uploads.kamar', 'public');
        } else {
            $fotoPath = $kamar->foto;
        }

        $kamar->update([
            'kode_kamar'    => $request->kode_kamar,
            'slug_kamar'    => Str::slug($request->kode_kamar, '-'),
            'deskripsi'     => $request->deskripsi,
            'harga'         => str_replace('Rp,', '.', '', $request->harga),
            'status'        => $request->status,
            'khusus'        => $request->khusus,
            'foto'          => $fotoPath,
        ]);

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Data kamar telah berhasil diperbarui!'
        ]);

    }

    public function destroy(string $id)
    {
        $kamar = Kamar::findOrFail($id);
        $kamar->delete(); // Ini otomatis jadi Soft Delete karena di Model sudah di-setting

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Data kamar telah berhasil dihapus. dan dipindahkan ke sampah!'
        ]);
    }

    public function trash()
    {
        view()->share('title', 'Sampah Kamar');

        $kamars = Kamar::onlyTrashed()->latest()->get();

        return view('pages.admins.data-kost.kamar.sampah-kamar', compact('kamars'));
    }

    public function restore(string $id)
    {
        $kamar = Kamar::onlyTrashed()->findOrFail($id);
        $kamar->restore();

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Data kamar telah berhasil dikembalikan!'
        ]);
    }

    public function forceDelete(string $id)
    {
        $kamar = Kamar::onlyTrashed()->findOrFail($id);

        if ($kamar->foto && Storage::disk('public')->exists($kamar->foto)) {
            Storage::disk('public')->delete($kamar->foto);
        }

        $kamar->forceDelete();

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Data kamar Dihapus permanen dan tidak dapat dikembalikan!',
        ]);
    }

}
