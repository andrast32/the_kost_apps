<x-admin-layout>

    <div class="card card-outline card-primary">

        <div class="card-header">
            <div class="d-flex align-items0center justify-content-between">

                <h3 class="card-title">
                    <span class="fw-bold">Profile lengkap</span>
                    <span class="fw-light">{{ $user->name }}</span>
                </h3>

                <a href="{{ route('data-user.penyewa.index') }}" class="btn btn-sm btn-outline-info right ml-auto">
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
                    <button class="btn btn-link text-primary float-right" data-toggle="modal" data-target="#edit-{{ $user->biodata->id }}">
                        <i class="fas fa-edit"></i> Edit Data
                    </button>

                    <button class="btn btn-link text-danger float-right" onclick="Delete({{ $user->biodata->id }}, '{{ $user->name }}')">
                        <i class="fas fa-trash"></i> Hapus Data
                    </button>

                    <form id="delete-{{ $user->biodata->id }}" action="{{ route('data-user.biodata.destroy', $user->biodata->id) }}" method="post">
                        @csrf @method('DELETE')
                    </form>

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

<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah biodata untuk {{ $user->name }}</h5>
                <button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <form action="{{ route('data-user.biodata.store', $user->id) }}" method="post" enctype="multipart/form-data">

                @csrf

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No. Hp (WA) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                    <input type="string" name="no_hp" class="form-control" placeholder="Masukan nomor hp atau whatsapp" required min="10">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                        <option value="Laki-Laki"></i> Laki-Laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label>Foto</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-camera"></i></span>
                                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(this, 'preview-add')">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Preview</label>
                                <div class="input-group">
                                    <img id="preview-add"
                                        src=""
                                        style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd; padding: 5px; display: none;"
                                        class="img-thumbnail"
                                    >
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pekerjaan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" name="pekerjaan" class="form-control" placeholder="masukan pekerjaan">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Alamat</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                                    <textarea name="alamat" rows="3" style="resize: none" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="reset">Reset</button>
                    <button class="btn btn-outline-success" type="submit">Submit</button>
                </div>

            </form>

        </div>
    </div>
</div>

@if ($user->biodata)
    <div class="modal fade" id="edit-{{ $user->biodata->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        <span class="fw-mediumbold">Rubah biodata</span>
                        <span class="fw-light">{{ $user->name }}</span>
                    </h5>

                    <button class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <form action="{{ route('data-user.biodata.update', $user->biodata->id) }}" method="post" enctype="multipart/form-data">

                    @csrf 
                    @method('PUT')

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. Hp (WA) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                        <input type="string" name="no_hp" class="form-control" value="{{ $user->biodata->no_hp }}" placeholder="Masukan nomor hp atau whatsapp" required min="10">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                        <select name="jenis_kelamin" class="form-control">
                                            <option value="" disabled>Pilih Jenis Kelamin</option>
                                            <option value="Laki-Laki" {{ $user->biodata->jenis_kelamin == 'Laki-Laki' ? 'selected' : '' }} ></i> Laki-Laki</option>
                                            <option value="Perempuan" {{ $user->biodata->jenis_kelamin == 'Perempuan' ? 'selected' : '' }} >Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label>Foto</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-camera"></i></span>
                                        <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(this, 'preview-{{ $user->biodata->foto }}')">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Preview</label>
                                    <div class="input-group">
                                        <img id="preview-{{ $user->biodata->foto }}"
                                            src="{{ $user->biodata->foto ? asset('storage/uploads/biodata/' . $user->biodata->foto) : '' }}"
                                            style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd; padding: 5px; {{ $user->biodata->foto ? 'display: block;' : 'display: none;' }}"
                                            class="img-thumbnail"
                                        >
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pekerjaan</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                        <input type="text" name="pekerjaan" class="form-control" value="{{ $user->biodata->pekerjaan }}" placeholder="masukan pekerjaan">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                                        <textarea name="alamat" rows="3" style="resize: none" class="form-control">{{ $user->biodata->alamat }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" type="reset">Reset</button>
                        <button class="btn btn-outline-success" type="submit">Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endif

<script>
    function Delete(id, name) {
        Swal.fire({
            title: 'Hapus data ini?',
                html: `Biodata <b>${name}</b> akan dihapus<br>
                    dan tidak dapat dikembalikan lagi!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-' + id).submit();
            }
        })
    }
</script>