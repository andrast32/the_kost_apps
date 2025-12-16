<x-admin-layout>

    <div class="card">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Manajemen data penyewa</h3>

                @if (isset($jumlahSampah) && $jumlahSampah > 0)
                    <a href="{{ route('admin.data-user.penyewa.sampah') }}" class="btn btn-sm btn-outline-danger right ml-auto">
                        <i class="fas fa-trash-alt"></i>
                        Lihat sampah
                        <span class="badge badge-danger ml-1">{{ $jumlahSampah }}</span>
                    </a>
                @endif

                <button class="btn btn-sm btn-round btn-outline-primary ml-2" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah penyewa
                </button>

            </div>
        </div>

        <div class="card-body">
            <table id="laporan" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Nama penyewa</th>
                        <th>Email penyewa</th>
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

                                    <button type="button" class="btn btn-link text-primary" data-toggle="modal" data-target="#modalEdit-{{ $data->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button type="button" class="btn btn-link text-danger" onclick="confirmDelete({{ $data->id }}, '{{ $data->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </div>
                            </td>

                        </tr>

                    @empty
                        
                    @endforelse
                </tbody>

                <tfoot class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Nama penyewa</th>
                        <th>Email penyewa</th>
                        <th width="10%">Action</th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>

</x-admin-layout>
