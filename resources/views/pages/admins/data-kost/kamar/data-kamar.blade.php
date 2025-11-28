<x-admin-layout>

    <div class="card">

        <div class="card-header">
            <div class="d-flex align-items-center">

                <h3 class="card-title">
                    Data Kamar
                </h3>

                <button class="btn btn-border btn-round btn-primary right ml-auto" style="margin: 0 0.5rem;" data-toggle="modal" data-target="#tambah">
                    <i class="fas fa-plus"></i>
                    Tambah {{ $title ?? 'Data' }}
                </button>

                @if ($jumlahSampah >= 0)
                    <a href="{{ route('admin.data-kost.kamar.sampah') }}" class="btn btn-border btn-round btn-danger ml-2">
                        <i class="fas fa-trash"></i>
                        Lihat Sampah
                        <span class="badge badge-danger">
                            {{ $jumlahSampah }}
                        </span>
                    </a>
                @endif

            </div>
        </div>

        <div class="card-body">
            <table id="laporan" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th>No</th>
                        <th>Kode Kamar</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Khusus</th>
                        <th>Foto Kamar</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @if ($kamars->isNotEmpty())
                        @foreach ($kamars as $kamar)

                            <tr align="center">
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $kamar->kode_kamar }}</td>

                                <td>Rp. {{ number_format($kamar->harga, 2, ',', '.' ) }}</td>

                                <td>

                                    @if ($kamar->status == 'Kosong')
                                        <span class="badge badge-success">
                                            {{ $kamar->status }}
                                        </span>

                                    @elseif ($kamar->status == 'Terisi')
                                        <span class="badge badge-danger">
                                            {{ $kamar->status }}
                                        </span>

                                    @elseif ($kamar->status == 'Dalam Perbaikan')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-tools"></i>
                                            {{ $kamar->status }}
                                        </span>

                                    @else
                                        <span class="badge badge-secondary">
                                            {{ $kamar->status }}
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    @if ($kamar->khusus == 'Laki-Laki')
                                        <span>
                                            <i class="fas fa-male"></i>
                                            {{ $kamar->khusus }}
                                        </span>
                                    @elseif ($kamar->khusus == 'Perempuan')
                                        <span>
                                            <i class="fas fa-female"></i>
                                            {{ $kamar->khusus }}
                                        </span>
                                    @else
                                        <span>
                                            <i class="fas fa-users"></i>
                                            {{ $kamar->khusus }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($kamar->foto)
                                        <img src="{{ Storage::url('uploads/kamar/' . $kamar->foto) }}" alt="Foto" width="100px" class="img-thumbnail">
                                    @else
                                        <span class="text-muted">Tidak ada foto</span>
                                    @endif
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-link btn-sm"
                                        data-toggle="modal"
                                        data-target ="#edit-{{ $kamar->id }}"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button
                                        class="btn btn-link btn-sm text-danger"
                                        data-toggle="modal"
                                        data-target ="#hapus-{{ $kamar->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </td>

                            </tr>

                            <div class="modal fade" id="edit-{{ $kamar->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                <span class="fw-mediumbold">
                                                    Edit kamar
                                                </span>
                                                <span class="fw-light">
                                                    {{ $kamar->kode_kamar }}
                                                </span>
                                            </h5>

                                            <button type="button" class="close" data-dismiss="modal">
                                                <span aria-hidden="true">
                                                    &times;
                                                </span>
                                            </button>

                                        </div>

                                        <form action="{{ route('admin.data-kost.kamar.update', $kamar->id) }}" method="post" enctype="multipart/form-data">

                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body">
                                                <div class="row">

                                                    <div class="col-md-6 pe-0">
                                                        <div class="form-group">

                                                            <label for="khusus">
                                                                Kamar Khusus
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="input-group">

                                                                <span class="input-group-text">
                                                                    <i class="fas fa-venus-mars"></i>
                                                                </span>

                                                                <select
                                                                    name="khusus"
                                                                    class="form-control"
                                                                    onchange="updateKodeEdit(this, '{{ $kamar->id }}', '{{ $kamar->kode_kamar }}', '{{ $kamar->khusus }}')"
                                                                    required
                                                                >

                                                                    <option value="" disabled>
                                                                        Kamar Khusus
                                                                    </option>

                                                                    <option value="Perempuan" {{ $kamar->khusus == 'Perempuan' ? 'selected' : '' }}>
                                                                        Perempuan
                                                                    </option>

                                                                    <option value="Laki-Laki" {{ $kamar->khusus == 'Laki-Laki' ? 'selected' : '' }}>
                                                                        Laki Laki
                                                                    </option>

                                                                    <option value="Keluarga" {{ $kamar->khusus == 'Keluarga' ? 'selected' : '' }}>
                                                                        Keluarga
                                                                    </option>

                                                                </select>

                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">

                                                            <label for="kode">
                                                                Kode Kamar
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="input-group">

                                                                <span class="input-group-text">
                                                                    <i class="fas fa-key"></i>
                                                                </span>

                                                                <input
                                                                    type="text"
                                                                    name="kode_kamar"
                                                                    id="kode_kamar_edit_{{ $kamar->id }}"
                                                                    class="form-control"
                                                                    value="{{ $kamar->kode_kamar }}"
                                                                    readonly
                                                                    required
                                                                />

                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 pe-0">
                                                        <div class="form-group">

                                                            <label for="status">
                                                                Status kamar
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="input-group">

                                                                <span class="input-group-text">
                                                                    <i class="fas fa-info-circle"></i>
                                                                </span>

                                                                <select name="status" class="form-control" required>

                                                                    <option value="" disabled>Pilih Status</option>

                                                                    <option value="Kosong" {{ $kamar->status == 'Kosong' ? 'selected' : '' }}>
                                                                        Kosong
                                                                    </option>

                                                                    <option value="Terisi" {{ $kamar->status == 'Terisi' ? 'selected' : '' }}>
                                                                        Terisi
                                                                    </option>

                                                                    <option value="Dalam Perbaikan" {{ $kamar->status == 'Dalam Perbaikan' ? 'selected' : '' }}>
                                                                        Dalam Perbaikan
                                                                    </option>

                                                                </select>

                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">

                                                            <label for="harga">
                                                                Harga kamar
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="input-group">

                                                                <span class="input-group-text">
                                                                    Rp.
                                                                </span>

                                                                <input
                                                                    type="text"
                                                                    name="harga"
                                                                    class="form-control input-harga"
                                                                    value="{{ number_format($kamar->harga, 0, ',', '.') }}"
                                                                    placeholder="0"
                                                                    required
                                                                />

                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 pe-0">
                                                        <div class="form-group">

                                                            <label for="foto">
                                                                Foto Kamar
                                                            </label>

                                                            <div class="input-group">

                                                                <span class="input-group-text">
                                                                    <i class="fas fa-camera"></i>
                                                                </span>

                                                                <input
                                                                    type="file"
                                                                    name="foto"
                                                                    class="form-control"
                                                                    accept="image/*"
                                                                    onchange="previewImage(this, 'preview-{{ $kamar->id }}')"
                                                                />

                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">

                                                            <label for="preview">
                                                                Preview gambar yang di upload
                                                            </label>

                                                            <div class="input-group">

                                                                <span class="input-group-text">
                                                                    <i class="fas fa-image"></i>
                                                                </span>

                                                                <img id="preview-{{ $kamar->id }}"
                                                                    src="{{ $kamar->foto ? Storage::url($kamar->foto) : '' }}"
                                                                    data-original-src="{{ $kamar->foto ? Storage::url($kamar->foto) : '' }}"
                                                                    alt="Preview Foto"
                                                                    style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd; padding: 5px; {{ $kamar->foto ? 'display: block;' : 'display: none;' }}"
                                                                />

                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12">
                                                        <div class="form-group">

                                                            <label>
                                                                Deskripsi Kamar
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="input-group">

                                                                <span class="input-group-text">
                                                                    <i class="fas fa-list"></i>
                                                                </span>

                                                                <textarea name="deskripsi" rows="3" class="form-control" placeholder="Masukan deskripsi kamar" required style="resize: none;">{{ $kamar->deskripsi }}</textarea>

                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <input type="reset" value="Reset" class="btn btn-primary float-right">
                                                <input type="submit" value="Submit" class="btn btn-success float-right">
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="hapus-{{ $kamar->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">

                                        <div class="modal-header"></div>

                                        <div class="modal-body"></div>

                                    </div>
                                </div>
                            </div>

                        @endforeach

                    @else
                        <tr>
                            <td colspan="7" align="center">Data Kamar Tidak Tersedia</td>
                        </tr>
                    @endif
                </tbody>

                <tfoot>
                    <tr align="center">
                        <th>No</th>
                        <th>Kode Kamar</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Kusus</th>
                        <th>Foto Kamar</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>

</x-admin-layout>

<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="fw-mediumbold">Tambah</span>
                    <span class="fw-light">Data Kamar</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('admin.data-kost.kamar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 pe-0">
                            <div class="form-group">

                                <label for="khusus">
                                    Kamar Khusus <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="fas fa-venus-mars"></i>
                                    </span>

                                    <select name="khusus" id="khusus_tambah" class="form-control" required>

                                        <option value="" selected disabled>Pilih kategori</option>

                                        <option value="Perempuan">Perempuan</option>

                                        <option value="Laki-Laki">Laki-Laki</option>

                                        <option value="Keluarga">Keluarga</option>

                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="kode">

                                    Kode Kamar

                                    <span class="text-danger">*</span>

                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input
                                        type="text"
                                        name="kode_kamar"
                                        id="kode_kamar_tambah"
                                        class="form-control"
                                        placeholder="Pilih kategori kamar dulu!"
                                        style="background-color: #e9ecef; cursor: not-allowed;"
                                        required
                                        readonly
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 pe-0">
                            <div class="form-group">
                                <label for="harga">
                                    Harga Kamar <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" name="harga" class="form-control input-harga" placeholder="0" required>
                                    @error('harga')
                                        <span class="text-danger" style="font-size: 12px;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">
                                    Status Kamar <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                    <select name="status" class="form-control" required>
                                        <option value="Kosong" selected>Kosong</option>
                                        <option value="Terisi">Terisi</option>
                                        <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 pe-0">
                            <div class="form-group">
                                <label for="foto">Foto Kamar</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-camera"></i>
                                    </span>
                                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(this, 'preview-tambah')">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Preview</label>
                                <div class="input-group">
                                    <img id="preview-tambah"
                                        src=""
                                        data-original-src=""
                                        alt="Preview"
                                        style="max-width: 100%; max-height: 150px; border-radius: 8px; border: 1px solid #ddd; padding: 5px; display: none;">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>
                                    Deskripsi Kamar <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-list"></i>
                                    </span>
                                    <textarea name="deskripsi" rows="3" class="form-control" placeholder="Masukan deskripsi kamar" required style="resize: none;"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <input type="submit" value="Simpan" class="btn btn-primary">
                </div>

            </form>
        </div>
    </div>
</div>

<script>

    const nextCodes = {
        'Perempuan' : "{{ $nextA }}",
        'Laki-Laki' : "{{ $nextB }}",
        'Keluarga'  : "{{ $nextC }}",
    };

    if(document.getElementById('khusus_tambah')) {
        document.getElementById('khusus_tambah').addEventListener('change', function() {

            let kategori    = this.value;
            let inputKode   = document.getElementById('kode_kamar_tambah');
            let info        = document.getElementById('info_kode');

            if (nextCodes[kategori]) {
                inputKode.value = nextCodes[kategori];
                if(info) {
                    info.innerText = "kode urut otomatis: " + nextCodes[kategori];
                    info.classList.remove('text-danger');
                    info.classList.add('text-success');
                }
            } else {
                inputKode.value = "";
                if(info) info.innerText "Silahkan pilih kategori kamar terlebih dahulu";
            }

        });
    }

    // --- SCRIPT BARU UNTUK RESET MODAL SAAT DITUTUP ---
    $(document).ready(function() {
        $('.modal').on('hidden.bs.modal', function () {
            let form = $(this).find('form');
            if(form.length > 0) {
                form[0].reset();
            }

            $(this).find('input[type="file"]').val('');

            let img = $(this).find('img[id^="preview-"]');
            let originalSrc = img.attr('data-original-src');

            if (originalSrc && originalSrc !== "") {
                img.attr('src', originalSrc);
                img.css('display', 'block');
            } else {
                img.attr('src', '');
                img.css('display', 'none');
            }

            $(this).find('#info_kode').text('Pilih kategori dulu').removeClass('text-success text-danger').addClass('text-muted');
            $(this).find('#kode_kamar_tambah').val('');
        });
    });

</script>
