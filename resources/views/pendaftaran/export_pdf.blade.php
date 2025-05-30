<html>
    <title>Data Pendaftar TOEIC</title>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body{
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }
        table {
            width:100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 4px 3px;
        }
        th{
            text-align: left;
        }
        .d-block{
            display: block;
        }
        img.image{
            width: auto;
            height: 20px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .p-1{
            padding: 5px 1px 5px 1px;
        }
        .font-10{
            font-size: 10pt;
        }
        .font-11{
            font-size: 11pt;
        }
        .font-12{
            font-size: 12pt;
        }
        .font-13{
            font-size: 13pt;
        }
        .border-bottom-header{
            border-bottom: 1px solid;
        }
        .border-all, .border-all th, .border-all td{
            border: 1px solid;
        }
        @media print {
            table, tr, td, th {
                page-break-inside: avoid;
            }
            tr {
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
        .scrollable-table {
            max-height: 600px;
            overflow-y: auto;
            display: block;
        }


    </style>
</head>
<body>
    <table class="border-bottom-header">
        <tr>
            <td width="10%" class="text-center">
                <img src="{{ asset('polinema-pw.jpg') }}" style="height: 80px; max-height: 80px; width: auto; padding-bottom: 5px;"></td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold mb-1">POLITEKNIK NEGERI MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341) 404420</span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
        </tr>
    </table>

    <h3 class="text-center">
        LAPORAN DATA PENDAFTAR TOEIC {{ optional($data[0]->created_at)->format('Y') }} {{ $data[0]->verifikasi_data }}
    </h3>
    @foreach($data as $b)
    <div style="margin-bottom: 30px;">
        <table class="border-all" style="margin-bottom: 20px;">
            <tr>
                <th class="text-left" width="30%">No</th>
                <td>{{ $loop->iteration }}</td>
            </tr>
            <tr>
                <th>Nama Lengkap</th>
                <td>{{ $b->user->nama_lengkap }}</td>
            </tr>
            <tr>
                <th>NIM</th>
                <td>{{ $b->user->username }}</td>
            </tr>
            <tr>
                <th>NIK</th>
                <td>{{ $b->nik }}</td>
            </tr>
            <tr>
                <th>Nomor Whatsapp</th>
                <td>{{ $b->no_wa }}</td>
            </tr>
            <tr>
                <th>Alamat Asal</th>
                <td>{{ $b->alamat_asal }}</td>
            </tr>
            <tr>
                <th>Alamat Sekarang</th>
                <td>{{ $b->alamat_sekarang }}</td>
            </tr>
            <tr>
                <th>Kampus</th>
                <td>{{ $b->kampus }}</td>
            </tr>
            <tr>
                <th>Jurusan</th>
                <td>{{ $b->jurusan }}</td>
            </tr>
            <tr>
                <th>Program Studi</th>
                <td>{{ $b->program_studi }}</td>
            </tr>
            <tr>
                <th>Pas Foto</th>
                <td><img src="{{ asset('uploads/pasfoto/' . $b->pas_foto) }}" alt="Pas Foto" width="100"></td>
            </tr>
            <tr>
                <th>KTM/KTP</th>
                <td><img src="{{ asset('uploads/ktmktp/' . $b->ktm_atau_ktp) }}" alt="KTM/KTP" width="100"></td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>
