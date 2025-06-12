<form action="{{ route('suratPernyataan.store') }}" method="POST" id="form-tambah">
    @csrf
    <!-- Modal Start -->
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Sertifikat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Role -->
                    <div class="form-group">
                    <label class="col-form-label font-weight-bold">
                        Upload Sertifikat 1 <span class="text-muted">(Maks. ukuran 5 MB | Format: JPG, PNG)</span>
                    </label>
                    <div class="custom-file">
                        <input type="file" name="sertifikat1" id="sertifikat1" class="custom-file-input" accept="image/jpeg,image/png" required>
                        <label class="custom-file-label" for="sertifikat1">Pilih file foto </label>
                    </div>
                    <small id="error-sertifikat1" class="form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-form-label font-weight-bold">
                        Upload Sertifikat 2 <span class="text-muted">(Maks. ukuran 5 MB | Format: JPG, PNG)</span>
                    </label>
                    <div class="custom-file">
                        <input type="file" name="sertifikat2" id="sertifikat2" class="custom-file-input" accept="image/jpeg,image/png" required>
                        <label class="custom-file-label" for="sertifikat2">Pilih file dokumen</label>
                    </div>
                    <small id="error-sertifikat2" class="form-text text-danger"></small>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal End -->
</form>
    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        function validateFileInput(inputId, errorId) {
            const fileInput = document.getElementById(inputId);
            const errorElement = document.getElementById(errorId);
            const file = fileInput.files[0];
            const allowedTypes = ['image/jpeg', 'image/png'];

            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    errorElement.textContent = 'Format file tidak valid. Hanya JPG dan PNG yang diperbolehkan.';
                    fileInput.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    errorElement.textContent = 'Ukuran file melebihi 5 MB. Silakan pilih file yang lebih kecil.';
                    fileInput.value = '';
                } else {
                    errorElement.textContent = '';
                }
            }
        }

        $('#sertifikat1').on('change', () => validateFileInput('sertifikat1', 'error-sertifikat1'));
        $('#sertifikat2').on('change', () => validateFileInput('sertifikat2', 'error-sertifikat2'));

        $('#form-tambah').validate({
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Mendaftar',
                                text: response.message
                            }).then(result => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('dashboard.mahasiswa') }}";
                                }
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
                    }
                });

                return false; // prevent default form submission
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
    });
</script>
