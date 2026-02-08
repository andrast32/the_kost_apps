<x-admin-layout>
    <div class="card card-outline card-primary">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Manajemen pemesanan kamar dan fasilitas</h3>

                <button class="btn btn-sm btn-round btn-outline-primary right ml-auto" data-toggle="modal" data-target="#add">
                    <i class="fas fa-plus"></i> Tambah pemesanan
                </button>

                @if (isset($Sampah) && $Sampah > 0)
                    <a href="{{ route('pemesanan.sampah') }}" class="btn btn-sm btn-round btn-outline-danger ml-2"></a>
                @endif

            </div>
        </div>

        <div class="card-body">
            <table id="data" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
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
                    @foreach ($items as $data)
                        <tr>

                            <td align="center">{{ $loop->iteration }}</td>

                            <td>
                                <div class="font-weight-bold text-dark">{{ $data->user->name }}</div>
                                <small class="badge badge-light border text-muted">{{ $data->user->biodata->jenis_kelamin }}</small>
                            </td>

                            <td>
                                <div class="font-weight-bold text-dark">{{ $data->kamar->khusus }}</div>
                                <small class="badge badge-light border text-muted">#{{ $data->kamar->kode }}</small>
                            </td>

                            <td align="center">
                                <span class="badge badge-info mt-1">{{ $data->jenis_sewa }}</span>
                            </td>

                            <td>
                                <div class="small">
                                    <span class="text-success font-weight-bold">In:</span> {{ \Carbon\Carbon::parse($data->tgl_masuk)->format('d/m/Y') }}<br>
                                </div>
                            </td>

                            <td>
                                <div class="small">
                                    <span class="text-danger font-weight-bold">Out:</span> {{ \Carbon\Carbon::parse($data->tgl_keluar)->format('d/m/Y') }}
                                </div>
                            </td>

                            <td align="center">
                                @if ($data->status == 'Aktif')
                                    <span class="badge badge-success">{{ $data->status }}</span>
                                @elseif ($data->status == 'Menunggu Pembayaran')
                                    <span class="badge badge-warning">{{ $data->status }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $data->status }}</span>
                                @endif
                            </td>

                            <td align="center">

                                <button class="btn btn-link text-info" data-toggle="modal" data-target="#info-{{ $data->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <button class="btn btn-link text-danger" onclick="Delete({{ $data->id }}, '$data')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

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
                        <i class="fas fa-cart-plus mr-2"></i> CHECK-IN THE KOST
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <form action="{{ route('pemesanan.store') }}" method="post" id="formBooking" enctype="multipart/form-data">

                    @csrf

                    <div class="modal-body bg-light">
                        <div class="row">

                        {{-- sisi kiri start--}}
                            <div class="col-lg-8 border-right p-4 bg-white">

                                <div class="form-group mb-4">
                                    <label><i class="fas fa-user-plus"></i> Pilih penyewa <span class="text-danger">*</span></label>

                                    <select name="user_id" id="user_id" class="form-control" required onchange="loadKamarTersedia(this.value)">
                                        <option value=""> Pilih Penyewa </option>
                                        @foreach ($penyewas as $p)
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
                        {{-- sisi kiri end--}}

                        {{-- sisi kanan start--}}

                            <div class="col-lg-4 p-4 d-flex flex-column bg-white">

                                <div class="col md-8">
                                    <div class="form-group">
                                            <label><i class="fas fa-box"></i> Tambah Fasilitas</label>
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

                                <div class="col md-8">
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

                        {{-- sisi kanan end--}}

                        </div>
                    </div>

                    <div class="modal-footer"></div>

                </form>

            </div>
        </div>
    </div>

    @foreach ($items as $info)
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

                </div>
            </div>
        </div>
    @endforeach

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

            $('#add').on('shown.bs.modal', function () {
                $('.select2').select2({ dropdownParent: $('#add') });
            });
        </script>
    @endpush

</x-admin-layout>