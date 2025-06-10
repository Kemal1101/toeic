@extends('layouts.template')
@section('page-title', 'PENDAFTARAN TOEIC')
@section('card-title', 'EDIT DATA PENDAFTARAN')
@section('content')
<div class="card shadow">
    <div class="card-body">
        <form action="{{ route('suratPernyataan.update_ajax', $dataPendaftar->surat_pernyataan_id) }}" method="POST" id="form-edit" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group mb-4">
                <label for="sertifikat1" class="col-form-label font-weight-bold">
                    Sertifikat 1
                    <span class="text-muted d-block small">(Maks. ukuran 5 MB | Format: JPG, PNG)</span>
                </label>
                <input type="file" name="sertifikat1" id="sertifikat1" class="form-control-file">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                <span class="text-danger error-text" id="error-sertifikat1"></span>
            </div>

            <div class="form-group mb-4">
                <label for="sertifikat2" class="col-form-label font-weight-bold">
                    Upload KTM atau KTP
                    <span class="text-muted d-block small">(Maks. ukuran 5 MB | Format: JPG, PNG)</span>
                </label>
                <input type="file" name="sertifikat2" id="sertifikat2" class="form-control-file">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah dokumen.</small>
                <span class="text-danger error-text" id="error-sertifikat2"></span>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('dashboard.mahasiswa') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        const maxSize = 5 * 1024 * 1024; // 5 MB

        function validateFileInput(inputId, errorId) {
            const fileInput = document.getElementById(inputId);
            const errorElement = document.getElementById(errorId);
            const file = fileInput.files[0];
            const allowedTypes = ['image/jpeg', 'image/png'];

            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    errorElement.textContent = 'Format file tidak valid. Hanya JPG dan PNG yang diperbolehkan.';
                    fileInput.value = '';
                    return false;
                }

                if (file.size > maxSize) {
                    errorElement.textContent = 'Ukuran file melebihi 5 MB. Silakan pilih file yang lebih kecil.';
                    fileInput.value = '';
                    return false;
                } else {
                    errorElement.textContent = '';
                    return true;
                }
            }
            return true; // No file selected, so no validation error for this input
        }

        $('#sertifikat1').on('change', () => validateFileInput('sertifikat1', 'error-sertifikat1'));
        $('#sertifikat2').on('change', () => validateFileInput('sertifikat2', 'error-sertifikat2'));

        $('#form-edit').validate({
            rules: {
                sertifikat1: {
                    extension: "jpg|jpeg|png"
                },
                sertifikat2: {
                    extension: "jpg|jpeg|png"
                }
            },
            messages: {
                sertifikat1: {
                    extension: "Format file tidak valid. Hanya JPG dan PNG yang diperbolehkan."
                },
                sertifikat2: {
                    extension: "Format file tidak valid. Hanya JPG dan PNG yang diperbolehkan."
                }
            },
            submitHandler: function (form) {
                // Perform file size and type validation before submitting
                const isSertifikat1Valid = validateFileInput('sertifikat1', 'error-sertifikat1');
                const isSertifikat2Valid = validateFileInput('sertifikat2', 'error-sertifikat2');

                if (!isSertifikat1Valid || !isSertifikat2Valid) {
                    return false; // Prevent form submission if files are invalid
                }

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
                                title: 'Berhasil Memperbarui Data',
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
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Terjadi kesalahan saat memproses permintaan.'
                        });
                        console.error("AJAX error: " + status + " - " + error);
                    }
                });

                return false; // prevent default form submission
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                // Place the error message right after the input field
                if (element.attr("type") === "file") {
                    error.insertAfter(element.next('small'));
                } else {
                    error.insertAfter(element);
                }
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
@endpush
