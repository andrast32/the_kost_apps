<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admins\Fasilitas;

use App\Http\Requests\Admin\FasilitasFormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class FasilitasController extends Controller
{

    public function index()
    {

        view()->share('title', 'Data Fasilitas');

        $data = [
            'fasilitas'     => Fasilitas::orderBy('stok', 'DESC')->orderBy('kode', 'ASC')->get(),
            'jumlahSampah'  => Fasilitas::onlyTrashed()->count(),

            'nextCode'      => Fasilitas::generateCode('F-'),
        ];

        return view('pages.admins.data-kost.fasilitas.data-fasilitas', $data);
    }

    public function trash()
    {

        view()->share('title','Sampah Fasilitas');

        $data = [
            'fasilitas'         => Fasilitas::onlyTrashed()->latest()->get(),
            'jumlahSampah'      => Fasilitas::onlyTrashed()->count(),
        ];

        return view('pages.admins.data-kost.fasilitas.sampah-fasilitas', $data);

    }

    public function store(FasilitasFormRequest $request)
    {

        try {
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
        } catch (Exception $e) {
            Log::error("Gagal menambah fasilitas: " . $e->getMessage());

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Fasilitas telah gagal ditambahkan!',
                'text'  => 'Terjadi kesalahan pada sistem. Silakan coba lagi.'
            ]);
        }

    }

    public function update(FasilitasFormRequest $request, $id)
    {
        try {
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
        } catch (Exception $e) {
            Log::error("Gagal update Fasilitas ID $id: " . $e->getMessage());

            return redirect()->back()->withInput()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal memperbarui data fasilitas!',
                'text'  => 'Terjadi kesalahan saat menyimpan perubahan.'
            ]);
        }

    }

    public function destroy(string $id)
    {
        try {
            Fasilitas::findOrFail($id)->delete();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Fasilitas telah berhasil dihapus. dan dipindahkan ke sampah!'
            ]);
        } catch (Exception $e) {
            Log::error("Gagal memindahkan fasilitas ID $id: " . $e->getMessage() . "kesampah");

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal menghapus kamar!',
                'text'  => 'Data tidak dapat dipindahkan ke sampah.'
            ]);
        }
    }

    public function restore(string $id)
    {
        try {
            Fasilitas::onlyTrashed()->findOrFail($id)->restore();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Fasilitas telah berhasil dikembalikan!'
            ]);
        } catch (Exception $e) {
            Log::error("Gagal restore fasilitas ID $id: " . $e->getMessage());

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal memulihkan fasilitas!',
                'text'  => 'Terjadi kesalahan saat mengembalikan data.'
            ]);
        }
    }

    public function forceDelete(string $id)
    {
        try {
            Fasilitas::onlyTrashed()->findOrFail($id)->forceDelete();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Fasilitas Dihapus permanen dan tidak dapat dikembalikan!',
            ]);
        } catch (Exception $e) {
            Log::error("Gagal force delete fasilitas ID $id: " . $e->getMessage());

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Gagal menghapus permanen!',
                'text'  => 'Mungkin data sedang terkait dengan transaksi lain.'
            ]);
        }
    }

}
