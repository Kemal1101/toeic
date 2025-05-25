<div class="modal fade" id="myModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="form-import" method="POST" enctype="multipart/form-data" action="{{ route('jadwal.import_ajax') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Import Data User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
                    <label>Download Contoh File</label>
                    <a href="{{ asset('template/jadwal_pelaksanaan_template.xlsx') }}" class="btn btn-info btn-sm" download><i class="fa fa-file-excel"></i>Download</a>
                    <small id="error-kategori_id" class="error-text form-text text-danger"></small>
                </div>
            <div class="form-group">
              <label for="data_jadwal_peserta">Pilih File</label>
              <input type="file" name="data_jadwal_peserta" id="data_jadwal_peserta" class="form-control" required>
              <small id="error-data_jadwal_peserta" class="error-text text-danger"></small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Upload</button>
          </div>
        </form>
      </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $('#form-import').validate({
            rules: {
                data_jadwal_peserta: {
                    required: true,
                    extension: "xlsx"
                }
            },
            submitHandler: function (form) {
                const formData = new FormData(form);

                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('.error-text').text('');

                        if (response.status) {
                            document.activeElement.blur();
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                           dataJadwal.ajax.reload();
                        } else {
                            $.each(response.msgField, function (prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengirim data.'
                        });
                    }
                });

                return false;
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });

        $(document).on('click', '[data-dismiss="modal"]', function () {
            $('#myModal').modal('hide');
        });
    });
</script>
