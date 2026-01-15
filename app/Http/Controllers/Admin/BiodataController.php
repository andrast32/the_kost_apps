<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admins\Biodata;
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
                'alamat'        => 'nullable|string'
            ]);

            Biodata::create([
                'user_id'       => $userId,
                'no_hp'         => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'pekerjaan'     => $request->pekerjaan,
                'alamat'        => $request->alamat
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

    public function update(Request $request, Biodata $biodata)
    {
        //
    }

    public function destroy(Biodata $biodata)
    {
        //
    }
}
