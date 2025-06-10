@extends('layouts.template')

@section('page-title', 'Sertifikat Peserta Ujian')
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
                <button onclick="modalActionHapusEdit('{{ route('sertif.import') }}')" class="btn btn-sm btn-info mt-1">
                    Import Data Sertifikat Peserta
                </button>
                <button onclick="modalActionHapusEdit('{{ route('sertif.create_ajax') }}')" class="btn btn-sm btn-success mt-1">
                    Tambah Data
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

            <table id="table_sertif" class="table table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>NIM</th>
                        <th>Status Pengambilan</th>
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

    function modalActionHapusEdit(url) {
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

let dataSertif;

$(document).ready(function() {
        dataSertif = $('#table_sertif').DataTable({
        processing: true,
        serverSide: true,
        // deferLoading: 0, // mencegah load otomatis
        ajax: {
            url: "{{ route('sertif.getSertif') }}",
            type: "GET",
            data: function(d) {
                d.tahun = $('#filter_tahun').val(); // filter tahun
            }
        },
        columns: [
            { data: 'nama_lengkap', name: 'user.nama_lengkap' },
            { data: 'username', name: 'user.username' },
            {
                data: 'is_taken',
                name: 'is_taken',
                orderable: false,      // <--- PENTING: Matikan pengurutan untuk kolom ini
                searchable: false,
                render: function(data, type, row) {
                    const selectedTrue = data == 1 ? 'selected' : '';
                    const selectedFalse = data == 0 ? 'selected' : '';
                    return `
                        <select class="form-select form-select-sm change-status" data-id="${row.sertifikat_id}">
                            <option value="1" ${selectedTrue}>Sudah Diambil</option>
                            <option value="0" ${selectedFalse}>Belum Diambil</option>
                        </select>
                    `;
                }
            },
            {
                data: null,
                name: 'hapus',
                orderable: false,      // <--- PENTING: Matikan pengurutan untuk kolom ini
                searchable: false,
                render: function(data, type, row) {
                    let url_hapus = `{{ route('sertif.confirm_ajax', ['id' => ':id']) }}`;
                    url_hapus = url_hapus.replace(':id', row.sertifikat_id);

                    return `<button button onclick="modalActionHapusEdit('${url_hapus}')" class="btn btn-sm btn-danger">Hapus</button>`;
                }
            }
        ],
    });

    $('#filter_tahun').on('change', function () {
        dataSertif.ajax.reload();
    });

    $('#table_sertif').on('change', '.change-status', function () {
    const select = $(this);
    const sertifikat_id = select.data('id');
    const newValue = select.val(); // 1 atau 0

    if (!sertifikat_id) {
        console.error('ID tidak ditemukan');
        return;
    }

    $.ajax({
        url: "{{ route('sertif.toggleStatus') }}", // Buat route ini di Laravel
        method: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            sertifikat_id: sertifikat_id,
            value: newValue
        },
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: response.message
            }).then((result) => {
                if (result.isConfirmed) {
                    dataSertif.ajax.reload();
                }
            });
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Status gagal diperbarui.'
            });
        }
    });
});
});
</script>


@endpush
