@extends('layouts.template')

@section('page-title', 'Jadwal Peserta')
@section('card-title', 'Data Jadwal Peserta')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="card-tools">
                <button onclick="modalAction('{{ route('jadwal.import') }}')" class="btn btn-sm btn-info mt-1">
                    Import Jadwal Peserta
                </button>
                <button onclick="modalAction('{{ route('jadwal.create_ajax') }}')" class="btn btn-sm btn-success mt-1">
                    Tambah Jadwal Peserta
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

            <table id="table_jadwal" class="table table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>NIM</th>
                        <th>Tanggal Pelaksanaan Ujian</th>
                        <th>Jam Pelaksanaaan Ujian</th>
                        <th>Link Zoom</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
        data-backdrop="static" data-keyboard="false" data-width="75%">
    </div>
@endsection

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

    let dataJadwal;

    $(document).ready(function() {
        dataJadwal = $('#table_jadwal').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('jadwal.getJadwal') }}",
                type: "GET"
            },
            columns: [
                { data: 'nama_lengkap', name: 'user.nama_lengkap' },
                { data: 'username', name: 'user.username' },
                { data: 'tanggal_pelaksanaan_tanggal', name: 'tanggal_pelaksanaan_tanggal' },
                { data: 'tanggal_pelaksanaan_jam', name: 'tanggal_pelaksanaan_jam' },
                { data: 'link_zoom', name: 'link_zoom' },
                {
                    data: null,
                    name: 'aksi',
                    orderable: false,      // <--- PENTING: Matikan pengurutan untuk kolom ini
                    searchable: false,
                    render: function(data, type, row) {
                        let url_hapus = `{{ route('jadwal.confirm_ajax', ['id' => ':id']) }}`;
                        url_hapus = url_hapus.replace(':id', row.tanggal_pelaksanaan_id);

                        return `<button onclick="modalAction('${url_hapus}')" class="btn btn-sm btn-danger">Hapus</button>`;
                    }
                }
            ],
            responsive: true
        });

        $('#user_id').change(function() {
            dataJadwal.ajax.reload();
        });
    });
</script>


@endpush
