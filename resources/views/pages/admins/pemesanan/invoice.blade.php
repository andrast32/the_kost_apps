<x-admin-layout>

    <div class="container-fluid">

        <div class="card shadow-lg">

            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Invoice Pemesanan
                </h4>
            </div>

            <div class="card-body">

                {{-- ================= HEADER ================= --}}
                <div class="row mb-4">

                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Detail Penyewa</h5>
                        <p class="mb-1"><strong>Nama:</strong> {{ $item->user->name }}</p>
                        <p class="mb-1"><strong>Gender:</strong> {{ $item->user->biodata->jenis_kelamin ?? '-' }}</p>
                        <p class="mb-1"><strong>Kode Pemesanan:</strong> #{{ $item->kode_pemesanan }}</p>
                    </div>

                    <div class="col-md-6 text-md-right">
                        <h5 class="font-weight-bold">Status</h5>

                        @if($item->status == 'Menunggu Pembayaran')
                            <span class="badge badge-warning p-2">
                                {{ $item->status }}
                            </span>

                            <p class="mt-2 text-danger">
                                Batas bayar:
                                {{ $item->created_at->addHours(48)->format('d M Y H:i') }}
                            </p>

                        @elseif($item->status == 'Aktif')
                            <span class="badge badge-success p-2">
                                {{ $item->status }}
                            </span>

                        @elseif($item->status == 'Dibatalkan')
                            <span class="badge badge-danger p-2">
                                {{ $item->status }}
                            </span>

                        @else
                            <span class="badge badge-secondary p-2">
                                {{ $item->status }}
                            </span>
                        @endif

                    </div>

                </div>


                {{-- ================= DETAIL KAMAR ================= --}}
                <div class="mb-4">
                    <h5 class="font-weight-bold">Detail Kamar</h5>

                    <div class="list-group">

                        <div class="list-group-item d-flex justify-content-between align-items-center">

                            <div class="d-flex align-items-center">

                                <img src="{{ $item->kamar->foto 
                                        ? Storage::url('uploads/kamar/'.$item->kamar->foto) 
                                        : asset('UI/dashboard/dist/img/boxed-bg.jpg') }}"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:8px"
                                     class="mr-3">

                                <div>
                                    <div class="font-weight-bold">
                                        #{{ $item->kamar->kode }}
                                    </div>
                                    <small>{{ $item->kamar->khusus }}</small>
                                </div>

                            </div>

                            <div class="font-weight-bold text-success">
                                Rp {{ number_format($item->kamar->harga,0,',','.') }}
                            </div>

                        </div>

                    </div>
                </div>


                {{-- ================= DETAIL FASILITAS ================= --}}
                <div class="mb-4">
                    <h5 class="font-weight-bold">Fasilitas Tambahan</h5>

                    @if($item->fasilitas->count())

                        <div class="list-group">

                            @foreach($item->fasilitas as $f)
                                <div class="list-group-item d-flex justify-content-between align-items-center">

                                    <div class="d-flex align-items-center">

                                        <img src="{{ $f->foto 
                                                ? Storage::url('uploads/fasilitas/'.$f->foto) 
                                                : asset('UI/dashboard/dist/img/boxed-bg.jpg') }}"
                                             style="width:50px;height:50px;object-fit:cover;border-radius:6px"
                                             class="mr-3">

                                        <div>
                                            <div class="font-weight-bold">
                                                {{ $f->nama }}
                                            </div>
                                        </div>

                                    </div>

                                    <div class="font-weight-bold text-success">
                                        Rp {{ number_format($f->pivot->harga_snap,0,',','.') }}
                                    </div>

                                </div>
                            @endforeach

                        </div>

                    @else

                        <div class="alert alert-light">
                            Tidak ada fasilitas tambahan
                        </div>

                    @endif
                </div>


                {{-- ================= TANGGAL ================= --}}
                <div class="row mb-4">

                    <div class="col-md-6">
                        <strong>Tanggal Masuk:</strong><br>
                        {{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d M Y') }}
                    </div>

                    <div class="col-md-6">
                        <strong>Tanggal Keluar:</strong><br>
                        {{ \Carbon\Carbon::parse($item->tgl_keluar)->format('d M Y') }}
                    </div>

                </div>


                {{-- ================= TOTAL ================= --}}
                <div class="border-top pt-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <h4 class="font-weight-bold">Total Bayar</h4>

                        <h3 class="text-success font-weight-bold">
                            Rp {{ number_format($item->total_harga,0,',','.') }}
                        </h3>

                    </div>

                </div>


                {{-- ================= TOMBOL ================= --}}
                <div class="mt-4 text-right">

                    <a href="{{ route('pemesanan.index') }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>

                    <button class="btn btn-info">
                        <i class="fas fa-print"></i>
                        Print / Unduh Invoice
                    </button>

                    @if ($item->status == 'Menunggu Pembayaran')
                        <button class="btn btn-success" onclick="Bayar({{ $item->id }}, '{{ $item->user->name }}')">
                            <i class="fas fa-wallet"></i>
                            Bayar Sekarang
                        </button>
                    @endif

                </div>

            </div>

        </div>

    </div>

</x-admin-layout>

<script>
    function bayar(id, name) {
        
    }
</script>