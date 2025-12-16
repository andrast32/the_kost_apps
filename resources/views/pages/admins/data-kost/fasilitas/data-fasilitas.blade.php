<x-admin-layout>

    <div class="card">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Manajemen data fasilitas</h3>

                @if (isset($jumlahSampah) && $jumlahSampah > 0)
                    <a href="{{ route('admin.data-kost.fasilitas.sampah') }}" class="btn btn-sm btn-outline-danger right ml-auto">
                        <i class="fas fa-trash-alt"></i>
                        Lihat Sampah
                        <span class="badge badge-danger ml-1">{{ $jumlahSampah }}</span>
                    </a>
                @endif

                <button class="btn btn-sm btn-round btn-outline-primary ml-2" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus"></i>Tambah fasilitas
                </button>

            </div>
        </div>

        <div class="card-body">
            <table id="data_tombol" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Kode fasilitas</th>
                        <th>Nama fasilitas</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Foto</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($fasilitas as $data)

                        <tr align="center">

                            <td>{{ $loop->iteration }}</td>

                            <td><strong>#{{ $data->kode }}</strong></td>

                            <td>{{ $data->nama }}</td>

                            <td>
                                @if ($data->harga >= 5000)
                                    Rp. {{ number_format($data->harga, 0, ',', '.') }}
                                @else
                                    <strong>Gratis.</strong>
                                @endif
                            </td>

                            <td>
                                @if ($data->stok == 0)
                                    <span class="badge badge-danger">Stok habis</span>
                                @elseif ($data->stok <= 5) 
                                    <span> Stok hampir habis, tersisa {{ $data->stok }} lagi</span>
                                @else
                                    <span>{{ $data->stok }}</span>
                                @endif
                            </td>

                            <td>
                                @if ($data->foto)
                                    <img src="{{ Storage::url('uploads/fasilitas/' . $data->foto) }}" alt="Foto fasilitas" width="100px" class="img-thumbnail">
                                @else
                                    <span class="text-muted">Tidak ada foto</span>
                                @endif
                            </td>

                            <td>
                                <div class="btn-group">

                                    <button type="button" class="btn btn-link text-primary" data-toggle="modal" data-target="#modalEdit-{{ $data->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button type="button" class="btn btn-link text-danger" onclick="confirmDelete({{ $data->id }}, '{{ $data->kode }}', '{{ $data->nama }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form
                                        id="delete-form-{{ $data->id }}"
                                        action="{{ route('admin.data-kost.fasilitas.destroy', $data->id) }}"
                                        method="post"
                                        style="display: none"
                                    >
                                        @csrf @method('DELETE')
                                    </form>

                                </div>
                            </td>

                        </tr>

                        <div class="modal fade" id="modalEdit-{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">

                                    <div class="modal-header">

                                        <h5 class="modal-title">
                                            <span class="fw-mediumbold">Rubah Fasilitas dengan kode</span>
                                            <span class="fw-light">#{{ $data->kode }}</span>
                                        </h5>

                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>

                                    </div>

                                    <form action="{{ route('admin.data-kost.fasilitas.update', $data->id) }}" method="post" enctype="multipart/form-data">

                                        @csrf
                                        @method('PUT')

                                        <div class="modal-body">
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nama fasilitas <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-pen"></i></span>
                                                            <input type="text" name="nama" value="{{ $data->nama }}" class="form-control" placeholder="Masukan nama fasilitas" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Kode fasilitas <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input type="text" name="kode" value="{{ $data->kode }}" class="form-control" readonly placeholder="Kode otomatis muncul" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Harga <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp.</span>
                                                            <input type="text" name="harga" value="{{ number_format($data->harga, 0, ',', '.') }}" class="form-control input-harga" placeholder="Masukan harga fasilitas" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Stok <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-box"></i></span>
                                                            <input type="number" min="0" name="stok" value="{{ $data->stok }}" class="form-control" placeholder="masukan stok" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">

                                                    <div class="form-group">
                                                        <label>Foto fasilitas</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-camera"></i></span>
                                                            <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(this, 'preview-{{ $data->id }}')">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Preview</label>
                                                        <div class="input-group">
                                                            <img id="preview-{{ $data->id }}"
                                                                src="{{ $data->foto ? asset('storage/uploads/fasilitas/' . $data->foto) : '' }}"
                                                                style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd; padding: 5px; {{ $data->foto ? 'display: block;' : 'display: none;' }}"
                                                                class="img-thumbnail"
                                                            >
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Deskripsi</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-list"></i></span>
                                                            <textarea name="deskripsi" class="form-control" rows="3" style="resize: none" placeholder="Deskripsi ...">{{ $data->deskripsi }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                            <button type="submit" class="btn btn-outline-success">Submit</button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-4">
                                <i class="far fa-building fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada Fasilitas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Kode fasilitas</th>
                        <th>Nama fasilitas</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Foto</th>
                        <th width="10%">Action</th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Fasilitas Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <form action="{{ route('admin.data-kost.fasilitas.store') }}" method="post" enctype="multipart/form-data">

                    @csrf

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama fasilitas <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-pen"></i></span>
                                        <input type="text" name="nama" class="form-control" placeholder="Masukan nama fasilitas" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode fasilitas <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="text" name="kode" value="{{ $nextCode }}" class="form-control" readonly placeholder="Kode otomatis muncul" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Harga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" name="harga" class="form-control input-harga" placeholder="Masukan harga fasilitas" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Stok <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-box"></i></span>
                                        <input type="number" min="0" name="stok" class="form-control" placeholder="masukan stok" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">

                                <div class="form-group">
                                    <label>Foto fasilitas</label>
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

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                                        <textarea name="deskripsi" class="form-control" rows="3" style="resize: none" placeholder="Deskripsi ..."></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-outline-success">Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</x-admin-layout>

<script>
    function confirmDelete(id, kode, nama) {
            Swal.fire({
                title: 'Hapus fasilitas dengan kode #' + kode + '?',
                text: "Fasilitas " + nama + " akan dipindahkan ke sampah!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
</script>
