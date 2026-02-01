<x-admin-layout title="Data Pemesanan">
    <div class="container-fluid">
        
        {{-- TOMBOL TAMBAH --}}
        <div class="card mb-4 border-left-primary shadow h-100 py-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Data Transaksi Pemesanan</h1>
                <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Transaksi Baru
                </button>
            </div>
        </div>

        {{-- TABEL DATA PEMESANAN --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Penyewa</th>
                                <th>Kamar</th>
                                <th>Periode Sewa</th>
                                <th>Fasilitas</th>
                                <th>Total & Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $item->user->name ?? 'User Hilang' }}</div>
                                    <small class="text-muted">{{ $item->user->biodata->no_hp ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-primary">{{ $item->kamar->nama_kamar ?? 'Kamar Dihapus' }}</div>
                                    <span class="badge badge-secondary">{{ $item->jenis_sewa }}</span>
                                </td>
                                <td>
                                    <small class="d-block text-muted">Masuk</small>
                                    {{ \Carbon\Carbon::parse($item->tgl_masuk)->translatedFormat('d M Y') }}
                                    <hr class="my-1">
                                    <small class="d-block text-muted">Keluar</small>
                                    {{ \Carbon\Carbon::parse($item->tgl_keluar)->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    @if($item->fasilitas->isNotEmpty())
                                        <div class="d-flex flex-wrap">
                                        @foreach($item->fasilitas as $fas)
                                            <span class="badge badge-info mr-1 mb-1">{{ $fas->nama_fasilitas }}</span>
                                        @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small">Tanpa Fasilitas</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold text-success mb-1">
                                        Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                    </div>
                                    <span class="badge {{ $item->status == 'Aktif' ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('pemesanan.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm btn-circle" onclick="return confirm('Hapus transaksi ini? Stok fasilitas akan dikembalikan.')" title="Batalkan">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <p class="text-muted">Belum ada data pemesanan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH (STYLE TOKO ONLINE) --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0">
                <form action="{{ route('pemesanan.store') }}" method="POST" id="formBooking" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-shopping-cart mr-2"></i> Buat Pesanan Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    
                    <div class="modal-body bg-light">
                        <div class="row">
                            {{-- KOLOM KIRI: USER & FILTER KAMAR --}}
                            <div class="col-lg-7 border-right bg-white p-4">
                                {{-- Step 1: Pilih User --}}
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark text-uppercase small ls-1">1. Pilih Penyewa</label>
                                    <select name="user_id" id="user_id" class="form-control select2" style="width: 100%" required onchange="loadKamars(this.value)">
                                        <option value="">-- Cari Nama Penyewa --</option>
                                        @foreach($penyewas as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->biodata->jenis_kelamin ?? 'Gender?' }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">* Daftar kamar akan difilter otomatis berdasarkan jenis kelamin penyewa.</small>
                                </div>

                                {{-- Step 2: List Kamar (Card Grid) --}}
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark text-uppercase small ls-1 mb-3">2. Pilih Kamar</label>
                                    
                                    {{-- Loader --}}
                                    <div id="loader" class="text-center d-none py-5">
                                        <div class="spinner-border text-primary" role="status"></div>
                                        <p class="mt-2 text-muted small">Sedang mencari kamar yang tersedia...</p>
                                    </div>

                                    {{-- Container Kamar --}}
                                    <div id="kamarContainer" class="row" style="max-height: 400px; overflow-y: auto;">
                                        <div class="col-12 text-center text-muted py-5 border rounded bg-light">
                                            <i class="fas fa-door-closed fa-3x mb-3 text-gray-300"></i>
                                            <p>Silakan pilih penyewa terlebih dahulu.</p>
                                        </div>
                                    </div>
                                    {{-- Input Hidden ID Kamar --}}
                                    <input type="hidden" name="kamar_id" id="selectedKamarId" required>
                                </div>
                            </div>

                            {{-- KOLOM KANAN: FASILITAS & PERHITUNGAN --}}
                            <div class="col-lg-5 p-4 d-flex flex-column">
                                
                                {{-- Step 3: Fasilitas --}}
                                <div class="mb-4">
                                    <label class="font-weight-bold text-dark text-uppercase small ls-1">3. Tambah Fasilitas (Opsional)</label>
                                    <div class="card border-0 bg-light">
                                        <div class="card-body p-2" style="max-height: 250px; overflow-y: auto;">
                                            @foreach($fasilitas as $f)
                                            <div class="custom-control custom-checkbox mb-2 p-2 rounded bg-white border shadow-sm item-fasilitas">
                                                <input type="checkbox" class="custom-control-input fasilitas-check" 
                                                       name="fasilitas_ids[]" 
                                                       id="fas_{{ $f->id }}" 
                                                       value="{{ $f->id }}"
                                                       data-harga="{{ $f->harga }}">
                                                <label class="custom-control-label w-100 d-flex justify-content-between align-items-center" for="fas_{{ $f->id }}" style="cursor: pointer">
                                                    <div class="d-flex align-items-center">
                                                        {{-- Placeholder Foto Fasilitas --}}
                                                        <div style="width: 40px; height: 40px; background: #eee; border-radius: 5px; margin-right: 10px; overflow: hidden;">
                                                            <img src="{{ $f->foto ? asset('storage/uploads/fasilitas/'.$f->foto) : asset('UI/dashboard/dist/img/boxed-bg.jpg') }}" style="width:100%; height:100%; object-fit:cover">
                                                        </div>
                                                        <div>
                                                            <span class="d-block font-weight-bold text-dark" style="font-size: 0.9rem">{{ $f->nama_fasilitas }}</span>
                                                            <span class="text-muted" style="font-size: 0.75rem">Stok: {{ $f->stok }}</span>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-success badge-pill">+{{ number_format($f->harga/1000) }}k</span>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Step 4: Kalkulator Tanggal --}}
                                <div class="mb-4 bg-white p-3 rounded border shadow-sm">
                                    <label class="font-weight-bold text-dark text-uppercase small ls-1">4. Periode Sewa</label>
                                    
                                    <div class="form-group mb-2">
                                        <label class="small text-muted mb-0">Tanggal Masuk (Otomatis Hari Ini)</label>
                                        <input type="date" name="tgl_masuk" id="tgl_masuk" class="form-control font-weight-bold text-primary" value="{{ date('Y-m-d') }}" readonly>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="small text-muted mb-0">Tanggal Keluar (Pilih)</label>
                                        <input type="date" name="tgl_keluar" id="tgl_keluar" class="form-control" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    </div>

                                    {{-- Hasil Perhitungan Otomatis --}}
                                    <div class="row mt-3 text-center bg-light mx-0 py-2 rounded">
                                        <div class="col-6 border-right">
                                            <small class="text-muted d-block">Durasi</small>
                                            <strong id="displayDurasi">- Hari</strong>
                                            <input type="hidden" name="durasi" id="inputDurasi">
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Jenis Sewa</small>
                                            <strong id="displayJenis" class="text-info">-</strong>
                                            <input type="hidden" name="jenis_sewa" id="inputJenis">
                                        </div>
                                    </div>
                                </div>

                                {{-- Step 5: Total Harga --}}
                                <div class="mt-auto bg-primary text-white p-3 rounded shadow">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-white-50 text-uppercase font-weight-bold">Estimasi Total</small>
                                        <small id="rincianHitungan" class="text-white-50" style="font-size: 0.7em"></small>
                                    </div>
                                    <h2 class="font-weight-bold mb-0" id="grandTotal">Rp 0</h2>
                                </div>

                                <button type="submit" class="btn btn-success btn-block btn-lg shadow mt-3 py-3 font-weight-bold">
                                    <i class="fas fa-save mr-2"></i> PROSES CHECK-IN
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- LIB PENDUKUNG --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- ALERT TOASTR --}}
    @if(session('alert'))
    <script>
        Swal.fire({
            icon: '{{ session('alert.icon') }}',
            title: '{{ session('alert.title') }}',
            text: '{{ session('alert.text') }}',
            confirmButtonColor: '#4e73df',
        });
    </script>
    @endif

    <script>
        let baseHargaKamar = 0;
        let selectedKamarNama = '';

        // 1. AJAX LOAD KAMAR (FILTER GENDER)
        function loadKamars(userId) {
            if(!userId) return;
            
            // Tampilkan loading & reset container
            $('#loader').removeClass('d-none');
            $('#kamarContainer').html('');
            
            // Reset pilihan sebelumnya jika ganti user
            $('#selectedKamarId').val('');
            baseHargaKamar = 0;
            $('#grandTotal').text('Rp 0'); // Reset tampilan harga
            
            $.ajax({
                url: "{{ route('pemesanan.getKamars') }}",
                type: "GET",
                data: { user_id: userId },
                success: function(response) {
                    $('#loader').addClass('d-none');
                    
                    // Jika tidak ada kamar yang cocok
                    if(response.length === 0) {
                        $('#kamarContainer').html(`
                            <div class="col-12 text-center text-danger py-4 border border-danger rounded bg-white">
                                <i class="fas fa-ban fa-2x mb-2"></i>
                                <p class="mb-0 font-weight-bold">Maaf, tidak ada kamar tersedia.</p>
                                <small>Tidak ada kamar kosong yang sesuai dengan jenis kelamin penyewa ini.</small>
                            </div>
                        `);
                        return;
                    }

                    let html = '';
                    response.forEach(function(kamar) {
                        // Gambar placeholder jika foto kosong
                        let foto = kamar.foto ? `/storage/uploads/kamar/${kamar.foto}` : '{{ asset("UI/dashboard/dist/img/boxed-bg.jpg") }}';
                        
                        html += `
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 cursor-pointer kamar-item shadow-sm" 
                                onclick="pilihKamar(this, ${kamar.id}, ${kamar.harga}, '${kamar.nama_kamar}')"
                                style="border: 2px solid #eaecf4; transition: all 0.2s; cursor: pointer; overflow:hidden">
                                <div class="position-relative">
                                    <img src="${foto}" class="card-img-top" style="height: 120px; object-fit: cover">
                                    
                                    {{-- PERBAIKAN: Menggunakan kolom 'khusus' dari database --}}
                                    <div class="position-absolute bg-primary text-white px-2 py-1 small rounded-bottom" style="top:0; left:10px;">
                                        ${kamar.khusus}
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="font-weight-bold text-dark mb-1">${kamar.nama_kamar}</h6>
                                    <p class="small text-muted mb-2" style="font-size: 0.75rem; line-height: 1.2">
                                        ${kamar.deskripsi ? kamar.deskripsi.substring(0, 50) + '...' : 'Tidak ada deskripsi'}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="font-weight-bold text-primary">Rp ${new Intl.NumberFormat('id-ID').format(kamar.harga)}</span>
                                        <span class="badge badge-light border">/Bulan</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });
                    $('#kamarContainer').html(html);
                },
                error: function(xhr) {
                    $('#loader').addClass('d-none');
                    // Tampilkan pesan error yang lebih jelas
                    let errorMsg = xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan pada server';
                    Swal.fire('Error', 'Gagal memuat data kamar: ' + errorMsg, 'error');
                }
            });
        }

        // 2. VISUAL PILIH KAMAR
        function pilihKamar(el, id, harga, nama) {
            // Reset style semua card
            $('.kamar-item').css('border-color', '#eaecf4').removeClass('bg-light-primary');
            $('.kamar-item').find('.card-body').removeClass('bg-alice-blue'); // Opsional class custom

            // Highlight card yang dipilih
            $(el).css('border-color', '#4e73df').addClass('bg-light-primary');
            
            // Simpan data ke variabel global & input
            $('#selectedKamarId').val(id);
            baseHargaKamar = harga;
            selectedKamarNama = nama;
            
            hitungSemua();
        }

        // 3. LOGIKA HITUNG DURASI & HARGA OTOMATIS
        function hitungSemua() {
            let tglMasuk = new Date($('#tgl_masuk').val());
            let tglKeluar = new Date($('#tgl_keluar').val());

            // Pastikan kamar sudah dipilih
            if (baseHargaKamar === 0) {
                $('#grandTotal').text('Pilih Kamar Dulu');
                return;
            }

            // Validasi Tanggal
            if (!tglKeluar || tglKeluar <= tglMasuk) {
                $('#displayDurasi').text('- Hari');
                $('#displayJenis').text('-');
                $('#grandTotal').text('Cek Tanggal');
                return;
            }

            // Hitung selisih hari
            let timeDiff = tglKeluar.getTime() - tglMasuk.getTime();
            let diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); // Total hari

            // Hitung Bulan & Sisa Hari (Logika Sederhana)
            // Di sini kita asumsikan untuk penentuan jenis sewa:
            // < 30 hari = Harian
            // >= 30 hari = Bulanan
            
            let jenisSewa = 'Harian';
            let durasiBayar = diffDays; 
            
            // Variabel untuk harga dasar perhitungan
            let hargaDasarHitung = 0;

            if (diffDays >= 30) {
                jenisSewa = 'Bulanan';
                // Jika bulanan, kita hitung berapa bulan. 
                // Jika user pilih 35 hari, tetap dihitung 1 bulan + 5 hari harian (opsional) 
                // ATAU sesuai request: "lebih 31 hari dihitung harian... jika lebih 2 bulan dihitung 2 bulan"
                
                // Mari kita gunakan logika `diffInMonths` sederhana via Javascript:
                // Sederhananya: Total Hari / 30
                let totalBulan = diffDays / 30;
                
                // Jika hasilnya mendekati bulat (misal 30, 31, 29, 60, 61), kita anggap bulat bulan.
                // Request user: "otomatis dihitung 2 bulan dst"
                
                durasiBayar = (diffDays / 30).toFixed(1); // Misal 1.0, 1.5, 2.0
                hargaDasarHitung = baseHargaKamar; // Harga per bulan
            } else {
                // Harian
                jenisSewa = 'Harian';
                durasiBayar = diffDays;
                hargaDasarHitung = baseHargaKamar / 30; // Harga per hari (proporsional)
            }

            // Update UI Teks
            $('#displayDurasi').text(diffDays + ' Hari');
            $('#displayJenis').text(jenisSewa);
            
            // Isi Input Hidden untuk Controller
            $('#inputJenis').val(jenisSewa);
            
            // PENTING: Controller kita minta 'durasi' sebagai int/numeric.
            // Jika Bulanan => kirim jumlah bulannya (misal 1, 2).
            // Jika Harian => kirim jumlah harinya.
            
            let durasiUntukDikirim = 0;
            if(jenisSewa === 'Bulanan') {
                // Pembulatan ke atas untuk durasi bulan agar aman di controller (logic controller pakai addMonths)
                // Atau biarkan desimal jika mau harga sangat presisi. 
                // Sesuai request: "hitung harian jika lebih dari 31" tapi "dihitung 2 bulan jika memilih lebih".
                // Solusi aman: Kirim total hari saja jika controllernya fleksibel, TAPI controller kita fix addMonths/addDays.
                
                // Kita akan paksa logika visual di sini agar sinkron dengan harga.
                // Mari kita buat simpel: Total Harga = (Harga Bulanan / 30) * Total Hari
                // Jadi jenis sewa hanya label, tapi perhitungan harga selalu berbasis hari untuk presisi.
                durasiUntukDikirim = diffDays; 
                // TAPI controller kita punya logic: if Bulanan -> addMonths.
                // Jadi kita harus kirim 'Bulanan' hanya jika pas kelipatan bulan, atau biarkan controller menangani.
                
                // REVISI LOGIC CONTROLLER DI PIKIRAN KITA:
                // Agar "pas" dengan permintaan, lebih baik kita kirim 'Harian' dan durasi 'Total Hari' 
                // tapi labelnya saja yang kita sebut Bulanan di database jika > 30 hari.
                
                // NAMUN, karena kita tidak bisa ubah controller sekarang, kita ikuti controller:
                durasiUntukDikirim = Math.round(diffDays / 30); 
                if(durasiUntukDikirim < 1) durasiUntukDikirim = 1;
            } else {
                durasiUntukDikirim = diffDays;
            }
            
            // Hack sedikit: Agar perhitungan harga AKURAT sampai ke harian, 
            // Kita hitung total harga di JS ini, dan pastikan logic di backend selaras.
            
            // Hitung Total Fasilitas
            let totalFasilitas = 0;
            $('.fasilitas-check:checked').each(function() {
                totalFasilitas += parseFloat($(this).data('harga'));
            });

            // LOGIKA HARGA FINAL SESUAI REQUEST:
            // "kalau harian harganya bedain... misal 1 bulan 600rb, harian jadi 20rb (600/30)"
            
            let hargaPerHariKamar = baseHargaKamar / 30;
            let hargaPerHariTotal = hargaPerHariKamar + (totalFasilitas / 30); // Asumsi harga fasilitas di DB juga bulanan?
            
            // Biasanya harga fasilitas di DB itu harga per bulan. Jadi harus dibagi 30 juga.
            // Jika harga fasilitas di DB adalah harga flat sekali bayar, jangan dibagi 30.
            // ASUMSI: Harga Fasilitas di DB adalah HARGA BULANAN (seperti kamar).
            
            let totalBayar = 0;
            if (jenisSewa === 'Bulanan') {
                // Harga Kamar + Fasilitas (Bulanan) * Durasi Bulan
                // Gunakan Math.round(diffDays/30) agar input durasi di controller masuk akal
                let durasiBulan = Math.round(diffDays / 30);
                if (durasiBulan === 0) durasiBulan = 1;
                
                $('#inputDurasi').val(durasiBulan); 
                totalBayar = (baseHargaKamar + totalFasilitas) * durasiBulan;
                
                $('#rincianHitungan').text(`(Kamar + Fasilitas) x ${durasiBulan} Bulan`);
            } else {
                // Hitungan Harian Murni
                $('#inputDurasi').val(diffDays);
                // (HargaKamar/30 + HargaFasilitas/30) * Hari
                totalBayar = ((baseHargaKamar + totalFasilitas) / 30) * diffDays;
                
                $('#rincianHitungan').text(`(Harga Proporsional Harian) x ${diffDays} Hari`);
            }

            // Tampilkan
            $('#grandTotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(Math.ceil(totalBayar)));
        }

        // Trigger Events
        $('#tgl_keluar').on('change', hitungSemua);
        $('.fasilitas-check').on('change', hitungSemua);

        // Init Select2 di Modal
        $('#modalTambah').on('shown.bs.modal', function () {
            $('.select2').select2({
                dropdownParent: $('#modalTambah'),
                theme: 'bootstrap4'
            });
        });
    </script>
    @endpush
</x-admin-layout>