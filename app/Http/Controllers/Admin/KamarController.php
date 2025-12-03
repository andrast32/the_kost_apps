<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KamarController extends Controller
{

    public function generateNextCode($lastCode, $prefix)
    {

        if (!$lastCode) {
            return $prefix . '0001';
        }

        $number = intval(substr($lastCode, 2));
        return $prefix . str_pad($number + 1, 4, '0', STR_PAD_LEFT);

    }

    public function index()
    {
        view()->share('title', 'Data Kamar');
        $kamars = Kamar::orderBy('kode', 'ASC')->get();
        $jumlahSampah = Kamar::onlyTrashed()->count();

        // --- LOGIKA GENERATE KODE OTOMATIS ---

        $lastA = Kamar::where('kode', 'like', 'A-%')->orderBy('kode', 'desc')->value('kode');
        $nextA = $this->generateNextCode($lastA, 'A-');

        $lastB = Kamar::where('kode', 'like', 'B-%')->orderBy('kode', 'desc')->value('kode');
        $nextB = $this->generateNextCode($lastB, 'B-');

        $lastC = Kamar::where('kode', 'like', 'C-%')->orderBy('kode', 'desc')->value('kode');
        $nextC = $this->generateNextCode($lastC, 'C-');

        return view('pages.admins.data-kost.kamar.data-kamar', compact('kamars', 'jumlahSampah', 'nextA', 'nextB', 'nextC'));
    }

    public function store(Request $request)
    {

        $hargaBersih = str_replace('.', '', $request->harga);
        $request->merge(['harga' => $hargaBersih]);

        $request->validate([
            'kode'          => 'required|unique:kamars,kode|max:50',
            'deskripsi'     => 'nullable|string',
            'harga'         => 'required|numeric|min:0',
            'status'        => 'required|in:Kosong,Terisi,Dalam Perbaikan',
            'khusus'        => 'required|in:Laki-Laki,Perempuan,Keluarga',
            'foto'          => 'nullable|image|max:10240', // Maksimal 10MB
        ]);

        $fotoName = null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $path = $request->file('foto')->store('uploads/kamar', 'public');
            $fotoName = basename($path);
        }

        $Kamar = Kamar::create([
            'kode'          => $request->kode,
            'slug'          => str::slug($request->kode),
            'deskripsi'     => $request->deskripsi,
            'harga'         => $request->harga,
            'status'        => $request->status,
            'khusus'        => $request->khusus,
            'foto'          => $fotoName,
        ]);

        $Kamar->update([
            'slug'    => Str::slug($request->kode . ' ' . $request->khusus . '' . $request->id, '-')
        ]);

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Data kamar telah berhasil ditambahkan!'
        ]);

    }

    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        $hargaBersih = str_replace('.', '', $request->harga);
        $request->merge(['harga' => $hargaBersih]);

        $request->validate([
            'kode'          => 'required|max:50|unique:kamars,kode,' . $kamar->id,
            'deskripsi'     => 'nullable|string',
            'harga'         => 'required|numeric|min:0',
            'status'        => 'required|in:Kosong,Terisi,Dalam Perbaikan',
            'khusus'        => 'required|in:Laki-Laki,Perempuan,Keluarga',
            'foto'          => 'nullable|image|max:10240', // Maksimal 10MB
        ]);

        $fotoName = $kamar->foto;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            if ($kamar->foto && Storage::disk('public')->exists('uploads/kamar/' . $kamar->foto)) {
                Storage::disk('public')->delete('uploads/kamar/' . $kamar->foto);
            }
            $path = $request->file('foto')->store('uploads/kamar', 'public');
            $fotoName = basename($path);
        }

        $kamar->fill([
            'kode'          => $request->kode,
            'slug'          => Str::slug($request->kode . ' ' . $request->khusus . ' ' . $kamar->id, '-'),
            'deskripsi'     => $request->deskripsi,
            'harga'         => $request->harga,
            'status'        => $request->status,
            'khusus'        => $request->khusus,
            'foto'          => $fotoName,
        ]);

        if ($kamar->isDirty()) {
            $kamar->save();
            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Data kamar telah berhasil diperbarui!'
            ]);
        } else {
            return redirect()->back()->with('alert', [
                'icon'  => 'info',
                'title' => 'Data kamar telah berhasil diperbarui!'
            ]);
        }

    }

    public function destroy(string $id)
    {
        $kamar = Kamar::findOrFail($id);
        $kamar->delete();

        return redirect()->back()->with('alert', [
            'icon'  => 'success',
            'title' => 'Data kamar telah berhasil dihapus. dan dipindahkan ke sampah!'
        ]);
    }

    public function trash()
    {
        $jumlahSampah = Kamar::onlyTrashed()->count();
        view()->share('title', 'Sampah Kamar');

        $kamars = Kamar::onlyTrashed()->latest()->get();

        return view('pages.admins.data-kost.kamar.sampah-kamar', compact('kamars', 'jumlahSampah'));
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
