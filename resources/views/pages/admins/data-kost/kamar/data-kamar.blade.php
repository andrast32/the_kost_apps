<x-admin-layout>

    <div class="card">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Manajemen data kamar</h3>

                @if (isset($jumlahSampah) && $jumlahSampah > 0)
                    <a href="{{ route('admin.data-kost.kamar.sampah') }}" class="btn btn-sm btn-outline-danger right ml-auto">
                        <i class="fas fa-trash-alt"></i>
                        Lihat sampah
                        <span class="badge badge-danger ml-1">{{ $jumlahSampah }}</span>
                    </a>
                @endif

                <button type="button" class="btn btn-sm btn-round btn-outline-primary ml-2" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah kamar
                </button>

            </div>
        </div>

        <div class="card-body">
            <table id="data_tombol" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Kode kamar</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Khusus</th>
                        <th>Foto</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($kamars as $data)

                        <tr align="center">

                            <td>{{ $loop->iteration }}</td>

                            <td><strong>#{{ $data->kode }}</strong></td>

                            <td>Rp {{ number_format($data->harga, 0, ',', '.') }}</td>

                            <td>
                                @if ($data->status == 'Kosong')
                                    <span class="badge badge-success">
                                        <i class="fas fa-door-open"></i>
                                        {{ $data->status }}
                                    </span>
                                @elseif ($data->status == 'Terisi')
                                    <span class="badge badge-danger">
                                        <i class="fas fa-door-closed"></i>
                                        {{ $data->status }}
                                    </span>
                                @elseif ($data->status == 'Dalam Perbaikan')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-tools"></i>
                                        {{ $data->status }}
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-ban"></i>Tidak ada status kamar
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($data->khusus == 'Laki-Laki')
                                    <span>
                                        <i class="fas fa-male"></i>
                                        {{ $data->khusus }}
                                    </span>
                                @elseif ($data->khusus == 'Perempuan')
                                    <span>
                                        <i class="fas fa-female"></i>
                                        {{ $data->khusus }}
                                    </span>
                                @elseif ($data->khusus == 'Keluarga')
                                    <span>
                                        <i class="fas fa-users"></i>
                                        {{ $data->khusus }}
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-ban"></i>Tidak ada Khusus kamar
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($data->foto)
                                    <img src="{{ Storage::url('uploads/kamar/' . $data->foto) }}" alt="Foto" width="100px" class="img-thumbnail">
                                @else
                                    <span class="text-muted">Tidak ada foto</span>
                                @endif
                            </td>

                            <td>
                                <div class="btn-group">

                                    <button type="button" class="btn btn-link text-primary" data-toggle="modal" data-target="#modalEdit-{{ $data->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button type="button" class="btn btn-link text-danger" onclick="confirmDelete({{ $data->id }}, '{{ $data->kode }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form
                                        id="delete-form-{{ $data->id }}"
                                        action="{{ route('admin.data-kost.kamar.destroy', $data->id) }}"
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
                                            <span class="fw-mediumbold">Rubah kamar dengan kode</span>
                                            <span class="fw-light">#{{ $data->kode }}</span>
                                        </h5>

                                        <button type="button" class="close" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span>
                                        </button>

                                    </div>

                                    <form action="{{ route('admin.data-kost.kamar.update', $data->id) }}" method="post" enctype="multipart/form-data">

                                        @csrf
                                        @method('PUT')

                                        <div class="modal-body">
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Kamar khusus <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                                            <select name="khusus" class="form-control" required
                                                                onchange="updateKodeEdit(this, '{{ $data->id }}', '{{ $data->kode }}', '{{ $data->khusus }}')" >
                                                                <option value="Perempuan" {{ $data->khusus == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                                <option value="Laki-Laki" {{ $data->khusus == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                                                <option value="Keluarga" {{ $data->khusus == 'Keluarga' ? 'selected' : '' }}>Keluarga</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Kode Kamar</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                            <input type="text" name="kode" id="kode_edit_{{ $data->id }}" class="form-control" value="{{ $data->kode }}" readonly required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Harga <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp.</span>
                                                            <input type="text" name="harga" class="form-control input-harga" value="{{ number_format($data->harga, 0, ',', '.') }}" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Status <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                                            <select name="status" class="form-control" required>
                                                                <option value="Kosong" {{ $data->status == 'Kosong' ? 'selected' : '' }}>Kosong</option>
                                                                <option value="Terisi" {{ $data->status == 'Terisi' ? 'selected' : '' }}>Terisi</option>
                                                                <option value="Dalam Perbaikan" {{ $data->status == 'Dalam Perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">

                                                    <div class="form-group">
                                                        <label>Foto Kamar</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-camera"></i></span>
                                                            <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(this, 'preview-{{ $data->id }}')">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Preview</label>
                                                        <div class="input-group">
                                                            <img id="preview-{{ $data->id }}"
                                                                src="{{ $data->foto ? asset('storage/uploads/kamar/' . $data->foto) : '' }}"
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
                                                            <textarea name="deskripsi" class="form-control" rows="3" style="resize: none">{{ $data->deskripsi }}</textarea>
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
                                <p class="text-muted">Belum ada kamar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Kode kamar</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Khusus</th>
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
                    <h5 class="modal-title" id="modalTambahLabel">Tambah kamar baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <form action="{{ route('admin.data-kost.kamar.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kamar khusus <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                        <select name="khusus" id="khusus_tambah" class="form-control" required>
                                            <option value="" disabled selected>Pilih kategori kamar</option>
                                            <option value="Perempuan">Perempuan</option>
                                            <option value="Laki-Laki">Laki-Laki</option>
                                            <option value="Keluarga">Keluarga</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Kamar</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="text" name="kode" id="kode_tambah" class="form-control" placeholder="kode otomatis muncul setelah pilih kategori" readonly required>
                                    </div>
                                    <small id="info_kode" class="text-muted">Pilih kategori kamar dulu!</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Harga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text" name="harga" class="form-control input-harga" placeholder="Masukan harga" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                        <select name="status" class="form-control" required>
                                            <option value="" selected disabled>Pilih Status Kamar</option>
                                            <option value="Kosong">Kosong</option>
                                            <option value="Terisi">Terisi</option>
                                            <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">

                                <div class="form-group">
                                    <label>Foto Kamar</label>
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

    <script>

        const nextCodes = {
            'Perempuan' : "{{ $nextA ?? '' }}",
            'Laki-Laki' : "{{ $nextB ?? '' }}",
            'Keluarga'  : "{{ $nextC ?? '' }}"
        };

        if(document.getElementById('khusus_tambah')){
            document.getElementById('khusus_tambah').addEventListener('change', function() {
                let kategori    = this.value;
                let inputKode   = document.getElementById('kode_tambah');
                let info        = document.getElementById('info_kode');

                if (nextCodes[kategori]) {
                    inputKode.value = nextCodes[kategori];
                    info.innerText = "Kode urut otomatis: " + nextCodes[kategori];
                    info.className = "text-success";
                } else {
                    inputKode.value = "";
                    info.innerText = "Silahkan pilih kategori!";
                }

            });
        }

        function updateKodeEdit(selectElement, id, originalKode, originalKategori) {
            let selectedKategori = selectElement.value;
            let inputKode = document.getElementById('kode_edit_' + id);

            if (selectedKategori === originalKategori) {
                inputKode.value = originalKode;
            } else {
                if (nextCodes[selectedKategori]) {
                    inputKode.value = nextCodes[selectedKategori];
                }
            }

        }

        function confirmDelete(id, kode) {
            Swal.fire({
                title: 'Hapus kamar dengan kode #' + kode + '?',
                text: "Kamar akan dipindahkan ke sampah!",
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

</x-admin-layout>
