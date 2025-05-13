@extends('layouts.template')

@section('page-title', 'User')
@section('card-title', 'Data User')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="card-tools">
                <button onclick="modalAction('{{ route('user.import') }}')" class="btn btn-sm btn-info mt-1">
                    Import User
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

            <table id="table_user" class="table table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Tanggal Lahir</th>
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

    let dataUser;

    $(document).ready(function() {
        dataUser = $('#table_user').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('user.getUsers') }}",
                type: "GET"
            },
            columns: [
                { data: 'nama_lengkap', name: 'nama_lengkap' },
                { data: 'username', name: 'username' },
                { data: 'role', name: 'role' },
                { data: 'tanggal_lahir', name: 'tanggal_lahir' }
            ],
            responsive: true
        });

        $('#role_id').change(function() {
            dataUser.ajax.reload();
        });
    });
</script>


@endpush
