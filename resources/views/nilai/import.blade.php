<div class="modal fade" id="myModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="form-import" method="POST" enctype="multipart/form-data" action="{{ route('nilai.import_ajax') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Import Data Nilai</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <!-- Info Alert -->
          <div class="alert alert-info" role="alert">
            <i class="fa fa-info-circle"></i> Pastikan tata letak file Excel sesuai dengan <strong>template</strong> yang disediakan agar data bisa diimpor dengan benar. <br>
            Jika Anda memiliki file dalam format PDF, silakan konversi terlebih dahulu ke Excel melalui website berikut: <br>
            👉 <a href="https://www.ilovepdf.com/pdf_to_excel" target="_blank">https://www.ilovepdf.com/pdf_to_excel</a>
          </div>

          <div class="form-group">
            <label>Download Contoh File: </label>
            <a href="{{ asset('template/data_nilai_template.xlsx') }}" class="btn btn-info btn-sm" download>
              <i class="fa fa-file-excel"></i> Download
            </a>
            <small id="error-kategori_id" class="error-text form-text text-danger"></small>
          </div>

          <div class="form-group mt-3">
            <label for="data_nilai">Pilih File</label>
            <input type="file" name="data_nilai" id="data_nilai" class="form-control" required>
            <small id="error-data_nilai" class="error-text text-danger"></small>
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
                data_nilai: {
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
                            }).then(() => {
                                // Reload halaman setelah klik OK
                                location.reload();
                            });
                        } else {
                            $.each(response.msgField, function (prefix, val) {
                                $('#error-' + prefix).text(val[0]);
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
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengirim data.'
                        }).then(() => {
                                // Reload halaman setelah klik OK
                                location.reload();
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
