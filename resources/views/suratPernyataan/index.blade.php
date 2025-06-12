@extends('layouts.template')
@section('page-title', 'Surat Keterangan Mengikuti Ujian TOEIC')
@section('card-title', 'Upload Sertifikat TOEIC')
@section('content')
<div class="alert alert-info">
    <p>
        <strong>Perhatian:</strong> Surat Keterangan Mengikuti Ujian TOEIC diperlukan apabila:
    </p>
    <ul>
        <li>Total nilai TOEIC di bawah <strong>400</strong> untuk <strong>Program D-III</strong>.</li>
        <li>Total nilai TOEIC di bawah <strong>450</strong> untuk <strong>Program D-IV</strong>.</li>
    </ul>
    <p>
        Silakan ajukan surat keterangan apabila Anda memenuhi kriteria di atas.
    </p>

    @php
        use App\Models\SuratPernyataanModel;

        $sudahRequestSurat = SuratPernyataanModel::where('user_id', Auth::id())->first();
    @endphp

    <button
        class="btn mt-3 {{ $sudahRequestSurat ? 'btn-secondary' : 'btn-primary' }}"
        onclick="{{ $sudahRequestSurat ? '' : "modalAction('" . route('suratPernyataan.upload') . "')" }}"
        {{ $sudahRequestSurat ? 'disabled' : '' }}
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="{{ $sudahRequestSurat ? 'Anda sudah mengajukan permintaan surat.' : '' }}"
    >
        Request Surat Keterangan Mengikuti
    </button>

    @if ($sudahRequestSurat && strtoupper($sudahRequestSurat->verifikasi_data) === 'TERVERIFIKASI')
        <a href="{{ route('suratPernyataan.export_ajax') }}" class="btn btn-success mt-3">
            Generate Surat
        </a>
    @endif
</div>

@endsection

<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
        data-backdrop="static" data-keyboard="false" data-width="75%">
</div>

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
