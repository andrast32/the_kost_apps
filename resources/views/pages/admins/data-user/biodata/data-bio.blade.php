<x-admin-layout title="Biodata Penyewa">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Profil Lengkap: {{ $user->name }}</h3>
                    </div>
                    <div class="card-body">
                        @if(!$user->biodata)
                            <div class="alert alert-info text-center">
                                <h5><i class="icon fas fa-info"></i> Belum Ada Data!</h5>
                                <p>Penyewa ini belum mengisi biodata lengkap.</p>
                                <button class="btn btn-success" data-toggle="modal" data-target="#modalTambah">
                                    <i class="fas fa-plus"></i> Tambah Biodata
                                </button>
                            </div>
                        @else
                            <table class="table table-striped">
                                <tr>
                                    <th width="30%">No. Handphone (WA)</th>
                                    <td>{{ $user->biodata->no_hp }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td>{{ $user->biodata->jenis_kelamin }}</td>
                                </tr>
                                <tr>
                                    <th>Pekerjaan</th>
                                    <td>{{ $user->biodata->pekerjaan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat Asal</th>
                                    <td>{{ $user->biodata->alamat ?? '-' }}</td>
                                </tr>
                            </table>

                            <div class="mt-4">
                                <button class="btn btn-warning" data-toggle="modal" data-target="#modalEdit">
                                    <i class="fas fa-edit"></i> Edit Data
                                </button>
                                
                                <form action="{{ route('biodata.destroy', $user->biodata->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger" onclick="return confirm('Apakah anda yakin ingin menghapus biodata ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                                <a href="{{ route('penyewa.index') }}" class="btn btn-secondary float-right">Kembali</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah">
        <div class="modal-dialog">
            <form action="{{ route('admin.data-user.biodata.store', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header"><h5>Tambah Biodata</h5></div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>id user</label>
                            <input type="text" name="user_id" class="form-control" value="{{ $user->id }}" readonly required>
                        </div>
                        <div class="form-group">
                            <label>No HP</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="Laki-Laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-primary" type="submit">Simpan Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($user->biodata)
    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog">
            <form action="{{ route('biodata.update', $user->biodata->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header"><h5>Edit Biodata</h5></div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>No HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ $user->biodata->no_hp }}" required>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="Laki-Laki" {{ $user->biodata->jenis_kelamin == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="Perempuan" {{ $user->biodata->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" value="{{ $user->biodata->pekerjaan }}">
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ $user->biodata->alamat }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</x-admin-layout>