@empty($dataPendaftar)
<div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="modal-message">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang Anda cari tidak ditemukan.
                </div>
                <a href="{{ route('pendaftaran.data_pendaftar') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
</div>
@else
     <form action="{{ route('pendaftaran.delete_ajax', ['id' => $dataPendaftar->data_pendaftaran_id]) }}" method="POST" id="form-delete">
        @csrf
        @method('DELETE')
        <div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hapus Data Pendaftar</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                    Apakah Anda ingin menghapus data seperti di bawah ini?
                </div>
                <table class="table table-sm table-bordered table-striped">
                    <tr><th class="text-right col-3">Nama Lengkap :</th><td class="col-9">{{ $dataPendaftar->user->nama_lengkap }}</td></tr>
                    <tr><th class="text-right col-3">NIM:</th><td class="col-9">{{ $dataPendaftar->user->username }}</td></tr>
                    <tr><th class="text-right col-3">Jurusan:</th><td class="col-9">{{ $dataPendaftar->jurusan}}</td></tr>
                    <tr><th class="text-right col-3">Program Studi:</th><td class="col-9">{{ $dataPendaftar->program_studi }}</td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#form-delete").validate({
            rules: {},
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('myModal'));
                            modal.hide(); // Menggunakan metode Bootstrap 5

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });

                            dataPendaftar.ajax.reload(); // Pastikan nama DataTable benar
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });

        $(document).on('click', '[data-bs-dismiss="modal"]', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('globalModal'));
            modal.hide();
        });
    });
</script>

@endempty
