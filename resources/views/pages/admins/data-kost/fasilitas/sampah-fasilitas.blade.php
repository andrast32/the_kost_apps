<x-admin-layout>

    <div class="card card-outline card-danger">

        <div class="card-header">
            <div class="d-flex aligin-items-center justify-content-between">

                <h3 class="card-title">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Sampah Fasilitas
                    <span class="badge badge-danger ml-1">{{ $jumlahSampah }}</span>
                </h3>

                <a href="{{ route('admin.data-kost.fasilitas.index') }}" class="btn btn-outline-primary btn-round ml-auto">
                    <i class="fas fa-aroow-left"></i> Kembali
                </a>

            </div>
        </div>

        <div class="card-body">

            <table id="data" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Kode fasilitas</th>
                        <th>Nama fasilitas</th>
                        <th>Harga</th>
                        <th>Dihapus pada</th>
                        <th>Foto</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($fasilitas as $data)
                        <tr align="center">

                            <td>{{ $loop->iteration }}</td>
                            <td><strong>#{{ $data->kode }}</strong></td>
                            <td>{{ $data->nama }}</td>

                            <td>
                                @if ($data->harga >= 5000)
                                    Rp. {{ number_format($data->harga, 0, ',', '.') }}
                                @else
                                    <strong>Gratis.</strong>
                                @endif
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($data->deleted_at)->translatedFormat('d M Y') }}
                            </td>

                            <td>
                                @if ($data->foto)
                                    <img src="{{ Storage::url('uploads/fasilitas/' . $data->foto) }}" alt="Foto fasilitas" width="100px" class="img-thumbnail">
                                @else
                                    <span class="text-muted">Tidak ada foto</span>
                                @endif
                            </td>

                            <td>
                                <div class="btn-group">

                                    <button class="btn btn-outline-primary text-primary mr-2" onclick="confirmRestore({{ $data->id }}, '{{ $data->kode }}', '{{ $data->nama }}')">
                                        <i class="fas fa-trash-restore"></i>
                                    </button>

                                    <form action="{{ route('admin.data-kost.fasilitas.restore', $data->id) }}" id="restore-form-{{ $data->id }}" method="post" style="display: none;">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    <button type="button" class="btn btn-outline-danger text-danger" onclick="confirmForceDelete({{ $data->id }}, '{{ $data->kode }}', '{{ $data->nama }}')">
                                        <i class="fas fa-times-circle"></i>
                                    </button>

                                    <form action="{{ route('admin.data-kost.fasilitas.force-delete', $data->id) }}" id="force-delete-form-{{ $data->id }}" method="POST" style="display: none">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-4">
                                <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada sampah fasilitas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>

    </div>

    <script>
        function confirmRestore(id, kode, nama) {
            Swal.fire({
                title: 'Kembalikan fasilitas dengan kode #' + kode + '?',
                text: "Fasilitas " + nama + " akan dikembalikan",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kembalikan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('restore-form-' + id).submit();
                }
            })
        }
        function confirmForceDelete(id, kode, nama) {
            Swal.fire({
                title: 'Hapus fasilitas dengan kode #' + kode + ' secara permanen?',
                text: "Fasilitas " + nama + " akan hilang selamanya",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('force-delete-form-' + id).submit();
                }
            })
        }
    </script>

</x-admin-layout>
