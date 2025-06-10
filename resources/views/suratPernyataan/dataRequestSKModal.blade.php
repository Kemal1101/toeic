<div id="modal_data_pendaftar" class="card card-outline card-primary">
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered">
           <tr>
                <th>Nama Lengkap</th>
                <td>{{ $user->nama_lengkap }}</td>
            </tr>
            <tr>
                <th>NIM</th>
                <td>{{ $user->username }}</td>
            </tr>
            <tr>
                <th>Sertifikat 1</th>
                <td>
                    <a href="{{ asset('uploads/sertifikat_surat_keterangan/' . $dataPendaftar->sertifikat1) }}" target="_blank">
                        <img src="{{ asset('uploads/sertifikat_surat_keterangan/' . $dataPendaftar->sertifikat1) }}" alt="Pas Foto" width="100">
                    </a>
                </td>
            </tr>
            <tr>
                <th>Sertifikat 2</th>
                <td>
                    <a href="{{ asset('uploads/sertifikat_surat_keterangan/' . $dataPendaftar->sertifikat2) }}" target="_blank">
                        <img src="{{ asset('uploads/sertifikat_surat_keterangan/' . $dataPendaftar->sertifikat2) }}" alt="KTM atau KTP" width="100">
                    </a>
                </td>
            </tr>
        </table>
        <div class="d-flex">
        <div class="mt-3 mx-1 text-end">
            <form action="{{ route('suratPernyataan.verifikasi.setuju', $dataPendaftar->surat_pernyataan_id) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i> VERIFIKASI
                </button>
            </form>
        </div>
        <div class="mt-3 mx-1 text-end">
            <button type="button" class="btn btn-danger" onclick="notes(this)" id="btn-tolak" data-id="{{ $dataPendaftar->surat_pernyataan_id }}">
                <i class="fas fa-times"></i> TOLAK
            </button>
        </div>
        </div>
    </div>
</div>
<!-- Modal -->
<!-- Tempat menampung modal dari AJAX -->

<script>
    // $(document).ready(function() {
    //     $('#btn-tolak').on('click', function() {
    //         console.log('Button clicked');
    //         const id = $(this).data('id');

    //         // Ambil URL template dari Laravel dan ganti :id dengan ID sebenarnya
    //         let url = `{{ route('pendaftaran.notes', ['id' => ':id']) }}`;
    //         url = url.replace(':id', id); // Ganti :id dengan nilai sebenarnya

    //         $.ajax({
    //             url: url,
    //             type: 'GET',
    //             success: function(response) {
    //                 $('#modal-container').html(response);
    //                 const modal = new bootstrap.Modal(document.getElementById('tolakModal'));
    //                 modal.show();
    //             },
    //             error: function(xhr) {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Gagal',
    //                     text: 'Gagal memuat modal. Silakan coba lagi.'
    //                 });
    //             }
    //         });
    //     });
    // });
function notes(btn) {
    const id = btn.getAttribute('data-id');
    let url = `{{ route('suratPernyataan.notes', ['id' => ':id']) }}`;
    url = url.replace(':id', id);

    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            // Masukkan konten modal baru
            $('#modal-container').html(response);

            // Tunggu sedikit agar DOM siap
            setTimeout(() => {
                const modalElement = document.getElementById('tolakModal');
                if (!modalElement) {
                    console.error('Modal tolakModal tidak ditemukan!');
                    return;
                }

                // Cek dan tutup modal sebelumnya jika ada
                const parentModalEl = document.getElementById('modalVerifikasi');
                const parentModal = bootstrap.Modal.getInstance(parentModalEl);

                if (parentModal) {
                    // Setelah modal parent ditutup, baru tampilkan modal baru
                    const handler = function () {
                        parentModalEl.removeEventListener('hidden.bs.modal', handler);
                        const modalInstance = new bootstrap.Modal(modalElement);
                        modalInstance.show();
                    };

                    parentModalEl.addEventListener('hidden.bs.modal', handler);
                    parentModal.hide();
                } else {
                    // Jika tidak ada modal parent, langsung tampilkan
                    const modalInstance = new bootstrap.Modal(modalElement);
                    modalInstance.show();
                }
            }, 50); // Delay pendek untuk memastikan DOM update
        },
        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal memuat modal. Silakan coba lagi.'
            });
        }
    });
}


</script>
