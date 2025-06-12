<!-- Modal Export -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('data_pendaftar.export_excel') }}" method="GET" target="_blank">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="myModalLabel">Export Data Pendaftar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="filter_tahun" class="form-label">Tahun</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-primary text-white">
                <i class="fas fa-calendar-alt me-1"></i> Tahun
              </span>
              <select id="filter_tahun" name="tahun" class="form-select">
                <option value="">Semua Tahun</option>
                @foreach (range(date('Y'), 2020) as $tahun)
                  <option value="{{ $tahun }}">{{ $tahun }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="verifikasi_data" class="form-label">Status Verifikasi</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-primary text-white">
                <i class="fas fa-check-circle me-1"></i> Status
              </span>
              <select id="verifikasi_data" name="verifikasi_data" class="form-select">
                <option value="">Semua Status</option>
                @foreach (['PENDING', 'DITOLAK', 'TERVERIFIKASI'] as $verifikasi_data)
                  <option value="{{ $verifikasi_data }}">{{ $verifikasi_data }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Export</button>
        </div>
      </div>
    </form>
  </div>
</div>
