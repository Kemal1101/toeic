@extends('layouts.template')
@section('page-title', 'PENDAFTARAN TOEIC')
@section('card-title', 'EDIT DATA PENDAFTARAN')
@section('content')
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('pendaftaran.update_ajax', $dataPendaftar->data_pendaftaran_id) }}" method="POST" enctype="multipart/form-data" id="form-tambah">
                @csrf
                 @method('PUT')
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Nama Lengkap</label>
                    <div class="col-sm-9">
                        <p class="form-control-plaintext font-weight-bold">{{ $user->nama_lengkap }}</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">NIM</label>
                    <div class="col-sm-9">
                        <p class="form-control-plaintext font-weight-bold">{{ $user->username }}</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">NIK</label>
                    <input type="text" name="nik" class="form-control" value="{{ $dataPendaftar->nik }}" required>
                    <small id="error-nik" class="text-danger"></small>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">No. WhatsApp</label>
                    <input type="text" name="no_wa" class="form-control" value="{{ $dataPendaftar->no_wa }}" required>
                    <small id="error-no_wa" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Alamat Asal</label>
                    <input type="text" name="alamat_asal" class="form-control" value="{{ $dataPendaftar->alamat_asal }}" required>
                    <small id="error-alamat_asal" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Alamat Sekarang</label>
                    <input type="text" name="alamat_sekarang" class="form-control" value="{{ $dataPendaftar->alamat_sekarang }}" required>
                    <small id="error-alamat_sekarang" class="text-danger"></small>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Kampus</label>
                    <select name="kampus" id="kampus" class="form-control" required>
                        <option value="">- Pilih Kampus -</option>
                        <option value="Kampus Utama">Kampus Utama</option>
                        <option value="PSDKU Kediri">PSDKU Kediri</option>
                        <option value="PSDKU Lumajang">PSDKU Lumajang</option>
                        <option value="PSDKU Pamekasan">PSDKU Pamekasan</option>
                    </select>
                    <small id="error-kampus" class="text-danger"></small>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Jurusan</label>
                    <select name="jurusan" id="jurusan" class="form-control" required>
                        <option value="">- Pilih Jurusan -</option>
                        <option value="Teknik Sipil">Teknik Sipil</option>
                        <option value="Teknik Kimia">Teknik Kimia</option>
                        <option value="Teknik Elektro">Teknik Elektro</option>
                        <option value="Teknik Mesin">Teknik Mesin</option>
                        <option value="Teknologi Informasi">Teknologi Informasi</option>
                        <option value="Akutansi">Akutansi</option>
                        <option value="Administrasi Niaga">Administrasi Niaga</option>
                    </select>
                    <small id="error-jurusan" class="text-danger"></small>
                </div>

                <div id="prodi-container" class="form-group" style="display: none;">
                    <label class="col-sm-3 col-form-label">Program Studi</label>
                    <select name="program_studi" id="program_studi" class="form-control" required>
                        <option value="">- Pilih Program Studi -</option>
                    </select>
                    <small id="error-program_studi" class="text-danger"></small>
                </div>

                <div class="form-group mb-4">
                    <label for="pas_foto" class="col-form-label font-weight-bold">
                        Pas Foto (3x4)
                        <span class="text-muted d-block small">(Maks. ukuran 5 MB | Format: JPG, PNG)</span>
                    </label>
                    <input type="file" name="pas_foto" id="pas_foto" class="form-control-file">
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                </div>

                <div class="form-group mb-4">
                    <label for="ktm_atau_ktp" class="col-form-label font-weight-bold">
                        Upload KTM atau KTP
                        <span class="text-muted d-block small">(Maks. ukuran 5 MB | Format: JPG, PNG)</span>
                    </label>
                    <input type="file" name="ktm_atau_ktp" id="ktm_atau_ktp" class="form-control-file">
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah dokumen.</small>
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
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            const maxSize = 5 * 1024 * 1024; // 5MB

            $('#pas_foto').on('change', function () {
                const file = this.files[0];
                const allowedTypes = ['image/jpeg', 'image/png'];

                if (file) {
                    if (!allowedTypes.includes(file.type)) {
                        $('#error-pas_foto').text('Format file tidak valid. Hanya JPG dan PNG yang diperbolehkan.');
                        $(this).val('');
                        return;
                    }

                    if (file.size > maxSize) {
                        $('#error-pas_foto').text('Ukuran file pas foto melebihi 5 MB. Silakan pilih file yang lebih kecil.');
                        $(this).val('');
                    } else {
                        $('#error-pas_foto').text('');
                    }
                }
            });

            $('#ktm_atau_ktp').on('change', function () {
                const file = this.files[0];
                const allowedTypes = ['image/jpeg', 'image/png'];

                if (file) {
                    if (!allowedTypes.includes(file.type)) {
                        $('#error-ktm_atau_ktp').text('Format file tidak valid. Hanya JPG, PNG yang diperbolehkan.');
                        $(this).val('');
                        return;
                    }

                    if (file.size > maxSize) {
                        $('#error-ktm_atau_ktp').text('Ukuran file KTM/KTP melebihi 5 MB. Silakan pilih file yang lebih kecil.');
                        $(this).val('');
                    } else {
                        $('#error-ktm_atau_ktp').text('');
                    }
                }
            });

            $("#form-tambah").validate({
                submitHandler: function(form) {
                    let formData = new FormData(form);
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if(response.status){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Mendaftar',
                                    text: response.message
                                }).then((result) => {
                                    // Setelah klik OK, redirect ke route user
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('user') }}";
                                    }
                                });
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-'+prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        },
                });
                    return false; // Cegah form submit biasa
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
    </script>
    <script>
    $(document).ready(function () {
        const programStudiData = {
            "Teknik Sipil": ["Manajemen Konstruksi", "Struktur Bangunan", "Geoteknik"],
            "Teknik Kimia": ["Teknik Kimia Industri", "Rekayasa Proses"],
            "Teknik Elektro": ["Elektronika", "Telekomunikasi", "Tenaga Listrik"],
            "Teknik Mesin": ["Desain Mesin", "Konversi Energi"],
            "Teknologi Informasi": ["Sistem Informasi", "Teknologi Komputer", "Rekayasa Perangkat Lunak"],
            "Akutansi": ["Akuntansi Keuangan", "Akuntansi Manajemen"],
            "Administrasi Niaga": ["Administrasi Bisnis", "Manajemen Pemasaran"]
        };

        $('#jurusan').change(function () {
            const selectedJurusan = $(this).val();
            const $programStudi = $('#program_studi');
            const $prodiContainer = $('#prodi-container');

            $programStudi.empty().append('<option value="">- Pilih Program Studi -</option>');

            if (selectedJurusan && programStudiData[selectedJurusan]) {
                // Tampilkan container Program Studi
                $prodiContainer.show();

                // Isi dropdown Program Studi sesuai jurusan
                programStudiData[selectedJurusan].forEach(function (prodi) {
                    $programStudi.append(`<option value="${prodi}">${prodi}</option>`);
                });
            } else {
                // Sembunyikan jika jurusan kosong
                $prodiContainer.hide();
            }
        });
    });
</script>

@endpush
