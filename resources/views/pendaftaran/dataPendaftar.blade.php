@extends('layouts.template')

@section('page-title', 'Pendaftar')
@section('card-title')
    <div class="row align-items-center">
        <div class="col">
            <h5 class="mb-3 fw-bold">Data Pendaftar</h5>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="row">
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
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <label class="input-group-text bg-primary text-white" for="filter_verifikasi_data">
                            <i class="fas fa-calendar-alt me-1"></i> Status Verifikasi
                        </label>
                        <select id="filter_verifikasi_data" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach (['PENDING', 'DITOLAK', 'TERVERIFIKASI'] as $verifikasi_data)
                                <option value="{{ $verifikasi_data }}">{{ $verifikasi_data }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <!-- Switch Button -->
                <div class="switch-container d-flex align-items-center gap-2 flex-nowrap">
                    <span class="fw-bold mb-0">(Pendaftaran)</span>

                    <div class="custom-switch" id="switch-pendaftaran" data-status="{{ $status }}">
                        <div class="switch-slider"></div>
                    </div>

                    <span class="switch-label fw-bold mb-0 text-nowrap" id="switch-label">
                        {{ $status === 'y' ? 'Dibuka' : 'Ditutup' }}
                    </span>
                </div>
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
                        <th>Status Verifikasi</th>
                        <th>Verifikasi</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3 mb-1">
        <button type="button" class="btn btn-sm btn-primary mx-1"
            onclick="modalActionExportPdf('{{ route('data_pendaftar.modal_export_pdf') }}')">
            Export Pendaftar PDF
        </button>
        <button type="button" class="btn btn-sm btn-success mx-1"
            onclick="modalActionExportPdf('{{ route('data_pendaftar.modal_export_excel') }}')">
            Export Pendaftar Excel
        </button>
    </div>

@endsection

<!-- Modal Verifikasi -->
<div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-labelledby="modalVerifikasiLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalVerifikasiLabel">Detail Pendaftar</h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalVerifikasiContent">
        <!-- Konten dari AJAX akan dimuat di sini -->
      </div>
    </div>
  </div>
</div>

<!-- Modal Notes Tolak -->
<div id="modal-container"></div>

<!-- Modal Delete -->
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
        data-backdrop="static" data-keyboard="false" data-width="75%">
</div>


@push('js')
<script>
    function modalAction(url, verifikasi_data) {
        if(verifikasi_data === 'PENDING') {
            $('#modalVerifikasi').modal('show');
            $('#modalVerifikasiContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');

            $.get(url, function(res) {
                $('#modalVerifikasiContent').html(res);
            }).fail(function(err) {
                $('#modalVerifikasiContent').html('<div class="alert alert-danger">Gagal memuat data.</div>');
            });
        }

    }
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

    function modalActionExportPdf(url) {
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

let dataPendaftaran;

$(document).ready(function() {
    dataPendaftaran = $('#table_pendaftar').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('pendaftaran.getPendaftar') }}",
            type: "GET",
            data: function(d) {
                d.tahun = $('#filter_tahun').val();
                d.verifikasi_data = $('#filter_verifikasi_data').val();
            }
        },
        columns: [
            // Kolom 'nama_lengkap' diambil dari relasi user, jadi tetap searchable
            { data: 'nama_lengkap', name: 'user.nama_lengkap' }, // <--- PENTING: Ubah name menjadi 'user.nama_lengkap'
            { data: 'username', name: 'user.username' }, // <--- PENTING: Ubah name menjadi 'user.username'
            {
                data: 'verifikasi_data',
                name: 'verifikasi_data', // Ini mungkin ada di tabel data_pendaftaran, biarkan searchable
                render: function(data, type, row) {
                    let badgeClass = 'badge rounded-pill p-2 fs-7 fw-normal';
                    switch(data) {
                        case 'PENDING':
                            badgeClass += ' bg-warning text-dark';
                            break;
                        case 'DITOLAK':
                            badgeClass += ' bg-danger text-white';
                            break;
                        case 'TERVERIFIKASI':
                            badgeClass += ' bg-success text-white';
                            break;
                        default:
                            badgeClass += ' bg-secondary text-white';
                    }
                    return `<span class="${badgeClass}">${data}</span>`;
                }
            },
            {
                data: null,
                name: 'aksi',
                orderable: false,      // <--- PENTING: Matikan pengurutan untuk kolom ini
                searchable: false,     // <--- PENTING: Matikan pencarian untuk kolom ini
                render: function(data, type, row) {
                    let url_verifikasi = `{{ route('pendaftaran.verifikasi', ['id' => ':id']) }}`;
                    url_verifikasi = url_verifikasi.replace(':id', row.data_pendaftaran_id);

                    let disabled = (row.verifikasi_data === 'DITOLAK' || row.verifikasi_data === 'TERVERIFIKASI')
                        ? 'disabled'
                        : '';
                    return disabled ? `<button onclick="modalAction('${url_verifikasi}', '${row.verifikasi_data}')" class="btn btn-sm btn-secondary" ${disabled}>Verifikasi</button>`
                       : `<button onclick="modalAction('${url_verifikasi}', '${row.verifikasi_data}')" class="btn btn-sm btn-primary" ${disabled}>Verifikasi</button>`;
                }
            },
            {
                data: null,
                name: 'hapus',
                orderable: false,      // <--- PENTING: Matikan pengurutan untuk kolom ini
                searchable: false,     // <--- PENTING: Matikan pencarian untuk kolom ini
                render: function(data, type, row) {
                    let url_hapus = `{{ route('pendaftaran.confirm_ajax', ['id' => ':id']) }}`;
                    url_hapus = url_hapus.replace(':id', row.data_pendaftaran_id);

                    return `<button onclick="modalActionHapusEdit('${url_hapus}')" class="btn btn-sm btn-danger">Hapus</button>`;
                }
            }
        ],
    });
});

    $('#filter_tahun, #filter_verifikasi_data').on('change', function () {
        dataPendaftaran.ajax.reload();
    });


$(document).ready(function () {
        const $switch = $('#switch-pendaftaran');
        const $label = $('#switch-label');
        const initialStatus = $switch.data('status');

        // Apply initial style
        if (initialStatus === 'y') {
            $switch.addClass('active');
        }

        $switch.on('click', function () {
            const isActive = $(this).hasClass('active');
            const newValue = isActive ? 'n' : 'y';

            // UI update
            $(this).toggleClass('active');
            $label.text(newValue === 'y' ? 'Dibuka' : 'Ditutup');

            // Send to server
            $.ajax({
                url: '{{ route("setting.togglePendaftaran") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    value: newValue
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    }).then(() => {
                        location.reload(); // Reload setelah user klik "Oke"
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat mengubah status.'
                    }).then(() => {
                        location.reload(); // Reload setelah user klik "Oke"
                    });
                }
            });

        });
    });

</script>
<style>
    .custom-switch {
        width: 60px;
        height: 28px;
        background-color: #ccc;
        border-radius: 30px;
        position: relative;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .custom-switch.active {
        background-color: #28a745;
    }

    .custom-switch .switch-slider {
        width: 24px;
        height: 24px;
        background: white;
        border-radius: 50%;
        position: absolute;
        top: 2px;
        left: 2px;
        transition: all 0.3s;
    }

    .custom-switch.active .switch-slider {
        transform: translateX(32px);
    }

    .switch-label {
        width: 30px;
        text-align: center;
    }
</style>


@endpush
