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
                                    <img src="{{ asset('UI/admin/images/kamar/' . $kamar->foto_kamar) }}"
                                        alt="Foto Kamar" width="100px">
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

                                                            <label for="kode">
                                                                Kode Kamar
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="input-group">

                                                                <span class="input-group-text">
                                                                    <i class="fas fa-key"></i>
                                                                </span>

                                                                <input type="text" name="kode_kamar" class="form-control" value="{{ $kamar->kode_kamar }}" required readonly>

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

                                                            <label for="status">
                                                                Status kamar
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="input-group">

                                                                <span class="input-group-text">
                                                                    <i class="fas fa-venus-mars"></i>
                                                                </span>

                                                                <select name="khusus" class="form-control" required>

                                                                    <option value="" disabled>
                                                                        Kamar Khusus
                                                                    </option>

                                                                    <option value="Laki-Laki" {{ $kamar->khusus == 'Laki-Laki' ? 'selected' : '' }}>
                                                                        Laki Laki
                                                                    </option>

                                                                    <option value="Perempuan" {{ $kamar->khusus == 'Perempuan' ? 'selected' : '' }}>
                                                                        Perempuan
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

                                                            <label for="khusus">
                                                                Kamar Khusus
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <div class="input-group">

                                                                <span class="input-group-text">
                                                                    <i class="fas fa-venus-mars"></i>
                                                                </span>

                                                                <select name="khusus" class="form-control" required>

                                                                    <option value="" disabled>
                                                                        Kamar Khusus
                                                                    </option>

                                                                    <option value="Laki-Laki" {{ $kamar->khusus == 'Laki-Laki' ? 'selected' : '' }}>
                                                                        Laki Laki
                                                                    </option>

                                                                    <option value="Perempuan" {{ $kamar->khusus == 'Perempuan' ? 'selected' : '' }}>
                                                                        Perempuan
                                                                    </option>

                                                                    <option value="Keluarga" {{ $kamar->khusus == 'Keluarga' ? 'selected' : '' }}>
                                                                        Keluarga
                                                                    </option>

                                                                </select>

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
                                                                    src="{{ $kamar->foto ? asset('storage/' . $kamar->foto) : '' }}" 
                                                                    alt="Preview Foto" 
                                                                    style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd; padding: 5px;"
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

            <div class="modal-header"></div>

            <div class="modal-body"></div>

        </div>
    </div>
</div>
