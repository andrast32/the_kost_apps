<x-admin-layout>

    <div class="card card-outline card-primary">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Sampah data penyewa</h3>

                <a href="{{ route('admin.data-user.penyewa.index') }}" class="btn btn-sm btn-outline-info ml-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

            </div>
        </div>

        <div class="card-body">
            <table id="data" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>id</th>
                        <th>Nama penyewa</th>
                        <th>Email penyewa</th>
                        <th>Terdaftar sejak</th>
                        <th>Dihapus pada</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $data)

                        <tr align="center">

                            <td>{{ $loop->iteration }}</td>
                            <td>#{{ $data->id }}</td>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d M Y ') }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->deleted_at)->translatedFormat('d M Y ') }}</td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-4">
                                <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada sampah penyewa.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>id</th>
                        <th>Nama penyewa</th>
                        <th>Email penyewa</th>
                        <th>Terdaftar sejak</th>
                        <th>Dihapus pada</th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>

</x-admin-layout>
