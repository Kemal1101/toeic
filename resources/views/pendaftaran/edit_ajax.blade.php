@extends('layouts.template')
@section('page-title', 'PENDAFTARAN TOEIC')
@section('card-title', 'EDIT DATA PENDAFTARAN')
@section('content')
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('pendaftaran.update_ajax', $dataPendaftar->data_pendaftaran_id) }}" method="POST" enctype="multipart/form-data" id="form-edit">
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
                    <label>NIK</label>
                    <input type="text" name="nik" class="form-control" value="{{ $dataPendaftar->nik }}" required>
                </div>
                <div class="form-group">
                    <label>No. WhatsApp</label>
                    <input type="text" name="no_wa" class="form-control" value="{{ $dataPendaftar->no_wa }}" required>
                </div>

                <div class="form-group">
                    <label>Alamat Asal</label>
                    <input type="text" name="alamat_asal" class="form-control" value="{{ $dataPendaftar->alamat_asal }}" required>
                </div>

                <div class="form-group">
                    <label>Alamat Sekarang</label>
                    <input type="text" name="alamat_sekarang" class="form-control" value="{{ $dataPendaftar->alamat_sekarang }}" required>
                </div>

                <div class="form-group">
                    <label>Jurusan</label>
                    <input type="text" name="jurusan" class="form-control" value="{{ $dataPendaftar->jurusan }}" required>
                </div>

                <div class="form-group">
                    <label>Program Studi</label>
                    <input type="text" name="program_studi" class="form-control" value="{{ $dataPendaftar->program_studi }}" required>
                </div>

                <div class="form-group">
                    <label>Kampus</label>
                    <select name="kampus" class="form-control" required>
                        <option value="Kampus Utama" {{ $dataPendaftar->kampus == 'Kampus Utama' ? 'selected' : '' }}>Kampus Utama</option>
                        <option value="PSDKU Kediri" {{ $dataPendaftar->kampus == 'PSDKU Kediri' ? 'selected' : '' }}>PSDKU Kediri</option>
                        <option value="PSDKU Lumajang" {{ $dataPendaftar->kampus == 'PSDKU Lumajang' ? 'selected' : '' }}>PSDKU Lumajang</option>
                        <option value="PSDKU Pamekasan" {{ $dataPendaftar->kampus == 'PSDKU Pamekasan' ? 'selected' : '' }}>PSDKU Pamekasan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pas Foto (3 : 4)</label>
                    @if($dataPendaftar->pas_foto)
                        <div>
                            <img src="{{ asset('uploads/pasfoto/' . $dataPendaftar->pas_foto) }}" alt="Pas Foto" width="150">
                        </div>
                    @endif
                    <input type="file" name="pas_foto" id="pas_foto" class="form-control">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                </div>

                <div class="form-group">
                    <label>Upload KTM atau KTP</label>
                    @if($dataPendaftar->ktm_atau_ktp)
                        <div>
                            <img src="{{ asset('uploads/ktmktp/' . $dataPendaftar->ktm_atau_ktp) }}" alt="Pas Foto" width="150">
                        </div>
                    @endif
                    <input type="file" name="ktm_atau_ktp" id="ktm_atau_ktp" class="form-control">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah dokumen.</small>
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
            $("#form-edit").on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
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
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseText
                        });
                    }
                });
            });
        });
    </script>
@endpush
