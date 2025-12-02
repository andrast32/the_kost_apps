<x-admin-layout>

    <div class="card border-danger">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Sampah data kamar
                    <span class="badge badge-danger ml-1">{{ $jumlahSampah }}</span>
                </h3>

                <a href="{{ route('admin.data-kost.kamar.index') }}" class="btn btn-outline-primary btn-round ml-auto">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

            </div>
        </div>

        <div class="card-body">

            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Perhatian!</strong> Data yang dihapus permanen tidak dapat dikembalikan lagi.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <table id="data" class="table table-bordered table-striped table-hover">

                <thead>
                    <tr align="center">
                        <th>No</th>
                        <th>Kode</th>
                        <th>Harga</th>
                        <th>Dihapus pada</th>
                        <th>Foto</th>
                        <th width="20%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($kamars as $kamar)
                        <tr align="center">

                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $kamar->kode_kamar }}</strong></td>
                            <td>Rp {{ number_format($kamar->harga, 0, ',', '.') }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($kamar->deleted_at)->translatedFormat('d M Y') }}
                            </td>

                            <td>
                                @if($kamar->foto)
                                    <img src="{{ Storage::url('uploads/kamar/' . $kamar->foto) }}" alt="Foto" width="100px" class="img-thumbnail">
                                @else
                                    <span class="text-muted">Tidak ada foto</span>
                                @endif
                            </td>

                            <td>
                                <div class="btn-group">

                                    <form action="{{ route('admin.data-kost.kamar.restore', $kamar->id) }}" method="post" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button class="btn btn-outline-primary text-primary mr-2">
                                            <i class="fas fa-trash-restore"></i>
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-outline-danger text-danger" onclick="confirmForceDelete({{ $kamar->id }}, '{{ $kamar->kode_kamar }}')">
                                        <i class="fas fa-times-circle"></i>
                                    </button>

                                    <form action="{{ route('admin.data-kost.kamar.force-delete', $kamar->id) }}" id="force-delete-form-{{ $kamar->id }}" method="POST" style="display: none">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada sampah kamar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>

    </div>

    <script>
        function confirmForceDelete(id, kode) {
            Swal.fire({
                title: 'Yakin mau hapus Permanen?',
                text: "Data kamar " + kode + " akan hilang selamanya",
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
