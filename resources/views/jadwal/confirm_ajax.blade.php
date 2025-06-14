@empty($jadwal)
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
                <a href="{{ route('jadwal') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
</div>
@else
     <form action="{{ route('jadwal.delete_ajax', ['id' => $jadwal->tanggal_pelaksanaan_id]) }}" method="POST" id="form-delete">
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
                        <td class="col-9 text-nowrap">{{ $jadwal->user->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3 text-nowrap">NIM </th>
                        <td class="col-9 text-nowrap">{{ $jadwal->user->username }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3 text-nowrap">Tanggal Pelaksanaan Ujian </th>
                        <td class="col-9 text-nowrap">
                            {{ \Carbon\Carbon::parse($jadwal->tanggal_pelaksanaan)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right col-3 text-nowrap">Jam Pelaksanaan Ujian </th>
                        <td class="col-9 text-nowrap">
                            {{ \Carbon\Carbon::parse($jadwal->tanggal_pelaksanaan)->format('h:i A') }}
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
                                success: function (response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                }).then(() => {
                                    location.reload(); // Reload setelah user klik "Oke"
                                });
                            };
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
