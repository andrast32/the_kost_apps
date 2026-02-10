<x-admin-layout>
    <div class="card card-outline card-primary">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Sampah pemesanan kamar dan fasilitas</h3>

                <a href="{{ route('pemesanan.index') }}" class="btn btn-sm btn-round btn-outline-info ml-2">
                    <i class="fas fa-arrow-left"></i> 
                    kembali
                </a>

            </div>
        </div>

        <div class="card-body">
            <table id="data" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th width="5%">kode</th>
                        <th>penyewa</th>
                        <th>Tipe & kode kamar</th>
                        <th>Tipe Sewa</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Keluar</th>
                        <th>Dihapus pada</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($items as $data)
                        <tr>

                            <td align="center">{{ $loop->iteration }}</td>

                            <td align="center">#{{ $data->kode_pemesanan }}</td>

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
                                {{ \Carbon\Carbon::parse($data->tgl_masuk)->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($data->tgl_keluar)->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($data->delete_at)->format('d/m/Y') }}
                            </td>

                            <td align="center">

                                <button class="btn btn-link text-primary" onclick="Restore({{ $data->id }}, '{{ $data->id }}')">
                                    <i class="fas fa-trash-restore"></i>
                                </button>

                                <button class="btn btn-link text-danger" onclick="Delete({{ $data->id }}, '{{ $data->kode_pemesanan }}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                                <form id="restore-{{ $data->id }}" action="{{ route('pemesanan.restore', $data->id) }}" method="post">@csrf @method('put')</form>

                                <form id="delete-{{ $data->id }}" action="{{ route('pemesanan.force', $data->id) }}" method="post">@csrf @method('DELETE')</form>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center p-4">
                                <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada sampah pemesanan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</x-admin-layout>

<script>

    function Restore(id, kode_pemesanan) {
        Swal.fire({
            title: 'Kembalikan data pemesanan dengan kode #' + kode_pemesanan + '?',
            text: "data tersebut akan dikembalikan",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kembalikan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('restore-' + id).submit();
            }
        });
    }

    function Delete(id, kode_pemesanan) {
        Swal.fire({
            title: 'Hapus data pemesanan dengan kode #' + kode_pemesanan + '?',
            text: "data tersebut akan dihapus permanen",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-' + id).submit();
            }
        });
    }

</script>