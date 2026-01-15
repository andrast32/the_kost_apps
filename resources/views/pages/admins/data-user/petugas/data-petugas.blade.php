<x-admin-layout>

    <div class="card">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Manajemen data petugas</h3>

                <button class="btn btn-sm btn-round btn-outline-primary right ml-auto" data-toggle="modal" data-target="#add">
                    <i class="fas fa-plus"></i> Tambah petugas
                </button>

                @if (isset($jumlahSampah) && $jumlahSampah > 0)
                    <a href="{{ route('admin.data-user.petugas.sampah') }}" class="btn btn-sm btn-outline-danger ml-2">
                        <i class="fas fa-trash-alt"></i>
                        Lihat sampah
                        <span class="badge badge-danger ml-1">{{ $jumlahSampah }}</span>
                    </a>
                @endif

            </div>
        </div>

        <div class="card-body">
            <table id="data" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Nama petugas</th>
                        <th>Email petugas</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $data)

                        <tr align="center">

                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->email }}</td>

                            <td>
                                <div class="btn-group">

                                    <button class="btn btn-link text-warning" onclick="Reset({{ $data->id }}, '{{ $data->name }}')">
                                        <i class="fas fa-key"></i>
                                    </button>

                                    <button class="btn btn-link text-danger" onclick="Delete({{ $data->id }}, '{{ $data->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form id="reset-{{ $data->id }}" action="{{ route('admin.data-user.petugas.update', $data->id) }}" method="post">
                                        @csrf @method('PUT')
                                    </form>

                                    <form id="delete-{{ $data->id }}" action="{{ route('admin.data-user.petugas.destroy', $data->id) }}" method="post">
                                        @csrf @method('DELETE')
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-4">
                                <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data petugas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Nama petugas</th>
                        <th>Email petugas</th>
                        <th width="10%">Action</th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>

    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header"></div>

                <form action="{{ route('admin.data-user.petugas.store') }}" method="post" enctype="multipart/form-data">

                    @csrf

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Nama petugas <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                                        <input type="text" name="name" class="form-control" placeholder="masukan nama petugas" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-outline-success">Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>

        function Reset(id, name) {
            Swal.fire({
                title: 'Reset password ' + name + '?',
                text: "Password akan direset menjadi (admin_4n4k_k0st.2026)!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reset-' + id).submit();
                }
            })
        }

        function Delete(id, name) {
            Swal.fire({
                title: 'Hapus data petugas ini?',
                text: "Data " + name + " akan dipindahkan ke sampah!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-' + id).submit();
                }
            })
        }

    </script>

</x-admin-layout>
