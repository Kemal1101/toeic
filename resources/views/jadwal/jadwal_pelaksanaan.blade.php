@extends('layouts.template')

@section('page-title', 'Jadwal Peserta')
@section('card-title', 'Data Jadwal Peserta')

@section('content')
<div class="container">
    @if($jadwal)
    <table class="table table-sm table-bordered table-striped">
        <tr>
            <th class="text-right col-3 text-nowrap">Nama Lengkap :</th>
            <td class="col-9 text-nowrap">{{ $jadwal->user->nama_lengkap }}</td>
        </tr>
        <tr>
            <th class="text-right col-3 text-nowrap">NIM :</th>
            <td class="col-9 text-nowrap">{{ $jadwal->user->username }}</td>
        </tr>
        @php
            \Carbon\Carbon::setLocale('id');
        @endphp

        <tr>
            <th class="text-right col-3 text-nowrap">Tanggal Pelaksanaan Ujian :</th>
            <td class="col-9 text-nowrap">
                {{ \Carbon\Carbon::parse($jadwal->tanggal_pelaksanaan)->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <th class="text-right col-3 text-nowrap">Jam Pelaksanaan Ujian :</th>
            <td class="col-9 text-nowrap">
                {{ \Carbon\Carbon::parse($jadwal->tanggal_pelaksanaan)->format('h:i A') }}
            </td>
        </tr>
        <tr>
            <th class="text-right col-3 text-nowrap">Link Zoom :</th>
            <td class="col-9 text-nowrap">
                <a href="{{ $jadwal->link_zoom }}" target="_blank" rel="noopener noreferrer">
                    {{ $jadwal->link_zoom }}
                </a>
            </td>
        </tr>
    </table>
    @else
    <div class="alert alert-warning">
        Jadwal pelaksanaan belum tersedia.
    </div>
    @endif
</div>
@endsection
