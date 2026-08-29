<x-admin-layout>
    <div class="card card-outline card-primary">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Manajemen pemesanan kamar dan fasilitas</h3>

                <a href="{{ route('pemesanan.index') }}" class="btn btn-sm btn-round btn-outline-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

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

                                @else
                                    <span class="badge badge-secondary">{{ $data->status }}</span>
                                @endif

                            </td>

                            <td align="center">

                                <button class="btn btn-link text-info" data-toggle="modal" data-target="#info-{{ $data->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <button class="btn btn-link text-danger" onclick="Delete({{ $data->id }}, '{{ $data->kode_pemesanan }}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                                <form id="delete-{{ $data->id }}" action="{{ route('pemesanan.destroy', $data->id) }}" method="post">@csrf @method('DELETE')</form>

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>


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

                function loadKamarTersedia(userId) {
                    if (!userId) return;

                    $('#kamarGrid').html('');

                    $.ajax({
                        url: "{{ route('pemesanan.getKamars') }}",
                        method: "GET",
                        data: { user_id: userId },
                        success: function (res) {

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
                        }
                    });
                }

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
                        text: 'pemesanan kamar dengan kode tersebut akan dibayar'
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