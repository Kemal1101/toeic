<!-- Modal Tambah Jadwal Peserta -->
<form action="{{ route('jadwal.store_ajax') }}" method="POST" id="form-tambah-jadwal">
    @csrf

    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Tambah Jadwal Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Pilih Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">NIM Peserta</label>
                        <input type="text" name="username" id="username" class="form-control">
                        <small id="error-username" class="text-danger"></small>
                    </div>

                    <!-- Tanggal Pelaksanaan -->
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                        <small id="error-tanggal" class="text-danger"></small>
                    </div>

                    <!-- Jam Pelaksanaan -->
                    <div class="mb-3">
                        <label for="jam" class="form-label">Jam Pelaksanaan</label>
                        <input type="time" name="jam" id="jam" class="form-control" required>
                        <small id="error-jam" class="text-danger"></small>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        $("#form-tambah-jadwal").validate({
            rules: {
                username: {
                    required: true,
                    minlength: 3,
                    maxlength: 20
                },
                tanggal: {
                    required: true,
                    date: true
                },
                jam: {
                    required: true,
                    time: true // Catatan: jQuery Validate tidak punya `time` bawaan, tapi akan tetap divalidasi sebagai input `type="time"`
                }
            },

            submitHandler: function (form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function (response) {
                        if (response.status) {
                            const modalEl = document.getElementById('myModal');
                            const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modalInstance.hide();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                // Reload halaman setelah klik OK
                                location.reload();
                            });
                        } else {
                            $('.error-text').text('');
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
    });
</script>
