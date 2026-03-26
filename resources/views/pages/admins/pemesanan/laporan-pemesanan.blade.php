<x-admin-layout>
    <div class="card card-outline card-primary">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Laporan data pemesanan kamar dan fasilitas</h3>

                <a href="{{ route('pemesanan.index') }}" class="btn btn-round btn-outline-primary right ml-auto">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>

            </div>
        </div>

        <div class="card-body">
            <table id="laporan" class="table table-bordered table-striped table-hover">

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
                                    <span class="badge badge-warning">{{ $data->status }}</span>
                                @elseif ($data->status == 'Selesai')
                                    <span class="badge badge-danger">{{ $data->status }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ $data->status }}</span>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>


</x-admin-layout>