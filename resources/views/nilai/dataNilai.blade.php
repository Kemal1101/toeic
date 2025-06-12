@extends('layouts.template')

@section('page-title', 'Nilai Peserta Ujian')
@section('card-title')
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
@endsection

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="card-tools">
                <button onclick="modalAction('{{ route('nilai.import') }}')" class="btn btn-sm btn-info mt-1">
                    Import Nilai
                </button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table id="table_nilai" class="table table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>NIM</th>
                        <th>Skor Listening</th>
                        <th>Skor Reading</th>
                        <th>Total Skor</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

@endsection

<!-- Modal Delete -->
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
        data-backdrop="static" data-keyboard="false" data-width="75%">
</div>


@push('js')
<script>

    function modalAction(url) {
    $.get(url, function(response) {
        $('#myModal').remove(); // bersihkan modal sebelumnya
        $('body').append(response); // tambahkan modal baru ke body
        const modalEl = document.getElementById('myModal');
        const modalInstance = new bootstrap.Modal(modalEl);
        modalInstance.show();
    }).fail(function() {
        alert('Gagal memuat modal.');
    });
    }

let dataNilai;

$(document).ready(function() {
        dataNilai = $('#table_nilai').DataTable({
        processing: true,
        serverSide: true,
        // deferLoading: 0, // mencegah load otomatis
        ajax: {
            url: "{{ route('nilai.getNilai') }}",
            type: "GET",
            data: function(d) {
                d.tahun = $('#filter_tahun').val(); // filter tahun
            }
        },
        columns: [
            { data: 'nama_lengkap', name: 'user.nama_lengkap' },
            { data: 'username', name: 'user.username' },
            { data: 'listening', name: 'listening' },
            { data: 'reading', name: 'reading' },
            { data: 'total', name: 'total' },
            {
                data: null,
                name: 'hapus',
                orderable: false,      // <--- PENTING: Matikan pengurutan untuk kolom ini
                searchable: false,
                render: function(data, type, row) {
                    let url_hapus = `{{ route('nilai.confirm_ajax', ['id' => ':id']) }}`;
                    url_hapus = url_hapus.replace(':id', row.nilai_id);

                    return `<button button onclick="modalAction('${url_hapus}')" class="btn btn-sm btn-danger">Hapus</button>`;
                }
            }
        ],
    });

    $('#filter_tahun').on('change', function () {
        dataNilai.ajax.reload();
    });

});


</script>


@endpush
