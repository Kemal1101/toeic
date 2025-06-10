@extends('layouts.template')
@section('page-title', 'Dashboard')
@section('card-title', '')
@section('content')
<div class="card shadow rounded-4">
    @php
        use App\Models\Data_PendaftaranModel;
        use App\Models\SuratPernyataanModel;

        $isTerdaftar = Data_PendaftaranModel::where('user_id', Auth::id())->exists();
        $status = null;
        $notes = null;
        $dataPendaftaran = null;

        if ($isTerdaftar) {
            $dataPendaftaran = Data_PendaftaranModel::where('user_id', Auth::id())->first();
            $status = $dataPendaftaran->verifikasi_data;
            if ($status === 'DITOLAK') {
                $notes = $dataPendaftaran->notes_ditolak;
            }
        }

        $surat = SuratPernyataanModel::where('user_id', Auth::id())->first();
        $suratStatus = $surat?->verifikasi_data;
        $suratNotes = $surat?->notes_ditolak;
    @endphp

    <div class="card-body">
        <h5 class="mb-3">
            👋 Selamat datang, <strong>{{ Auth::user()->nama_lengkap }}</strong>
        </h5>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="d-flex flex-column">
                    <span class="fw-semibold text-muted">NIM</span>
                    <span>{{ Auth::user()->username }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex flex-column">
                    <span class="fw-semibold text-muted">Tempat, Tanggal Lahir</span>
                    <span>{{ Auth::user()->tempat_lahir }}, {{ Auth::user()->tanggal_lahir }}</span>
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <span class="fw-semibold text-muted">Status Mendaftar:</span>
            <span class="badge {{ $isTerdaftar ? 'bg-success' : 'bg-danger' }}">
                {{ $isTerdaftar ? 'Sudah mendaftar' : 'Belum mendaftar' }}
            </span>
        </div>

        @if ($isTerdaftar)
            <div class="mb-3">
                <span class="fw-semibold text-muted">Status Verifikasi:</span>
                @if ($status === 'PENDING')
                    <span class="badge bg-warning text-dark">Menunggu verifikasi</span>
                @elseif ($status === 'TERVERIFIKASI')
                    <span class="badge bg-success">Terverifikasi</span>
                @elseif ($status === 'DITOLAK')
                    <span class="badge bg-danger">Ditolak</span>
                    <div class="text-danger mt-2">Catatan: {{ $notes }}</div>
                    <a href="{{ route('pendaftaran.edit_ajax', ['id' => $dataPendaftaran->data_pendaftaran_id]) }}"
                       class="btn btn-outline-danger btn-sm mt-2">
                        Edit Data
                    </a>
                @endif
            </div>
        @endif

        @if ($suratStatus)
            <div class="mb-3">
                <span class="fw-semibold text-muted">Status Surat Keterangan:</span>
                @if ($suratStatus === 'PENDING')
                    <span class="badge bg-warning text-dark">Menunggu verifikasi</span>
                @elseif ($suratStatus === 'TERVERIFIKASI')
                    <span class="badge bg-success">Terverifikasi</span>
                @elseif ($suratStatus === 'DITOLAK')
                    <span class="badge bg-danger">Ditolak</span>
                    <div class="text-danger mt-2">Catatan: {{ $suratNotes }}</div>
                    <a href="{{ route('suratPernyataan.edit_ajax', ['id' => $surat->surat_pernyataan_id]) }}"
                       class="btn btn-outline-danger btn-sm mt-2">
                        Edit Data
                    </a>
                @endif
            </div>
        @endif
    </div>

    <div class="card-footer text-end bg-transparent border-top-0">
        <a href="modalAction('{{ route('user.edit_password_ajax', ['id' => Auth::id()]) }}')"
           class="btn btn-primary btn-sm">
            Ubah Password
        </a>
    </div>
</div>

@endsection

@push('js')
<script>
    function modalAction(url) {
        $.get(url, function(response) {
            $('#myModal').remove(); // bersihkan modal sebelumnya
            $('body').append(response); // tambahkan modal baru ke body
            const modalEl = document.getElementById('myModal');
            const modalInstance = new bootstrap.Modal(modalEl);
            modalInstance.show();
        }).fail(function() {
            alert('Gagal memuat modal.');
        });
    }
</script>
@endpush
