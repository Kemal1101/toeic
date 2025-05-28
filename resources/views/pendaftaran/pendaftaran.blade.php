@extends('layouts.template')
@section('page-title', 'PENDAFTARAN TOEIC')
@section('card-title', 'ISI DATA PENDAFTARAN')
@section('content')
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('pendaftaran.store_ajax') }}" method="POST" enctype="multipart/form-data" id="form-tambah">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Nama Lengkap:</label>
                    <div class="col-sm-9">
                        <p class="form-control-plaintext font-weight-bold">{{ $nama_lengkap }}</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">NIM:</label>
                    <div class="col-sm-9">
                        <p class="form-control-plaintext font-weight-bold">{{ $username }}</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">NIK:</label>
                    <input type="text" name="nik" id="nik" class="form-control" required>
                    <small id="error-nik" class="text-danger"></small>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">No. WhatsApp:</label>
                    <input type="text" name="no_wa" id="no_wa" class="form-control" required>
                    <small id="error-no_wa" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Alamat Asal:</label>
                    <input type="text" name="alamat_asal" id="alamat_asal" class="form-control" required>
                    <small id="error-alamat_asal" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Alamat Sekarang:</label>
                    <input type="text" name="alamat_sekarang" id="alamat_sekarang" class="form-control" required>
                    <small id="error-alamat_sekarang" class="text-danger"></small>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Kampus:</label>
                    <select name="kampus" id="kampus" class="form-control" required>
                        <option value="">- Pilih Kampus -</option>
                        <option value="Kampus Utama">Kampus Utama</option>
                        <option value="PSDKU Kediri">PSDKU Kediri</option>
                        <option value="PSDKU Lumajang">PSDKU Lumajang</option>
                        <option value="PSDKU Pamekasan">PSDKU Pamekasan</option>
                    </select>
                    <small id="error-kampus" class="text-danger"></small>
                </div>
               <div id="jurusan-container" class="form-group" style="display: none;">
                    <label class="col-sm-3 col-form-label">Jurusan:</label>
                    <select name="jurusan" id="jurusan" class="form-control" required disabled>
                    <select name="jurusan" id="jurusan" class="form-control" required disabled>
                    </select>
                    <small id="error-jurusan" class="text-danger"></small>
                </div>

                <div id="prodi-container" class="form-group" style="display: none;">
                    <label class="col-sm-3 col-form-label">Program Studi:</label>
                    <select name="program_studi" id="program_studi" class="form-control" required disabled>
                    <select name="program_studi" id="program_studi" class="form-control" required disabled>
                    </select>
                    <small id="error-program_studi" class="text-danger"></small>
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold">
                        Pas Foto (3x4) <span class="text-muted">(Maks. ukuran 5 MB | Format: JPG, PNG)</span>
                    </label>
                    <div class="custom-file">
                        <input type="file" name="pas_foto" id="pas_foto" class="custom-file-input" accept="image/jpeg,image/png" required>
                        <label class="custom-file-label" for="pas_foto">Pilih file foto </label>
                    </div>
                    <small id="error-pas_foto" class="form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-form-label font-weight-bold">
                        Upload KTM atau KTP <span class="text-muted">(Maks. ukuran 5 MB | Format: JPG, PNG)</span>
                    </label>
                    <div class="custom-file">
                        <input type="file" name="ktm_atau_ktp" id="ktm_atau_ktp" class="custom-file-input" accept="image/jpeg,image/png" required>
                        <label class="custom-file-label" for="ktm_atau_ktp">Pilih file dokumen</label>
                    </div>
                    <small id="error-ktm_atau_ktp" class="form-text text-danger"></small>
                </div>

                <div class="form-group text-right mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan
                    </button>
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

            const maxSize = 5 * 1024 * 1024; // 5MB

            const jurusanData = {
                "Kampus Utama": [
                    "Teknik Sipil", "Teknik Kimia", "Teknik Elektro",
                    "Teknik Mesin", "Teknologi Informasi", "Akutansi",
                    "Administrasi Niaga"
                ],
                "PSDKU Kediri": [
                    "Teknologi Informasi", "Akutansi", "Administrasi Niaga"
                ],
                "PSDKU Lumajang": [
                    "Teknik Sipil", "Teknik Mesin"
                ],
                "PSDKU Pamekasan": [
                    "Teknik Elektro", "Administrasi Niaga"
                ]
            };

            const programStudiData = {
                "Teknik Sipil": ["Manajemen Konstruksi", "Struktur Bangunan", "Geoteknik"],
                "Teknik Kimia": ["Teknik Kimia Industri", "Rekayasa Proses"],
                "Teknik Elektro": ["Elektronika", "Telekomunikasi", "Tenaga Listrik"],
                "Teknik Mesin": ["Desain Mesin", "Konversi Energi"],
                "Teknologi Informasi": ["Sistem Informasi", "Teknologi Komputer", "Rekayasa Perangkat Lunak"],
                "Akutansi": ["Akuntansi Keuangan", "Akuntansi Manajemen"],
                "Administrasi Niaga": ["Administrasi Bisnis", "Manajemen Pemasaran"]
            };

            $('#kampus').change(function () {
                const selectedKampus = $(this).val();
                const $jurusan = $('#jurusan');
                const $jurusanContainer = $('#jurusan-container');
                const $prodiContainer = $('#prodi-container');

                $jurusan.empty().append('<option value="">- Pilih Jurusan -</option>');
                $('#program_studi').empty().append('<option value="">- Pilih Program Studi -</option>');

                $prodiContainer.hide();
                $('#program_studi').prop('disabled', true);

                if (selectedKampus && jurusanData[selectedKampus]) {
                    $jurusanContainer.show();
                    $jurusan.prop('disabled', false);
                    jurusanData[selectedKampus].forEach(jurusan => {
                        $jurusan.append(`<option value="${jurusan}">${jurusan}</option>`);
                    });
                } else {
                    $jurusanContainer.hide();
                    $jurusan.prop('disabled', true);
                }
            });

            $('#jurusan').change(function () {
                const selectedJurusan = $(this).val();
                const $programStudi = $('#program_studi');
                const $prodiContainer = $('#prodi-container');

                $programStudi.empty().append('<option value="">- Pilih Program Studi -</option>');

                if (selectedJurusan && programStudiData[selectedJurusan]) {
                    $prodiContainer.show();
                    $programStudi.prop('disabled', false);
                    programStudiData[selectedJurusan].forEach(prodi => {
                        $programStudi.append(`<option value="${prodi}">${prodi}</option>`);
                    });
                } else {
                    $prodiContainer.hide();
                    $programStudi.prop('disabled', true);
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

            $('#pas_foto').on('change', () => validateFileInput('pas_foto', 'error-pas_foto'));
            $('#ktm_atau_ktp').on('change', () => validateFileInput('ktm_atau_ktp', 'error-ktm_atau_ktp'));

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
                                        window.location.href = "{{ route('user') }}";
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


@endpush
