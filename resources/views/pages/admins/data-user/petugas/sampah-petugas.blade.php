<x-admin-layout>

    <div class="card card-outline card-primary">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Sampah data petugas</h3>

                <a href="{{ route('admin.data-user.petugas.index') }}" class="btn btn-sm btn-outline-info ml-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

            </div>
        </div>

        <div class="card-body">
            <table id="data" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Nama petugas</th>
                        <th>Email petugas</th>
                        <th>Dihapus pada</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $data)

                        <tr align="center">

                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->deleted_at)->translatedFormat('d M Y ') }}</td>

                            <td>
                                <div class="btn-group">

                                    <button class="btn btn-outline-primary text-primary mr-2" onclick="Restore({{ $data->id }}, '{{ $data->name }}')">
                                        <i class="fas fa-trash-restore"></i>
                                    </button>

                                    <button class="btn btn-outline-danger text-danger mr-2" onclick="Delete({{ $data->id }}, '{{ $data->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    <form action="{{ route('admin.data-user.petugas.restore', $data->id) }}" id="restore-{{ $data->id }}" method="post" style="display: none">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    <form action="{{ route('admin.data-user.petugas.force-delete', $data->id) }}" id="delete-{{ $data->id }}" method="post" style="display: none">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-4">
                                <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada sampah petugas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Nama petugas</th>
                        <th>Email petugas</th>
                        <th>Dihapus pada</th>
                        <th>Action</th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>

    <script>

        function Restore(id, name) {
            Swal.fire({
                title: 'Kembalikan data ' + name + '?',
                text: "Data "+ name +" akan dikembalikan",
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
            })
        }

        function Delete(id, name) {
            Swal.fire({
                title: 'Hapus data ' + name +'?',
                text: "Data "+ name +" akan hilang selamanya",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-' + id).submit();
                }
            })
        }

    </script>

</x-admin-layout>
