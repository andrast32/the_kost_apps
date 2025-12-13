<x-admin-layout>

    <div class="card">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Manajemen data petugas</h3>

                @if (isset($jumlahSampah) && $jumlahSampah > 0)
                    <a href="{{ route('admin.data-user.petugas.sampah') }}" class="btn btn-sm btn-outline-danger right ml-auto">
                        <i class="fas fa-trash-alt"></i>
                        Lihat sampah
                        <span class="badge badge-danger ml-1">{{ $jumlahSampah }}</span>
                    </a>
                @endif

                <button class="btn btn-sm btn-round btn-outline-primary ml-2" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah petugas
                </button>

            </div>
        </div>

        <div class="card-body">
            <table id="laporan" class="table table-bordered table-striped table-hover">

                <thead class="bg-navy">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Nama petugas</th>
                        <th>Email petugas</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>

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

</x-admin-layout>
