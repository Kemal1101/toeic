@extends('layouts.template')
@section('page-title', 'Dashboard')
@section('card-title', 'Statistik Pendaftaran')
@section('content')

<div class="row">
    <!-- Jumlah Pendaftar -->
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                Jumlah Pendaftar
                <div class="h4 mt-2">{{ $jumlahPendaftar }}</div>
            </div>
        </div>
    </div>

    <!-- Belum Mendaftar -->
    <div class="col-md-4 mb-4">
        <div class="card bg-secondary text-white shadow">
            <div class="card-body">
                Belum Mendaftar
                <div class="h4 mt-2">{{ $jumlahBelumMendaftar }}</div>
            </div>
        </div>
    </div>

    <!-- Ditolak -->
    <div class="col-md-4 mb-4">
        <div class="card bg-danger text-white shadow">
            <div class="card-body">
                Ditolak
                <div class="h4 mt-2">{{ $jumlahDitolak }}</div>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="col-md-6 mb-4">
        <div class="card bg-warning text-dark shadow">
            <div class="card-body">
                Pending
                <div class="h4 mt-2">{{ $jumlahPending }}</div>
            </div>
        </div>
    </div>

    <!-- Terverifikasi -->
    <div class="col-md-6 mb-4">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                Terverifikasi
                <div class="h4 mt-2">{{ $jumlahTerverifikasi }}</div>
            </div>
        </div>
    </div>
</div>

@endsection
