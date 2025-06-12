@empty($nilai)
<div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
           <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan</div>
                <a href="{{ route('nilai') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
</div>
@else
     <form action="{{ route('nilai.delete_ajax', ['id' => $nilai->nilai_id]) }}" method="POST" id="form-delete">
        @csrf
        @method('DELETE')
        <div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hapus Data Jadwal</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                    Apakah Anda ingin menghapus data seperti di bawah ini?
                </div>
               <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3 text-nowrap">Nama Lengkap </th>
                        <td class="col-9 text-nowrap">{{ $nilai->user->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3 text-nowrap">NIM </th>
                        <td class="col-9 text-nowrap">{{ $nilai->user->username }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3 text-nowrap">Nilai Listening </th>
                        <td class="col-9 text-nowrap">
                            {{ $nilai->listening }}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right col-3 text-nowrap">Nilai Reading </th>
                        <td class="col-9 text-nowrap">
                            {{ $nilai->reading }}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right col-3 text-nowrap">Nilai Total </th>
                        <td class="col-9 text-nowrap">
                            {{ $nilai->total }}
                        </td>
                    </tr>
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
                            if(response.status){
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                }).then(() => {
                                // Reload halaman setelah klik OK
                                location.reload();
                            });
                            }else{
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-'+prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                }).then(() => {
                                // Reload halaman setelah klik OK
                                location.reload();
                            });
                            }
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
        $(document).on('click', '[data-dismiss="modal"]', function() {
            $('#myModal').modal('hide');
        });

    </script>
@endempty
