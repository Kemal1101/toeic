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

        const dataStruktur = {
            "Kampus Utama": {
                "Teknik Elektro": [
                    "D-IV Teknik Elektronika", "D-IV Sistem Kelistrikan", "D-IV Jaringan Telekomunikasi Digital",
                    "D-III Teknik Elektronika", "D-III Teknik Listrik", "D-III Teknik Telekomunikasi"
                ],
                "Teknik Mesin": [
                    "D-IV Teknik Otomotif Elektronik", "D-IV Teknik Mesin Produksi dan Perawatan",
                    "D-III Teknik Mesin", "D-III Teknologi Pemeliharaan Pesawat Udara"
                ],
                "Teknik Sipil": [
                    "D-IV Manajemen Rekayasa Konstruksi", "D-IV Teknologi Rekayasa Konstruksi Jalan dan Jembatan",
                    "D-III Teknik Sipil", "D-III Teknologi Konstruksi Jalan, Jembatan, dan Bangunan Air",
                    "D-III Teknologi Pertambangan"
                ],
                "Akuntansi": [
                    "D-IV Akuntansi Manajemen", "D-IV Keuangan", "D-III Akuntansi"
                ],
                "Administrasi Niaga": [
                    "D-IV Manajemen Pemasaran", "D-IV Bahasa Inggris untuk Komunikasi Bisnis dan Profesional",
                    "D-IV Pengelolaan Arsip dan Rekaman Informasi", "D-IV Usaha Perjalanan Wisata",
                    "D-IV Bahasa Inggris Untuk Industri Pariwisata", "D-III Administrasi Bisnis"
                ],
                "Teknik Kimia": [
                    "D-IV Teknologi Kimia Industri", "D-III Teknik Kimia"
                ],
                "Teknologi Informasi": [
                    "D-IV Teknik Informatika", "D-IV Sistem Informasi Bisnis", "D-II Pengembangan Piranti Lunak Situs"
                ]
            },
            "PSDKU Kediri": {
                "Teknik Elektro": ["D-IV Teknik Elektronika"],
                "Teknik Mesin": ["D-IV Teknik Mesin Produksi dan Perawatan", "D-III Teknik Mesin"],
                "Akuntansi": ["D-IV Keuangan", "D-III Akuntansi"],
                "Teknologi Informasi": ["D-III Manajemen Informatika"]
            },
            "PSDKU Lumajang": {
                "Teknik Mesin": ["D-IV Teknologi Rekayasa Otomotif"],
                "Teknologi Informasi": ["D-III Teknologi Informasi"],
                "Teknik Sipil": ["D-III Teknik Sipil"],
                "Akuntansi": ["D-III Akuntansi"]
            },
            "PSDKU Pamekasan": {
                "Teknik Elektro": ["D-IV Teknik Otomotif Elektronik"],
                "Akuntansi": ["D-IV Akuntansi Manajemen"],
                "Teknologi Informasi": ["D-III Manajemen Informatika"]
            }
        };

        $('#kampus').change(function () {
            const selectedKampus = $(this).val();
            const $jurusan = $('#jurusan');
            const $programStudi = $('#program_studi');

            $jurusan.empty().append('<option value="">- Pilih Jurusan -</option>');
            $programStudi.empty().append('<option value="">- Pilih Program Studi -</option>');

            $('#prodi-container').hide();
            $programStudi.prop('disabled', true);

            if (selectedKampus && dataStruktur[selectedKampus]) {
                $('#jurusan-container').show();
                $jurusan.prop('disabled', false);
                Object.keys(dataStruktur[selectedKampus]).forEach(jurusan => {
                    $jurusan.append(`<option value="${jurusan}">${jurusan}</option>`);
                });
            } else {
                $('#jurusan-container').hide();
                $jurusan.prop('disabled', true);
            }
        });

        $('#jurusan').change(function () {
            const selectedKampus = $('#kampus').val();
            const selectedJurusan = $(this).val();
            const $programStudi = $('#program_studi');

            $programStudi.empty().append('<option value="">- Pilih Program Studi -</option>');

            if (selectedKampus && selectedJurusan && dataStruktur[selectedKampus]?.[selectedJurusan]) {
                $('#prodi-container').show();
                $programStudi.prop('disabled', false);
                dataStruktur[selectedKampus][selectedJurusan].forEach(prodi => {
                    $programStudi.append(`<option value="${prodi}">${prodi}</option>`);
                });
            } else {
                $('#prodi-container').hide();
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



@endpush
