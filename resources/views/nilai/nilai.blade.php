@extends('layouts.template')

@section('page-title', 'Skor Toeic Peserta')
@section('card-title', 'Data Skor Toeic Peserta')

@section('content')
<div class="container">
    @if($nilai)
    <table class="table table-sm table-bordered table-striped">
        <tr>
            <th class="text-right col-3 text-nowrap">Nama Lengkap :</th>
            <td class="col-9 text-nowrap">{{ $nilai->user->nama_lengkap }}</td>
        </tr>
        <tr>
            <th class="text-right col-3 text-nowrap">NIM :</th>
            <td class="col-9 text-nowrap">{{ $nilai->user->username }}</td>
        </tr>
        <tr>
            <th class="text-right col-3 text-nowrap">Skor Listening :</th>
            <td class="col-9 text-nowrap">{{ $nilai->listening }}</td>
        </tr>
        <tr>
            <th class="text-right col-3 text-nowrap">Skor Reading :</th>
            <td class="col-9 text-nowrap">{{ $nilai->reading }}</td>
        </tr>
        <tr>
            <th class="text-right col-3 text-nowrap">Skor Total :</th>
            <td class="col-9 text-nowrap">{{ $nilai->total }}</td>
        </tr>
    </table>
    @else
    <div class="alert alert-warning">
        Jadwal pelaksanaan belum tersedia.
    </div>
    @endif
</div>
@endsection
