<x-admin-layout>
    
    <div class="card">

        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">

                <h3 class="card-title">Manajemen data fasilitas</h3>

                @if (isset($jumlahSampah) && $jumlahSampah > 0)
                    <a href="{{ route('admin.data-kost.fasilitas.sampah') }}" class="btn btn-sm btn-outline-danger right ml-auto">
                        <i class="fas fa-trash-alt"></i>
                        Lihat Sampah
                        <span class="badge badge-danger ml-1">{{ $jumlahSampah }}</span>
                    </a>
                @endif

                <button class="btn btn-sm btn-round btn-outline-primary ml-2" data-toggle="modal" data-target="modalTambah">
                    <i class="fas fa-plus"></i>Tambah fasilitas
                </button>

            </div>
        </div>

    </div>

</x-admin-layout>
