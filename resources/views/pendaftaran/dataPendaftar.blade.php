@extends('layouts.template')

@section('page-title', 'Pendaftar')
@section('card-title')
    <div class="row align-items-center">
        <div class="col">
            <h5 class="mb-0 fw-bold">Data Pendaftar</h5>
        </div>
        <div class="col-auto">
            <div class="input-group input-group-sm">
                <label class="input-group-text bg-primary text-white" for="filter_tahun">
                    <i class="fas fa-calendar-alt me-1"></i> Tahun
                </label>
                <select id="filter_tahun" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach (range(date('Y'), 2020) as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table id="table_pendaftar" class="table table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>NIM</th>
                        <th>NIK</th>
                        <th>Nomor Whatsapp</th>
                        <th>Alamat Asal</th>
                        <th>Alamat Sekarang</th>
                        <th>Jurusan</th>
                        <th>Program Studi</th>
                        <th>Kampus</th>
                        <th>Pas Foto</th>
                        <th>KTM atau KTP</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

@endsection

@push('js')
<script>
    let dataPendaftaran;

$(document).ready(function() {
        dataPendaftar = $('#table_pendaftar').DataTable({
        processing: true,
        serverSide: true,
        // deferLoading: 0, // mencegah load otomatis
        ajax: {
            url: "{{ route('pendaftaran.getPendaftar') }}",
            type: "GET",
            data: function(d) {
                d.tahun = $('#filter_tahun').val(); // <-- tambahkan baris ini
            }
        },
        columns: [
            { data: 'nama_lengkap', name: 'nama_lengkap' },
            { data: 'username', name: 'username' },
            { data: 'nik', name: 'nik' },
            { data: 'no_wa', name: 'no_wa' },
            { data: 'alamat_asal', name: 'alamat_asal' },
            { data: 'alamat_sekarang', name: 'alamat_sekarang' },
            { data: 'jurusan', name: 'jurusan' },
            { data: 'program_studi', name: 'program_studi' },
            { data: 'kampus', name: 'kampus' },
            { data: 'pas_foto', name: 'pas_foto', orderable: false, searchable: false },
            { data: 'ktm_atau_ktp', name: 'ktm_atau_ktp', orderable: false, searchable: false },
        ],
        responsive: true
    });
    $('#filter_tahun').on('change', function() {
        dataPendaftar.ajax.reload();
    });

});

</script>


@endpush
