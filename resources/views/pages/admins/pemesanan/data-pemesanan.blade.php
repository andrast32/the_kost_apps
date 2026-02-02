<x-admin-layout title="Data Pemesanan">
    <div class="container-fluid">
        {{-- Header & Tombol Tambah --}}
        <div class="card mb-4 border-left-primary shadow h-100 py-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0 text-gray-800 font-weight-bold text-uppercase">Pemesanan Kost</h1>
                <button class="btn btn-primary shadow-sm px-4 font-weight-bold" data-toggle="modal" data-target="#modalTokoOnline">
                    <i class="fas fa-plus mr-2"></i> BUAT PESANAN BARU
                </button>
            </div>
        </div>

        {{-- Tabel Utama --}}
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="dataTable" width="100%">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th>Penyewa</th>
                                <th>Kamar</th>
                                <th>Periode Sewa</th>
                                <th>Fasilitas</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $item->user->name }}</div>
                                    <small class="badge badge-light border text-muted">{{ $item->user->biodata->jenis_kelamin }}</small>
                                </td>
                                <td>
                                    <div class="text-primary font-weight-bold">{{ $item->kamar->nama_kamar }}</div>
                                    <small class="text-muted">Kode kamar: #{{ $item->kamar->kode }}</small>
                                </td>
                                <td>
                                    <div class="small">
                                        <span class="text-success font-weight-bold">In:</span> {{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d/m/Y') }}<br>
                                        <span class="text-danger font-weight-bold">Out:</span> {{ \Carbon\Carbon::parse($item->tgl_keluar)->format('d/m/Y') }}
                                    </div>
                                    <span class="badge badge-info mt-1">{{ $item->jenis_sewa }}</span>
                                </td>
                                <td>
                                    @foreach($item->fasilitas as $f)
                                        <span class="badge badge-secondary mr-1">{{ $f->nama }}</span>
                                    @endforeach
                                </td>
                                <td class="font-weight-bold text-success text-lg">Rp {{ number_format($item->total_harga) }}</td>
                                <td>
                                    <span class="badge {{ $item->status == 'Aktif' ? 'badge-success' : 'badge-secondary' }}">{{ $item->status }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('pemesanan.destroy', $item->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm rounded-circle" onclick="return confirm('Batalkan pesanan?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL GAYA TOKO ONLINE --}}
    <div class="modal fade" id="modalTokoOnline" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('pemesanan.store') }}" method="POST" id="formBooking">
                    @csrf
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-cart-plus mr-2"></i> FORM CHECK-IN THE KOST</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="row">
                            {{-- SISI KIRI: PILIH USER & GRID KAMAR --}}
                            <div class="col-lg-8 border-right p-4 bg-white">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark"><i class="fas fa-user mr-1 text-primary"></i> 1. PILIH PENYEWA</label>
                                    <select name="user_id" id="user_id" class="form-control select2" style="width:100%" required onchange="loadKamarTersedia(this.value)">
                                        <option value="">-- Pilih Nama Penyewa --</option>
                                        @foreach($penyewas as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->biodata->jenis_kelamin ?? 'Belum Ada Biodata' }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <label class="font-weight-bold text-dark"><i class="fas fa-bed mr-1 text-primary"></i> 2. PILIH KAMAR (DIFILTER SESUAI GENDER)</label>
                                <div id="kamarLoader" class="text-center d-none py-5">
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

                            {{-- SISI KANAN: FASILITAS, TANGGAL & HARGA --}}
                            <div class="col-lg-4 p-4 d-flex flex-column" style="background: #fdfdfd">
                                <div class="mb-4">
                                    <label class="font-weight-bold text-dark"><i class="fas fa-box mr-1 text-primary"></i> 3. TAMBAH FASILITAS</label>
                                    <div class="list-group list-group-flush border rounded shadow-sm bg-white" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($fasilitas as $f)
                                        <div class="list-group-item p-2">
                                            <div class="custom-control custom-checkbox d-flex align-items-center">
                                                <input type="checkbox" class="custom-control-input check-fasilitas" id="fas_{{ $f->id }}" name="fasilitas_ids[]" value="{{ $f->id }}" data-harga="{{ $f->harga }}">
                                                <label class="custom-control-label w-100 pl-2" for="fas_{{ $f->id }}" style="cursor:pointer">
                                                    <div class="d-flex align-items-center">
                                                        <div style="width:40px; height:40px; background:#f4f4f4" class="rounded mr-2 overflow-hidden">
                                                            <img src="{{ $f->foto ? asset('storage/uploads/fasilitas/'.$f->foto) : asset('UI/dashboard/dist/img/boxed-bg.jpg') }}" style="width:100%; height:100%; object-fit:cover">
                                                        </div>
                                                        <div>
                                                            <span class="d-block font-weight-bold text-dark small">{{ $f->nama_fasilitas }}</span>
                                                            <span class="text-success small">+Rp {{ number_format($f->harga) }}</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-4 bg-white p-3 border rounded shadow-sm">
                                    <label class="font-weight-bold text-dark small mb-3">4. ATUR TANGGAL KELUAR</label>
                                    <div class="form-group mb-2">
                                        <small class="text-muted">Tgl Masuk (Otomatis):</small>
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

                                <div class="mt-auto bg-dark text-white p-3 rounded shadow-lg border-left-success" style="border-left: 5px solid">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-uppercase font-weight-bold text-white-50">Total Bayar</small>
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <h2 class="font-weight-bold mb-0 text-success" id="textGrandTotal">Rp 0</h2>
                                </div>

                                <button type="submit" class="btn btn-success btn-block btn-lg shadow mt-3 font-weight-bold py-3">
                                    <i class="fas fa-check-circle mr-2"></i> KONFIRMASI & SIMPAN
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @if(session('alert'))
    <script>
        Swal.fire({
            icon: '{{ session("alert.icon") }}',
            title: '{{ session("alert.title") }}',
            text: '{{ session("alert.text") }}',
            showConfirmButton: false, timer: 3000
        });
    </script>
    @endif

    <script>
        let hargaKamarDipilih = 0;

        function loadKamarTersedia(userId) {
            if (!userId) return;

            $('#kamarLoader').removeClass('d-none');
            $('#kamarGrid').html('');

            $.ajax({
                url: "{{ route('pemesanan.getKamars') }}",
                method: "GET",
                data: { user_id: userId },
                success: function (res) {
                    $('#kamarLoader').addClass('d-none');

                    if (!res.length) {
                        $('#kamarGrid').html(`
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="fas fa-bed fa-3x mb-3"></i>
                                <p>Tidak ada kamar tersedia</p>
                            </div>
                        `);
                        return;
                    }

                    let html = '';
                    res.forEach(k => {
                        let foto = k.foto
                            ? `/storage/uploads/kamar/${k.foto}`
                            : '{{ asset("UI/dashboard/dist/img/boxed-bg.jpg") }}';

                        html += `
                        <div class="col-md-6 mb-3">
                            <div class="card card-kamar shadow-sm"
                                onclick="pilihKamar(this, ${k.id}, ${k.harga})"
                                style="cursor:pointer">
                                <img src="${foto}" class="card-img-top"
                                    style="height:150px;object-fit:cover">
                                <div class="card-body p-2">
                                    <div class="font-weight-bold">#${k.kode}</div>
                                    <div class="text-success font-weight-bold">
                                        Rp ${new Intl.NumberFormat('id-ID').format(k.harga)}
                                        <small>/bulan</small>
                                    </div>
                                    <small class="badge badge-primary">${k.khusus}</small>
                                </div>
                            </div>
                        </div>`;
                    });

                    $('#kamarGrid').html(html);
                },
                error: function () {
                    $('#kamarLoader').addClass('d-none');
                    alert('Gagal memuat kamar');
                }
            });
        }


        function pilihKamar(el, id, harga) {
            $('.card-kamar').css({'border':'none', 'background':'white'}).find('.card-body').css('color','inherit');
            $(el).css({'border':'3px solid #4e73df', 'background':'#f8f9fc'});
            $('#input_kamar_id').val(id);
            hargaKamarDipilih = harga;
            hitungLogikaSewa();
        }

        function hitungLogikaSewa() {
            let masuk = new Date($('#tgl_masuk').val());
            let keluar = new Date($('#tgl_keluar').val());

            if (!keluar || keluar <= masuk) return;

            // Hitung selisih hari
            let timeDiff = keluar.getTime() - masuk.getTime();
            let diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

            let tipe = 'Harian';
            let durasiFinal = diffDays;

            // Logika "Real Bulan"
            // Jika tanggal keluar sama dengan tanggal masuk di bulan berikutnya, dihitung 1 bulan.
            let isSameDay = masuk.getDate() === keluar.getDate();
            let diffMonths = (keluar.getFullYear() - masuk.getFullYear()) * 12 + (keluar.getMonth() - masuk.getMonth());

            if (isSameDay && diffMonths > 0) {
                tipe = 'Bulanan';
                durasiFinal = diffMonths;
            } else if (diffDays >= 30) {
                // Alternatif: jika lebih dari 30 hari tapi tanggal tidak sama, hitung bulanan (pembulatan)
                // Sesuai request: "jika 30 hari maka nilainya bulanan"
                tipe = 'Bulanan';
                durasiFinal = Math.floor(diffDays / 30);
            }

            $('#labelDurasi').text(durasiFinal + (tipe === 'Bulanan' ? ' Bulan' : ' Hari'));
            $('#labelTipe').text(tipe);
            $('#input_durasi').val(durasiFinal);
            $('#input_jenis_sewa').val(tipe);

            // Hitung Harga
            let hargaFasilitas = 0;
            $('.check-fasilitas:checked').each(function() {
                hargaFasilitas += parseFloat($(this).data('harga'));
            });

            let total = 0;
            if (tipe === 'Bulanan') {
                total = (hargaKamarDipilih + hargaFasilitas) * durasiFinal;
            } else {
                // Harga harian proporsional sesuai database (Harga/30)
                let hargaPerHari = (hargaKamarDipilih + hargaFasilitas) / 30;
                total = hargaPerHari * diffDays;
            }

            $('#textGrandTotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(Math.ceil(total)));
        }

        $(document).on('change', '.check-fasilitas', hitungLogikaSewa);

        $('#modalTokoOnline').on('shown.bs.modal', function () {
            $('.select2').select2({ dropdownParent: $('#modalTokoOnline') });
        });
    </script>
    @endpush
</x-admin-layout>