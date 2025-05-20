@extends('layouts.template')
@section('page-title', 'PENDAFTARAN TOEIC')
@section('card-title', 'ISI DATA PENDAFTARAN')
@section('content')
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('pendaftaran.store_ajax') }}" method="POST" enctype="multipart/form-data" id="form-tambah">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Nama Lengkap</label>
                    <div class="col-sm-9">
                        <p class="form-control-plaintext font-weight-bold">{{ $nama_lengkap }}</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">NIM</label>
                    <div class="col-sm-9">
                        <p class="form-control-plaintext font-weight-bold">{{ $username }}</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">NIK</label>
                    <input type="text" name="nik" id="nik" class="form-control" required>
                    <small id="error-nik" class="text-danger"></small>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">No. WhatsApp</label>
                    <input type="text" name="no_wa" id="no_wa" class="form-control" required>
                    <small id="error-no_wa" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Alamat Asal</label>
                    <input type="text" name="alamat_asal" id="alamat_asal" class="form-control" required>
                    <small id="error-alamat_asal" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Alamat Sekarang</label>
                    <input type="text" name="alamat_sekarang" id="alamat_sekarang" class="form-control" required>
                    <small id="error-alamat_sekarang" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Jurusan</label>
                    <input type="text" name="jurusan" id="jurusan" class="form-control" placeholder="Contoh: Teknologi Informasi" required>
                    <small id="error-jurusan" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Program Studi</label>
                    <input type="text" name="program_studi" id="program_studi" class="form-control" placeholder="Contoh: Sistem Informasi Bisnis" required>
                    <small id="error-program_studi" class="text-danger"></small>
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
                <label class="col-sm-3 col-form-label">Pas Foto (3 : 4)</label>
                    <div class="custom-file">
                        <input type="file" name="pas_foto" id="pas_foto" class="custom-file-input" accept="image/*" required>
                        <label class="custom-file-label" for="pas_foto">Pilih file foto</label>
                    </div>
                    <small id="error-pas_foto" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Upload KTM atau KTP</label>
                    <div class="custom-file">
                        <input type="file" name="ktm_atau_ktp" id="ktm_atau_ktp" class="custom-file-input" accept="image/*,application/pdf" required>
                        <label class="custom-file-label" for="ktm_atau_ktp">Pilih file dokumen</label>
                    </div>
                    <small id="error-ktm_atau_ktp" class="text-danger"></small>
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
@endpush
