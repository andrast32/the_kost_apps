<x-admin-layout>
    <div class="card card-outline card-primary">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Manajemen pemesanan kamar dan fasilitas</h3>

                <button class="btn btn-sm btn-round btn-outline-primary right ml-auto" data-toggle="modal" data-target="#add">
                    <i class="fas fa-plus"></i> Tambah pemesanan
                </button>

                <a href="{{ route('pemesanan.laporan') }}" class="btn btn-sm btn-round btn-outline-secondary ml-2">
                    <i class="fas fa-print"></i> Print data pemesanan
                </a>

                @if (isset($Sampah) && $Sampah > 0)
                    <a href="{{ route('pemesanan.sampah') }}" class="btn btn-sm btn-round btn-outline-danger ml-2">
                        <i class="fas fa-trash-alt"></i> 
                        lihat sampah
                        <span class="badge badge-danger ml-1">{{ $Sampah }}</span>
                    </a>
                @endif

            </div>
        </div>

        <div class="card-body">
            <table id="data" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th width="10%">Kode Kamar</th>
                        <th>penyewa</th>
                        <th>Tipe & kode kamar</th>
                        <th>Tipe Sewa</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Keluar</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($item as $data)
                        <tr>

                            <td align="center">{{ $loop->iteration }}</td>

                            <td align="center">#{{ $data->kode_pemesanan }}</td>

                            <td>
                                <div class="font-weight-bold text-dark">{{ $data->user->name }}</div>
                            </td>

                            <td>
                                <div class="font-weight-bold text-dark">{{ $data->kamar->khusus }}</div>
                                <small class="badge badge-light border text-muted">#{{ $data->kamar->kode }}</small>
                            </td>

                            <td align="center">
                                @if ($data->jenis_sewa == 'Bulanan')
                                    <span class="badge badge-success mt-1">{{ $data->jenis_sewa }}</span>
                                @elseif ($data->jenis_sewa == 'Harian')
                                    <span class="badge badge-info mt-1">{{ $data->jenis_sewa }}</span>
                                    
                                @else
                                    <span class="badge badge-secondary mt-1">Jenis Sewa Tidak Diketahui</span>
                                @endif
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($data->tgl_masuk)->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($data->tgl_keluar)->format('d/m/Y') }}
                            </td>

                            <td align="center">

                                @if ($data->status == 'Aktif')
                                    <span class="badge badge-success">{{ $data->status }}</span>

                                @elseif ($data->status == 'Menunggu Pembayaran')
                                    <span class="badge badge-warning">{{ $data->status }}</span> <br>

                                @elseif ($data->status == 'Selesai')
                                    <span class="badge badge-danger">{{ $data->status }}</span> <br>
                                    <button class="btn btn-sm btn-link text-primary" data-toggle="modal" data-target="#pesan-{{ $data->id }}">
                                        <i class="fas fa-redo"></i> Pesan ulang
                                    </button>

                                @else
                                    <span class="badge badge-secondary">{{ $data->status }}</span>
                                @endif

                            </td>

                            <td align="center">

                                <a href="{{ route('pemesanan.invoice', $data->id) }}" class="btn btn-link text-primary">
                                    <i class="fas fa-file-invoice"></i>
                                </a>

                                <button class="btn btn-link text-info" data-toggle="modal" data-target="#info-{{ $data->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <button class="btn btn-link text-danger" onclick="Delete({{ $data->id }}, '{{ $data->kode_pemesanan }}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                                <br>

                                @if($data->masihBisaEdit() )
                                    <button class="btn btn-link text-warning"
                                        data-toggle="modal"
                                        data-target="#edit-{{ $data->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif

                                <form id="delete-{{ $data->id }}" action="{{ route('pemesanan.destroy', $data->id) }}" method="post">@csrf @method('DELETE')</form>

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <div class="modal fade" id="add" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

                <div class="modal-header bg-gradient-primary">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-cart-plus mr-2"></i> 
                        Pesan Kamar
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form action="{{ route('pemesanan.store') }}" method="post" id="formBooking" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body bg-light">
                        <div class="row">

                        {{-- ===================== --}}
                        {{-- =  SISI KIRI START  = --}}
                        {{-- ===================== --}}
                            <div class="col-lg-8 border-right p-4 bg-white">

                                <div class="form-group mb-8">

                                    <label>
                                        <i class="fas fa-user-plus"></i> 
                                        Pilih penyewa 
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select name="user_id" id="user_id" class="form-control" required onchange="loadKamarTersedia(this.value)">
                                        <option value=""> Pilih Penyewa </option>
                                        @foreach ($penyewa as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->biodata->jenis_kelamin ?? 'Belum mempunyai biodata' }})</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="form-group mb-8">

                                    <label><i class="fas fa-bed"></i> Pilih Kamar (Difilter sesuai gender) <span class="text-danger">*</span></label>

                                    <div class="text-center d-none py-5" id=".">
                                        <div class="spinner-grow text-primary" role="status"></div>
                                        <p class="text-muted mt-2 small font-italic font-weight-bold">Mencari kamar yang cocok...</p>
                                    </div>

                                    <div id="kamarGrid" class="row mt-2" style="max-height: 450px; overflow-y: auto;">
                                        <div class="col-12 text-center py-5 text-muted border border-dashed rounded">
                                            <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                                            <p>Silakan pilih penyewa di atas terlebih dahulu.</p>
                                        </div>
                                    </div>

                                    <input type="hidden" name="kamar_id" id="input_kamar_id" required>

                                </div>

                            </div>
                        {{-- ===================== --}}
                        {{-- =   SISI KIRI END   = --}}
                        {{-- ===================== --}}

                        {{-- ====================== --}}
                        {{-- =  SISI KANAN START  = --}}
                        {{-- ====================== --}}
                            <div class="col-lg-4 p-4 d-flex flex-column bg-white">

                                <div class="col md-8">
                                    <div class="form-group">

                                        <label>
                                            <i class="fas fa-box"></i> Tambah Fasilitas
                                        </label>

                                        <div class="list-group list-group-flush border rounded shadow-sm bg-white">
                                            @foreach ($fasilitas as $f)
                                                <div class="list-group-item p-2">
                                                    <div class="custom-control custom-checkbox d-flex align-items-center">

                                                        <input type="checkbox" class="custom-control-input check-fasilitas" name="fasilitas_ids[]" id="fas_{{ $f->id }}" value="{{ $f->id }}" data-harga="{{ $f->harga }}">

                                                        <label for="fas_{{ $f->id }}" class="custom-control-label w-100 pl-2" style="cursor:pointer">
                                                            <div class="d-flex align-items-center">

                                                                <div class="rounded mr-2 overflow-hidden" style="width:40px; height:40px; background:#f4f4f4">
                                                                    <img src="{{ $f->foto ? asset('storage/uploads/fasilitas/'.$f->foto) : asset('UI/dashboard/dist/img/boxed-bg.jpg') }}" alt="foto fasilitas" style="width:100%; height:100%; object-fit:cover">
                                                                </div>

                                                                <div>
                                                                    <span class="d-block font-weight-bold text-dark small">{{ $f->nama }}</span>
                                                                    <span class="text-success small">+Rp {{ number_format($f->harga) }}</span>
                                                                </div>

                                                            </div>
                                                        </label>

                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>

                                <div class="col md-8 mb-3 bg-light p-3 border rounded">
                                    <label><i class="far fa-calendar"></i> ATUR TANGGAL KELUAR</label>
                                    <div class="form-group mb-2">
                                        <small class="text-muted">Tgl Masuk:</small>
                                        <input type="date" name="tgl_masuk" id="tgl_masuk" class="form-control font-weight-bold text-primary" value="{{ date('Y-m-d') }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <small class="text-muted">Pilih Tgl Keluar:</small>
                                        <input type="date" name="tgl_keluar" id="tgl_keluar" class="form-control" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" onchange="hitungLogikaSewa()">
                                    </div>
                                    
                                    <div class="row text-center mt-3 mx-0 py-2 border rounded bg-light">
                                        <div class="col-6 border-right">
                                            <small class="d-block text-muted">Durasi</small>
                                            <span id="labelDurasi" class="font-weight-bold">-</span>
                                            <input type="hidden" name="durasi" id="input_durasi">
                                        </div>
                                        <div class="col-6 text-uppercase">
                                            <small class="d-block text-muted">Tipe</small>
                                            <span id="labelTipe" class="font-weight-bold text-info">-</span>
                                            <input type="hidden" name="jenis_sewa" id="input_jenis_sewa">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-auto p-3 rounded shadow-lg border-left-success" style="border-left: 5px solid">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-uppercase font-weight-bold text-dark-50">Total Bayar</small>
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <h2 class="font-weight-bold mb-0 text-success" id="textGrandTotal">Rp 0</h2>
                                </div>

                                <button type="submit" class="btn btn-success btn-block btn-lg shadow mt-3 font-weight-bold py-3">
                                    <i class="fas fa-check-circle mr-2"></i> KONFIRMASI & SIMPAN
                                </button>

                            </div>
                        {{-- ====================== --}}
                        {{-- =   SISI KANAN END   = --}}
                        {{-- ====================== --}}

                        </div>
                    </div>

                    <div class="modal-footer"></div>

                </form>

            </div>
        </div>
    </div>

    @foreach ($item as $info)

        @if($info->masihBisaEdit())
            <div class="modal fade" id="edit-{{ $info->id }}" tabindex="-1" role="dialog" data-backdrop="static">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">

                        <div class="modal-header bg-gradient-primary">
                            <h5 class="modal-title font-weight-bold">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Pemesanan #{{ $info->kode_pemesanan }}
                            </h5>
                            <button class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <form action="{{ route('pemesanan.update',$info->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body bg-light">
                                <div class="row">

                                    {{-- ===================== --}}
                                    {{-- =  SISI KIRI START  = --}}
                                    {{-- ===================== --}}
                                        <div class="col-lg-8 border-right p-4 bg-white">

                                            <label class="font-weight-bold">
                                                <i class="fas fa-bed"></i> Pilih Kamar
                                            </label>

                                            @php
                                                $gender = strtolower(trim($info->user->biodata->jenis_kelamin ?? ''));
                                            @endphp

                                            <div class="row">

                                                @foreach($kamars as $kamar)

                                                    @php
                                                        $khusus = strtolower(trim($kamar->khusus));
                                                        $boleh = ($khusus === $gender || $khusus === 'keluarga');
                                                    @endphp

                                                    @if($boleh)

                                                        <div class="col-md-6 mb-3">
                                                            <div class="card card-kamar shadow-sm 
                                                                {{ $info->kamar_id == $kamar->id ? 'border-primary' : '' }}"
                                                                onclick="pilihKamarEdit(this, {{ $kamar->id }}, {{ $kamar->harga }}, {{ $info->id }})"
                                                                style="cursor:pointer">

                                                                <img src="{{ $kamar->foto 
                                                                        ? Storage::url('uploads/kamar/'.$kamar->foto) 
                                                                        : asset('UI/dashboard/dist/img/boxed-bg.jpg') }}"
                                                                    class="card-img-top"
                                                                    style="height:150px;object-fit:cover">

                                                                <div class="card-body p-2">
                                                                    <div class="font-weight-bold">
                                                                        #{{ $kamar->kode }}
                                                                    </div>

                                                                    <div class="text-success font-weight-bold">
                                                                        Rp {{ number_format($kamar->harga) }}
                                                                        <small>/bulan</small>
                                                                    </div>

                                                                    <small class="badge badge-primary">
                                                                        {{ $kamar->khusus }}
                                                                    </small>
                                                                </div>

                                                            </div>
                                                        </div>

                                                    @endif

                                                @endforeach

                                            </div>

                                            <input type="hidden" 
                                                name="kamar_id" 
                                                id="edit_kamar_id_{{ $info->id }}"
                                                value="{{ $info->kamar_id }}">

                                        </div>
                                    {{-- ===================== --}}
                                    {{-- =   SISI KIRI END   = --}}
                                    {{-- ===================== --}}

                                    {{-- ====================== --}}
                                    {{-- =  SISI KANAN START  = --}}
                                    {{-- ====================== --}}
                                        <div class="col-lg-4 p-4 d-flex flex-column bg-white">

                                            {{-- FASILITAS --}}
                                            <div class="col md-8">
                                                <div class="form-group">

                                                    <label>
                                                        <i class="fas fa-box"></i> Edit Fasilitas
                                                    </label>
                                                    
                                                    <div class="list-group list-group-flush border rounded shadow-sm bg-white">
                                                        @foreach($fasilitas as $fas)

                                                        @php
                                                            $selected = $info->fasilitas->contains($fas->id);
                                                        @endphp

                                                        <div class="list-group-item p-2">
                                                            <div class="custom-control custom-checkbox d-flex align-items-center">

                                                                <input type="checkbox"
                                                                class="custom-control-input check-fasilitas-edit"
                                                                id="edit_fas_{{ $info->id }}_{{ $fas->id }}"
                                                                name="fasilitas_ids[]"
                                                                value="{{ $fas->id }}"
                                                                data-harga="{{ $fas->harga }}"
                                                                {{ $selected ? 'checked' : '' }}>

                                                                <label class="custom-control-label w-100 pl-2"
                                                                for="edit_fas_{{ $info->id }}_{{ $fas->id }}"
                                                                style="cursor:pointer">

                                                                    <div class="d-flex align-items-center">

                                                                        <div class="rounded mr-2 overflow-hidden"
                                                                        style="width:40px;height:40px;background:#f4f4f4">
                                                                            <img src="{{ $fas->foto ? asset('storage/uploads/fasilitas/'.$fas->foto) : asset('UI/dashboard/dist/img/boxed-bg.jpg') }}"
                                                                            style="width:100%;height:100%;object-fit:cover">
                                                                        </div>

                                                                        <div>
                                                                            <span class="d-block font-weight-bold text-dark small">
                                                                                {{ $fas->nama }}
                                                                            </span>
                                                                            <span class="text-success small">
                                                                                +Rp {{ number_format($fas->harga) }}
                                                                            </span>
                                                                        </div>

                                                                    </div>

                                                                </label>

                                                            </div>
                                                        </div>

                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>

                                            {{-- TANGGAL (READONLY) --}}
                                            <div class="col md-8 mb-3 bg-light p-3 border rounded">
                                                <small class="text-muted">Tanggal Masuk</small>
                                                <input type="text"
                                                    class="form-control mb-2"
                                                    value="{{ \Carbon\Carbon::parse($info->tgl_masuk)->format('d/m/Y') }}"
                                                    readonly>

                                                <small class="text-muted">Tanggal Keluar</small>
                                                <input type="text"
                                                    class="form-control"
                                                    value="{{ \Carbon\Carbon::parse($info->tgl_keluar)->format('d/m/Y') }}"
                                                    readonly>
                                            </div>


                                            {{-- TOTAL --}}
                                            <div class="mt-auto bg-dark text-white p-3 rounded shadow-lg">
                                                <div class="d-flex justify-content-between">
                                                    <small>Total Bayar</small>
                                                    <i class="fas fa-receipt"></i>
                                                </div>
                                                <h4 class="font-weight-bold text-success mb-0"
                                                    id="editTotal-{{ $info->id }}">
                                                    Rp {{ number_format($info->total_harga) }}
                                                </h4>
                                            </div>


                                            <button type="submit"
                                                class="btn btn-success btn-block btn-lg mt-3">
                                                <i class="fas fa-save mr-2"></i>
                                                Simpan Perubahan
                                            </button>

                                        </div>
                                    {{-- ====================== --}}
                                    {{-- =   SISI KANAN END   = --}}
                                    {{-- ====================== --}}

                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        @endif

        <div class="modal fade" id="pesan-{{ $info->id }}" tabindex="-1" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header bg-gradient-primary">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-redo"></i> Pesan Ulang #{{ $info->kode_pemesanan }}
                        </h5>
                        <button class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>

                    <form action="{{ route('pemesanan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-body bg-light">
                            <div class="row">

                                {{-- KIRI --}}
                                <div class="col-lg-8 border-right p-4 bg-white">

                                    {{-- USER (LOCKED) --}}
                                    <div class="form-group mb-4">
                                        <label><i class="fas fa-user"></i> Penyewa</label>
                                        <input type="text" class="form-control" value="{{ $info->user->name }} ({{ $info->user->biodata->jenis_kelamin ?? '-' }})" readonly>
                                        <input type="hidden" name="user_id" value="{{ $info->user->id }}">
                                    </div>

                                    {{-- KAMAR (LOCKED) --}}
                                    <div class="form-group mb-8">

                                        <label><i class="fas fa-bed"></i> Kamar yang dipesan sebelumnya</label>

                                        <div class="col-md-6 mb-3">
                                            <div class="card card-kamar shadow-sm" style="border: 3px solid #4e73df;">

                                                <img src="{{ $info->kamar->foto ? Storage::url('uploads/kamar/'.$info->kamar->foto) : asset('UI/dashboard/dist/img/boxed-bg.jpg') }}" style="height:180px;object-fit:cover">

                                                <div class="card-body p-2">
                                                    <div class="font-weight-bold">#{{ $info->kamar->kode }}</div>
                                                    <div class="text-success font-weight-bold">
                                                        Rp. {{ number_format($info->kamar->harga,0,',','.') }}
                                                        <small>/bulan</small>
                                                    </div>
                                                    <span class="badge badge-primary">{{ $info->kamar->khusus }}</span>
                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                    <input type="hidden" name="kamar_id" value="{{ $info->kamar->id }}">

                                </div>

                                {{-- KANAN --}}
                                <div class="col-lg-4 p-4 d-flex flex-column bg-white">

                                    {{-- FASILITAS --}}
                                    <label>Fasilitas</label>
                                    <div class="list-group">

                                        @foreach($fasilitas as $f)
                                            @php
                                                $checked = $info->fasilitas->contains($f->id);
                                            @endphp

                                            <div class="list-group-item">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                        class="custom-control-input fasilitas-ulang-{{ $info->id }}"
                                                        id="ulang_{{ $info->id }}_{{ $f->id }}"
                                                        name="fasilitas_ids[]"
                                                        value="{{ $f->id }}"
                                                        data-harga="{{ $f->harga }}"
                                                        {{ $checked ? 'checked' : '' }}>

                                                    <label class="custom-control-label"
                                                        for="ulang_{{ $info->id }}_{{ $f->id }}">
                                                        {{ $f->nama }}
                                                        <span class="text-success">
                                                            (+Rp {{ number_format($f->harga) }})
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>

                                    {{-- TANGGAL --}}
                                    <div class="mt-3">
                                        <label>Tanggal Masuk</label>
                                        <input type="date"
                                            name="tgl_masuk"
                                            class="form-control"
                                            value="{{ now()->format('Y-m-d') }}"
                                            readonly>
                                    </div>

                                    <div class="mt-2">
                                        <label>Tanggal Keluar</label>
                                        <input type="date"
                                            name="tgl_keluar"
                                            class="form-control tgl-ulang"
                                            data-id="{{ $info->id }}"
                                            required>
                                    </div>

                                    <input type="hidden" name="durasi" class="durasi-ulang-{{ $info->id }}">
                                    <input type="hidden" name="jenis_sewa" class="jenis-ulang-{{ $info->id }}">

                                    {{-- TOTAL --}}
                                    <div class="mt-auto p-3 rounded shadow-lg border-left-success" style="border-left: 5px solid">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-uppercase font-weight-bold text-dark-50">Total Bayar</small>
                                            <i class="fas fa-receipt"></i>
                                        </div>
                                        <h4 class="font-weight-bold mb-0 text-success" id="total-ulang-{{ $info->id }}">
                                            Rp 0
                                        </h4>
                                    </div>

                                    <button type="submit" class="btn btn-success btn-block">
                                        Simpan Pesanan Baru
                                    </button>

                                </div>

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="info-{{ $info->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <span class="fw-mediumbold">Detail pemesanan</span>
                            <span class="fw-light">#{{ $info->id }}</span>
                        </h5>
                        <button class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="col-sm-12 mt-3">
                            <label class="font-weight-bold">Kamar yang dipesan</label>
                            <div class="list-group mt-2">
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    
                                    <div class="d-flex align-items-center">
                                        <img
                                            src="{{ $info->kamar->foto ? Storage::url('uploads/kamar/'.$info->kamar->foto) : asset('UI/dashboard/dist/img/boxed-bg.jpg') }}"
                                            style="width:50px;height:50px;object-fit:cover;border-radius:6px"
                                            class="mr-3"
                                        >

                                        <div>
                                            <div class="font-weight-bold">#{{ $info->kamar->kode }} | {{ $info->kamar->khusus }}</div>
                                        </div>
                                    </div>

                                    <div class="font-weight-bold text-success">
                                        Rp {{ number_format($info->kamar->harga) }}
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 mt-3">
                            <label class="font-weight-bold">Fasilitas yang dipesan</label>

                            @if ($info->fasilitas->count())
                                <div class="list-group mt-2">
                                    @foreach ($info->fasilitas as $f)
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            
                                            <div class="d-flex align-items-center">
                                                <img
                                                    src="{{ $f->foto ? Storage::url('uploads/fasilitas/'.$f->foto) : asset('UI/dashboard/dist/img/boxed-bg.jpg') }}"
                                                    style="width:50px;height:50px;object-fit:cover;border-radius:6px"
                                                    class="mr-3"
                                                >

                                                <div>
                                                    <div class="font-weight-bold">#{{ $f->kode }} | {{ $f->nama }}</div>
                                                </div>
                                            </div>

                                            <div class="font-weight-bold text-success">
                                                Rp {{ number_format($f->pivot->harga_snap) }}
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-light mt-2">
                                    <i class="fas fa-info-circle"></i>
                                    Tidak ada fasilitas tambahan
                                </div>
                            @endif
                        </div>

                        <div class="col-sm-12 mt-3 ml-5 float-right">
                            <div class="list-group mt-2">
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="font-weight-bold">Total</div>
                                        </div>
                                    </div>

                                    <div class="font-weight-bold text-success">
                                        Rp {{ number_format($info->total_harga) }}
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <a href="{{ route('pemesanan.invoice', $data->id) }}" class="btn btn-secondary">
                            <i class="fas fa-file-invoice"></i> Print kwitansi
                        </a>

                        @if ($info->status == "Menunggu Pembayaran")
                            <button class="btn btn-info" onclick="Bayar({{ $data->id }})">
                                <i class="fas fa-wallet"></i>
                                Bayar Sewa
                            </button>
                        @endif

                    </div>

                </div>
            </div>
        </div>

    @endforeach

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>

            /* ======================================================
            GLOBAL VARIABLE
            ====================================================== */

                let hargaKamarDipilih = 0;
                let hargaKamarEdit = {};
                let durasiEdit = {};

                @foreach($item as $info)
                    hargaKamarEdit[{{ $info->id }}] = {{ $info->kamar->harga }};
                    durasiEdit[{{ $info->id }}] = {{ $info->durasi ?? 1 }};
                @endforeach

                /* ======================================================
                LOAD KAMAR (ADD BOOKING)
                ====================================================== */

                window.loadKamarTersedia = function(userId) {

                    if (!userId) return;

                    $('#kamarGrid').html('');

                    $.ajax({
                        url: "{{ route('pemesanan.getKamars') }}",
                        method: "GET",
                        data: {
                            user_id: userId
                        },

                        success: function(res) {

                            if (!res.length) {
                                $('#kamarGrid').html(`
                                    <div class="col-12 text-center py-5 text-muted">
                                        <i class="fas fa-bed fa-3x mb-3 opacity-25"></i>
                                        <p>Tidak ada kamar yang sesuai dengan penyewa.</p>
                                    </div>
                                `);

                                $('#input_kamar_id').val('');
                                return;
                            }

                            let html = '';

                            res.forEach(function(kamar) {

                                let foto = kamar.foto
                                    ? "{{ asset('storage/uploads/kamar') }}/" + kamar.foto
                                    : "{{ asset('UI/dashboard/dist/img/boxed-bg.jpg') }}";

                                html += `
                                    <div class="col-md-6 mb-3">
                                        <div class="card card-kamar shadow-sm"
                                            onclick="pilihKamar(this, ${kamar.id}, ${kamar.harga})"
                                            style="cursor:pointer">

                                            <img src="${foto}"
                                                class="card-img-top"
                                                style="height:150px;object-fit:cover">

                                            <div class="card-body p-2">

                                                <div class="font-weight-bold">
                                                    #${kamar.kode}
                                                </div>

                                                <div class="text-success font-weight-bold">
                                                    Rp ${Number(kamar.harga).toLocaleString('id-ID')}
                                                    <small>/bulan</small>
                                                </div>

                                                <small class="badge badge-primary">
                                                    ${kamar.khusus}
                                                </small>

                                            </div>
                                        </div>
                                    </div>
                                `;
                            });

                            $('#kamarGrid').html(html);
                        },

                        error: function(xhr) {

                            console.error('Gagal mengambil data kamar:', xhr);

                            $('#kamarGrid').html(`
                                <div class="col-12 text-center py-5 text-danger">
                                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                    <p>Gagal mengambil data kamar.</p>
                                </div>
                            `);
                        }
                    });
                };

                function pilihKamar(el, id, harga) {

                    $('.card-kamar').css({'border':'1px solid #dee2e6'});
                    $(el).css({'border':'3px solid #4e73df'});

                    $('#input_kamar_id').val(id);
                    hargaKamarDipilih = harga;

                    hitungLogikaSewa();
                }

            /* ======================================================
            HITUNG TOTAL ADD BOOKING
            ====================================================== */

                function hitungLogikaSewa() {

                    let masuk = new Date($('#tgl_masuk').val());
                    let keluar = new Date($('#tgl_keluar').val());

                    if (!keluar || keluar <= masuk) return;

                    let diffDays = Math.ceil((keluar - masuk) / (1000 * 3600 * 24));

                    let tipe = 'Harian';
                    let durasiFinal = diffDays;

                    let isSameDay = masuk.getDate() === keluar.getDate();
                    let diffMonths = (keluar.getFullYear() - masuk.getFullYear()) * 12 +
                                    (keluar.getMonth() - masuk.getMonth());

                    if (isSameDay && diffMonths > 0) {
                        tipe = 'Bulanan';
                        durasiFinal = diffMonths;
                    } else if (diffDays >= 30) {
                        tipe = 'Bulanan';
                        durasiFinal = Math.floor(diffDays / 30);
                    }

                    $('#labelDurasi').text(durasiFinal + (tipe === 'Bulanan' ? ' Bulan' : ' Hari'));
                    $('#labelTipe').text(tipe);
                    $('#input_durasi').val(durasiFinal);
                    $('#input_jenis_sewa').val(tipe);

                    let hargaFasilitas = 0;
                    $('.check-fasilitas:checked').each(function() {
                        hargaFasilitas += parseFloat($(this).data('harga'));
                    });

                    let total = 0;

                    if (tipe === 'Bulanan') {
                        total = (hargaKamarDipilih + hargaFasilitas) * durasiFinal;
                    } else {
                        total = ((hargaKamarDipilih + hargaFasilitas) / 30) * diffDays;
                    }

                    $('#textGrandTotal').text(
                        'Rp ' + new Intl.NumberFormat('id-ID').format(Math.ceil(total))
                    );
                }

                $(document).on('change', '.check-fasilitas', hitungLogikaSewa);

            /* ======================================================
            EDIT BOOKING
            ====================================================== */

                function pilihKamarEdit(el, id, harga, pemesananId) {

                    let modal = $('#edit-' + pemesananId);

                    modal.find('.card-kamar')
                        .css({'border':'1px solid #dee2e6'});

                    $(el).css({'border':'3px solid #17a2b8'});

                    modal.find('input[name="kamar_id"]').val(id);

                    hargaKamarEdit[pemesananId] = harga;

                    hitungTotalEdit(pemesananId);
                }

                function hitungTotalEdit(pemesananId) {

                    let modal = $('#edit-' + pemesananId);

                    let hargaKamar = hargaKamarEdit[pemesananId] ?? 0;
                    let durasi = durasiEdit[pemesananId] ?? 1;

                    let hargaFasilitas = 0;

                    modal.find('.check-fasilitas-edit:checked').each(function () {
                        hargaFasilitas += parseFloat($(this).data('harga'));
                    });

                    let total = (hargaKamar + hargaFasilitas) * durasi;

                    modal.find('#editTotal-' + pemesananId)
                        .text('Rp ' + new Intl.NumberFormat('id-ID').format(Math.ceil(total)));
                }

                $(document).on('change', '.check-fasilitas-edit', function () {
                    let modal = $(this).closest('.modal');
                    let id = modal.attr('id').replace('edit-','');
                    hitungTotalEdit(id);
                });

            /* ======================================================
            PESAN ULANG
            ====================================================== */

                $('.tgl-ulang').on('change', function(){

                    let id = $(this).data('id');

                    let kamarHarga = hargaKamarEdit[id] ?? 0;

                    let masuk = new Date();
                    let keluar = new Date($(this).val());

                    if (!keluar || keluar <= masuk) return;

                    let diffDays = Math.ceil((keluar - masuk) / (1000*3600*24));

                    let tipe = 'Harian';
                    let durasi = diffDays;

                    let isSameDay = masuk.getDate() === keluar.getDate();
                    let diffMonths = (keluar.getFullYear()-masuk.getFullYear())*12 +
                                    (keluar.getMonth()-masuk.getMonth());

                    if (isSameDay && diffMonths > 0) {
                        tipe = 'Bulanan';
                        durasi = diffMonths;
                    }

                    $('.durasi-ulang-'+id).val(durasi);
                    $('.jenis-ulang-'+id).val(tipe);

                    let fasilitasTotal = 0;

                    $('.fasilitas-ulang-'+id+':checked').each(function(){
                        fasilitasTotal += parseFloat($(this).data('harga'));
                    });

                    let total = 0;

                    if(tipe === 'Bulanan'){
                        total = (kamarHarga + fasilitasTotal) * durasi;
                    } else {
                        total = ((kamarHarga + fasilitasTotal) / 30) * diffDays;
                    }

                    $('#total-ulang-'+id).text(
                        'Rp ' + new Intl.NumberFormat('id-ID').format(Math.ceil(total))
                    );
                });

            /* ======================================================
            DELETE CONFIRM
            ====================================================== */

                function Delete(id, kode) {
                    Swal.fire({
                        title: 'Hapus pemesanan dengan kode #'+kode+'?',
                        text: 'pemesanan tersebut akan dihapus!',
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

                function Bayar(id) {
                    Swal.fire({
                        title: 'Lakukan pembayaran untuk pemesanan dengan kode #'+id+'?',
                        text: 'pemesanan kamar dengan kode tersebut akan dibayar',
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

    @endpush

</x-admin-layout>