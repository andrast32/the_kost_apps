<x-admin-layout>
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="d-flex justify-content-beetween align-items-center">
                <h3 class="card-title">
                    Manajemen Data Pemesanan kamar dan fasilitas
                </h3>

                <button class="btn btn-sm btn-round btn-outline-primary right ml-auto" data-target="#add" data->
                    <i class="fas fa-plus"></i> tambah pemesanan
                </button>

                @if (isset($Sampah) && Sampah > 0)
                    <a href="" class="btn btm-sm btn-outline-danger ml-2"></a>
                @endif

            </div>
        </div>
    </div>
</x-admin-layout>
