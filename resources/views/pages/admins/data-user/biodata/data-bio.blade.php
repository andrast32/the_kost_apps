<x-admin-layout>

    <div class="card card-outline card-primary">

        <div class="card-header">
            <div class="d-flex align-items0center justify-content-between">

                <h3 class="card-title">Profile lengkap {{ $user->name }}</h3>

                <a href="{{ route('admin.data-user.penyewa.index') }}" class="btn btn-sm btn-outline-info right ml-auto">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

            </div>
        </div>

        <div class="card-body">
            @if ($user->biodata)
                <table id="data" class="table table-bordered table-striped table-hover">

                    <tr>
                        <th><i class="fas fa-tag"></i> Nama</th>
                        <td>{{ $user->name }}</td>
                    </tr>

                    <tr>
                        <th><i class="far fa-envelope"></i> Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>

                    <tr>
                        <th><i class="far fa-image"></i> Foto</th>
                        <td>
                            @if ($user->biodata->foto)
                                <img src="{{ Storage::url('uploads/biodata/' . $user->biodata->foto) }}" alt="Foto biodata" class="img-thumbnail" width="100px">
                            @else
                                <span class="text-muted">Tidak ada foto</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th><i class="fab fa-whatsapp"></i> No. Handphone (WA)</th>
                        <td>{{ $user->biodata->no_hp }}</td>
                    </tr>

                    <tr>
                        <th><i class="fas fa-venus-mars"></i> Jenis kelamin</th>
                        <td>

                            @if ($user->biodata->jenis_kelamin == 'Laki-Laki')
                                <span>
                                    <i class="fas fa-mars"></i>
                                    {{ $user->biodata->jenis_kelamin }}
                                </span>

                            @elseif ($user->biodata->jenis_kelamin == 'Perempuan')
                                <span>
                                    <i class="fas fa-venus"></i>
                                    {{ $user->biodata->jenis_kelamin }}
                                </span>

                            @else
                                <span>
                                    <i class="fas fa-genderless"></i>
                                    Jenis kelamin tidak terdaftar
                                </span>

                            @endif

                        </td>
                    </tr>

                    <tr>
                        <th><i class="fas fa-briefcase"></i> Pekerjaan</th>
                        <td>
                            @if ($user->biodata->pekerjaan)
                                {{ $user->biodata->pekerjaan }}
                            @else
                                Tidak punya kerjaan POTENSI NGUTANG TINGGI
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th><i class="fas fa-map-marked-alt"></i> Alamat Asal</th>
                        <td>{{ $user->biodata->alamat }}</td>
                    </tr>

                </table>

                <div class="mt-4">
                    <button class="btn btn-link text-primary float-right" data-toggle="modal" data-target="#edit">
                        <i class="fas fa-edit"></i> Edit Data
                    </button>

                    <button class="btn btn-link text-danger"></button>

                </div>
            @else
                <div class="alert text-center">
                    <h5><i class="icon fas fa-info"></i> Belum ada data</h5>
                    <p>{{ $user->name }} belum mengisi biodata lengkap.</p>
                    <button class="btn btn-outline-info" data-toggle="modal" data-target="#add">
                        <i class="fas fa-plus"></i> Tambahkan Biodata
                    </button>
                </div>
            @endif
        </div>

    </div>

</x-admin-layout>