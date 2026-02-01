<x-admin-layout>

<style>
.pilih-kamar,.pilih-fasilitas{cursor:pointer;transition:.2s}
.pilih-kamar:hover,.pilih-fasilitas:hover{transform:scale(1.02)}
.pilih-kamar.active{border:2px solid #007bff;box-shadow:0 0 0 3px rgba(0,123,255,.25);position:relative}
.pilih-fasilitas.active{border:2px solid #28a745;box-shadow:0 0 0 3px rgba(40,167,69,.25);position:relative}
.pilih-kamar.active::after,.pilih-fasilitas.active::after{
    content:"✓ Dipilih";position:absolute;top:8px;right:8px;
    background:#000;color:#fff;padding:3px 7px;font-size:11px;border-radius:4px
}
</style>

<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Manajemen Pemesanan</h3>
        <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#addPemesanan">
            <i class="fas fa-plus"></i> Tambah Pemesanan
        </button>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead class="bg-navy text-center">
                <tr>
                    <th>No</th><th>Penyewa</th><th>Kamar</th>
                    <th>Masuk</th><th>Keluar</th><th>Total</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $p)
                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->user->name }}</td>
                    <td>#{{ $p->kamar->kode }}</td>
                    <td>{{ $p->tgl_masuk }}</td>
                    <td>{{ $p->tgl_keluar }}</td>
                    <td>Rp {{ number_format($p->total_harga,0,',','.') }}</td>
                    <td>
                        <span class="badge badge-{{ $p->status=='Aktif'?'success':'secondary' }}">
                            {{ $p->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted p-4">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================= MODAL TAMBAH ================= --}}
<div class="modal fade" id="addPemesanan">
<div class="modal-dialog modal-xl"><div class="modal-content">
<form action="{{ route('admin.pemesanan.store') }}" method="POST">
@csrf

<div class="modal-header bg-primary">
    <h5 class="modal-title"><i class="fas fa-shopping-cart"></i> Tambah Pemesanan</h5>
    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
</div>

<div class="modal-body">

{{-- PENYEWA --}}
<div class="form-group">
    <label>Penyewa</label>
    <select name="user_id" id="userSelect" class="form-control">
        <option value="">-- Pilih Penyewa --</option>
        @foreach($penyewa as $u)
            <option value="{{ $u->id }}" data-gender="{{ $u->jenis_kelamin }}">
                {{ $u->name }} ({{ $u->jenis_kelamin }})
            </option>
        @endforeach
    </select>
</div>

{{-- KAMAR --}}
<label>Kamar</label>
<div class="row">
@foreach($kamars as $k)
<div class="col-md-4 kamar-card d-none" data-gender="{{ $k->jenis_kelamin }}">
    <div class="card pilih-kamar" data-harga="{{ $k->harga }}">
        <input type="radio" name="kamar_id" value="{{ $k->id }}" hidden required>
        <img src="{{ Storage::url('uploads/kamar/'.$k->foto) }}"
             class="card-img-top" style="height:160px;object-fit:cover">
        <div class="card-body">
            <strong>#{{ $k->kode }}</strong>
            <p class="text-muted">{{ $k->deskripsi }}</p>
            <b>Rp {{ number_format($k->harga,0,',','.') }} / bulan</b>
        </div>
    </div>
</div>
@endforeach
</div>

{{-- FASILITAS --}}
<label class="mt-3">Fasilitas</label>
<div class="row">
@foreach($fasilitas as $f)
<div class="col-md-3">
    <div class="card pilih-fasilitas" data-harga="{{ $f->harga }}">
        <input type="checkbox" name="fasilitas_ids[]" value="{{ $f->id }}" hidden>
        <img src="{{ Storage::url('uploads/fasilitas/'.$f->foto) }}"
             class="card-img-top" style="height:120px;object-fit:cover">
        <div class="card-body">
            <strong>{{ $f->nama_fasilitas }}</strong><br>
            Rp {{ number_format($f->harga,0,',','.') }} / bulan
        </div>
    </div>
</div>
@endforeach
</div>

{{-- TANGGAL --}}
<div class="row mt-3">
    <div class="col-md-4">
        <label>Tanggal Masuk</label>
        <input type="date" class="form-control" value="{{ $now->toDateString() }}" readonly>
        <input type="hidden" name="tgl_masuk" value="{{ $now->toDateString() }}">
    </div>
    <div class="col-md-4">
        <label>Tanggal Keluar</label>
        {{-- PENTING: ADA name="" --}}
        <input type="date" id="tglKeluar" name="tgl_keluar" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label>Durasi</label>
        <input type="text" id="durasiText" class="form-control" readonly>
    </div>
</div>

<input type="hidden" name="durasi" id="durasi">
<input type="hidden" name="jenis_sewa" id="jenisSewa">

{{-- TOTAL --}}
<div class="alert alert-info mt-3">
    <h5><i class="fas fa-receipt"></i> Total Harga</h5>
    <h3 id="totalHargaText">Rp 0</h3>
    <small class="text-muted">
        * Harga harian dihitung dari harga bulanan / 30 hari
    </small>
</div>

</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-success" id="btnSimpan" disabled>
        <i class="fas fa-save"></i> Simpan
    </button>

</div>

</form>
</div></div></div>

</x-admin-layout>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // elemen
    const userSelect = document.getElementById('userSelect');
    const kamarCards = document.querySelectorAll('.kamar-card');
    const pilihKamarCards = document.querySelectorAll('.pilih-kamar');
    const pilihFasilitasCards = document.querySelectorAll('.pilih-fasilitas');
    const tglKeluar = document.getElementById('tglKeluar');
    const durasiText = document.getElementById('durasiText');
    const durasiInput = document.getElementById('durasi');
    const jenisSewaInput = document.getElementById('jenisSewa');
    const totalHargaText = document.getElementById('totalHargaText');
    const btnSimpan = document.getElementById('btnSimpan');

    let hargaBulananKamar = 0, hargaBulananFasilitas = 0, durasi = 0, jenis = 'Harian';
    const rupiah = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

    const hitung = () => {
        if (!durasi || durasi <= 0) {
            totalHargaText.innerText = 'Rp 0';
            return;
        }
        let total = 0;
        if (jenis === 'Bulanan') {
            total = (hargaBulananKamar + hargaBulananFasilitas) * durasi;
        } else {
            total = ((hargaBulananKamar / 30) + (hargaBulananFasilitas / 30)) * durasi;
        }
        totalHargaText.innerText = rupiah(total);
    };

    const tryEnableSave = () => {
        const kamarChecked = Array.from(document.querySelectorAll('input[name="kamar_id"]')).some(i=>i.checked);
        const validDurasi = durasi && durasi > 0;
        btnSimpan.disabled = !(kamarChecked && validDurasi);
    };

    // filter kamar by gender
    if (userSelect) {
        const applyGenderFilter = () => {
            const opt = userSelect.selectedOptions[0];
            const g = opt && opt.dataset.gender ? opt.dataset.gender.toString().toLowerCase().trim() : '';
            kamarCards.forEach(c => {
                const kg = c.dataset.gender ? c.dataset.gender.toString().toLowerCase().trim() : '';
                let show = false;
                if (!g) {
                    show = false;
                } else if (g === 'keluarga') {
                    show = true;
                } else if (kg === 'keluarga') {
                    show = true;
                } else if (kg === g) {
                    show = true;
                }
                c.classList.toggle('d-none', !show);
            });
            // jika kamar yang dipilih sekarang tersembunyi, reset pilihannya
            document.querySelectorAll('input[name="kamar_id"]').forEach(r => {
                const card = r.closest('.kamar-card');
                if (card && card.classList.contains('d-none')) {
                    r.checked = false;
                }
            });
            // sesuaikan tampilan active pada kartu kamar
            pilihKamarCards.forEach(x => {
                const r = x.querySelector('input[type="radio"]');
                x.classList.toggle('active', r && r.checked);
            });
            tryEnableSave();
        };
        userSelect.addEventListener('change', applyGenderFilter);
        // jalankan sekali saat load (jika ada nilai awal)
        applyGenderFilter();
    }

    // pilih kamar
    pilihKamarCards.forEach(card => {
        card.addEventListener('click', () => {
            pilihKamarCards.forEach(x => x.classList.remove('active'));
            card.classList.add('active');
            const radio = card.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            hargaBulananKamar = parseFloat(card.dataset.harga) || 0;
            hitung();
            tryEnableSave();
        });
    });

    // pilih fasilitas
    pilihFasilitasCards.forEach(card => {
        card.addEventListener('click', () => {
            const cb = card.querySelector('input[type="checkbox"]');
            if (!cb) return;
            cb.checked = !cb.checked;
            card.classList.toggle('active', cb.checked);
            hargaBulananFasilitas = 0;
            document.querySelectorAll('.pilih-fasilitas input:checked').forEach(checkedInput => {
                const p = checkedInput.closest('.pilih-fasilitas');
                hargaBulananFasilitas += parseFloat(p.dataset.harga) || 0;
            });
            hitung();
        });
    });

    // helper: parse date as UTC (avoid timezone shift)
    const toUTCDate = (dateStr) => {
        const parts = dateStr.split('-').map(Number);
        return Date.UTC(parts[0], parts[1]-1, parts[2]);
    };

    // hitung durasi & jenis
    if (tglKeluar) {
        tglKeluar.addEventListener('change', () => {
            const masukStr = "{{ $now->toDateString() }}";
            if (!tglKeluar.value) { return; }
            const mUTC = toUTCDate(masukStr);
            const kUTC = toUTCDate(tglKeluar.value);
            if (kUTC <= mUTC) {
                alert('Tanggal keluar harus setelah masuk');
                tglKeluar.value = '';
                durasiText.value = '';
                durasiInput.value = '';
                jenisSewaInput.value = '';
                tryEnableSave();
                return;
            }
            const oneDay = 24*60*60*1000;
            const hari = Math.ceil((kUTC - mUTC) / oneDay);
            // Default: harian
            jenis = 'Harian';
            durasi = hari;
            // Jika tanggal hari sama -> hitung selisih bulan penuh
            const m = new Date(masukStr);
            const k = new Date(tglKeluar.value);
            if (m.getDate() === k.getDate()) {
                const bln = (k.getFullYear() - m.getFullYear())*12 + (k.getMonth() - m.getMonth());
                if (bln >= 1) { jenis = 'Bulanan'; durasi = bln; }
            } else if (hari % 30 === 0) {
                // juga treat sebagai bulanan bila kelipatan 30 hari
                jenis = 'Bulanan';
                durasi = Math.round(hari / 30);
            }
            durasiText.value = jenis === 'Bulanan' ? durasi + ' Bulan' : durasi + ' Hari';
            durasiInput.value = durasi;
            jenisSewaInput.value = jenis;
            hitung();
            tryEnableSave();
        });
    }
});
</script>
