<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\Kamar;
use App\Http\Requests\Admin\KamarFormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

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
        try {

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

        } catch (Exception $e) {

            Log::error("Gagal tambah kamar: " . $e->getMessage());

            return redirect()->back()->withInput()->with('alert', [
                'icon'  => 'error',
                'title' => 'Kamar telah gagal ditambahkan!',
                'text'  => 'Terjadi kesalahan pada sistem. Silakan coba lagi.'
            ]);
        }

    }

    public function update(KamarFormRequest $request, $id)
    {
        try {
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
        } catch (Exception $e) {
            Log::error("Gagal update kamar ID $id: " . $e->getMessage());

            return redirect()->back()->withInput()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal memperbarui data kamar!',
                'text'  => 'Terjadi kesalahan saat menyimpan perubahan.'
            ]);
        }

    }

    public function destroy(string $id)
    {
        try {
            Kamar::findOrFail($id)->delete();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'kamar telah berhasil dihapus. dan dipindahkan ke sampah!'
            ]);
        } catch (Exception $e) {
            Log::error("Gagal hapus (soft) kamar ID $id: " . $e->getMessage());

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal menghapus kamar!',
                'text'  => 'Data tidak dapat dipindahkan ke sampah.'
            ]);
        }

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
        try {
            Kamar::onlyTrashed()->findOrFail($id)->restore();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'kamar telah berhasil dikembalikan!'
            ]);
        } catch (Exception $e) {
            Log::error("Gagal restore kamar ID $id: " . $e->getMessage());

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal memulihkan kamar!',
                'text'  => 'Terjadi kesalahan saat mengembalikan data.'
            ]);
        }
    }

    public function forceDelete(string $id)
    {
        try {
            Kamar::onlyTrashed()->findOrFail($id)->forceDelete();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'kamar Dihapus permanen dan tidak dapat dikembalikan!',
            ]);
        } catch (Exception $e) {
            Log::error("Gagal force delete kamar ID $id: " . $e->getMessage());

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal menghapus permanen!',
                'text'  => 'Mungkin data sedang terkait dengan transaksi lain.'
            ]);
        }
    }

}
