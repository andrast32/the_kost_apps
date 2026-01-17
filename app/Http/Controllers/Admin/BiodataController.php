<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admins\Biodata;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;

class BiodataController extends Controller
{

    public function show($slug)
    {
        $user = User::where('slug', $slug)->with('biodata')->firstOrFail();
        return view('pages.admins.data-user.biodata.data-bio', compact('user'));
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'no_hp'         => 'required|max:15',
                'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
                'pekerjaan'     => 'nullable|string|max:255',
                'alamat'        => 'nullable|string',
                'foto'          => 'nullable|image|max:10240'
            ]);

            if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
                $filename = $request->file('foto')->hashName();

                $request->file('foto')->storeAs(
                    'uploads/biodata',
                    $filename,
                    'public'
                );

                $fotoPath = $filename;

            }

            Biodata::create([
                'user_id'       => $request->userId,
                'no_hp'         => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'pekerjaan'     => $request->pekerjaan,
                'alamat'        => $request->alamat,
                'foto'          => $fotoPath
            ]);

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Biodata telah berhasil ditambahkan!'
            ]);

        } catch (Exception $e) {
            Log::error("Gagal menambahkan failitas: " . $e->getMessage());

            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Biodata gagal ditambahkan!'
            ]);
        }
    }

    public function update(Request $request, $id)
    {

        try {

            $biodata = Biodata::findOrFail($id);

            $request->validate([
                'no_hp'         => 'required|max:15',
                'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
                'pekerjaan'     => 'nullable|string|max:255',
                'alamat'        => 'nullable|string',
                'foto'          => 'nullable|image|max:10240'
            ]);

            $data = [
                'no_hp'         => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'pekerjaan'     => $request->pekerjaan,
                'alamat'        => $request->alamat
            ];

            if ($request->hasFile('foto') && $request->file('foto')->isValid()) {

                if ($biodata->foto) {
                    Storage::disk('public')->delete('uploads/biodata/' . $biodata->foto);
                }

                $fotoName = $request->file('foto')->hashName();
                $request->file('foto')->storeAs('uploads/biodata', $fotoName, 'public');

                $data['foto'] = $fotoName;

            }

            $biodata->update($data);

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title'  => 'Biodata berhasil diperbarui!'
            ]);

        } catch (Exception $e) {

            Log::error("Gagal update biodata: " . $e->getMessage());
            
            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Biodata gagal diperbarui!',
            ]);

        }
    }

    public function destroy(string $id)
    {
        try {

            $biodata = Biodata::findOrFail($id);

            if ($biodata->foto && Storage::disk('public')->exists('uploads/biodata/'. $biodata->foto)) {
                Storage::disk('public')->delete('uploads/biodata/'. $biodata->foto);
            }

            $biodata->delete();

            return redirect()->back()->with('alert', [
                'icon'  => 'success',
                'title' => 'Biodata berhasil dihapus!'
            ]);

        } catch (\Throwable $th) {
            Log::error("Gagal hapus biodata: " . $e->getMessage());

            // Redirect dengan pesan error
            return redirect()->back()->with('alert', [
                'icon'  => 'error',
                'title' => 'Terjadi kesalahan saat menghapus data!'
            ]);
        }
    }
}
