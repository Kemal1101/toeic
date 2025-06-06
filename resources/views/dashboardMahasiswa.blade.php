@extends('layouts.template')
@section('page-title', 'Dashboard')
@section('card-title', '')
@section('content')
<div class="card">
    @php
        use App\Models\Data_PendaftaranModel;

        $isTerdaftar = Data_PendaftaranModel::where('user_id', Auth::id())->exists();
        // Ambil status verifikasi_data jika user sudah mendaftar
        $status = null;
        if ($isTerdaftar) {
            $pendaftaran = Data_PendaftaranModel::where('user_id', Auth::id())->first();
            $status = $pendaftaran->verifikasi_data;
            if ($status === 'DITOLAK') {
                $notes = $pendaftaran->notes_ditolak;
            }
        }
    @endphp
        <div class="card-body">
            <h5>Selamat datang, {{ Auth::user()->nama_lengkap }}</h5>
            <div>
                <span>
                    NIM: {{ Auth::user()->username }}
                </span>
            </div>
            <div>
                <span>
                    Tanggal Lahir: {{ Auth::user()->tanggal_lahir}}
                </span>
            </div>
            <div>
                <span>
                    Status Mendaftar:
                </span>
                <span style="color: {{ $isTerdaftar ? 'green' : 'red' }}">
                    {{ $isTerdaftar ? 'Sudah mendaftar' : 'Belum mendaftar' }}
                </span>
            </div>
            {{-- Tampilkan status verifikasi hanya jika sudah mendaftar --}}
            @if ($isTerdaftar)
                <div>
                    <span>
                        Status Verifikasi:
                    </span>
                    <span style="color:
                        {{ $status === 'PENDING' ? 'orange' : ($status === 'TERVERIFIKASI' ? 'green' : 'red') }}">
                        @if ($status === 'PENDING')
                            Masih menunggu verifikasi
                        @else
                            {{ $status }}
                            @if ($status === 'DITOLAK')
                                ( {{ $notes }} )
                                <a href="{{ route('pendaftaran.edit_ajax', ['id' => $dataPendaftaran->data_pendaftaran_id]) }}" class="btn btn-sm btn-danger">
                                    Edit Data
                                </a>
                            @endif
                        @endif
                    </span>
                </div>
            @endif
        </div>
    </div>
@endsection
