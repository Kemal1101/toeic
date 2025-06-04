@extends('layouts.template')

@section('page-title', 'Status Pengambilan Sertifikat')
@section('card-title', '')

@section('content')
<div class="container">
    @if($sertif)
    <table class="table table-sm table-bordered table-striped">
        <tr>
            <th class="text-right col-3 text-nowrap">Nama Lengkap :</th>
            <td class="col-9 text-nowrap">{{ $sertif->nama_lengkap }}</td>
        </tr>
        <tr>
            <th class="text-right col-3 text-nowrap">NIM :</th>
            <td class="col-9 text-nowrap">{{ $sertif->username }}</td>
        </tr>
    </table>

    <div class="text-center mt-3">
        <h5 class="text-success">SERTIFIKAT SUDAH BISA DIAMBIL</h5>
    </div>
    @else
    <div class="alert alert-warning">
        Sertifikat anda belum bisa diambil
    </div>
    @endif
</div>
@endsection
