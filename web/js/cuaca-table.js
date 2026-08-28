// Fungsi inisialisasi DataTable
function initCuacaDataTable() {
    if ($('#table-cuaca-group').length && !$.fn.DataTable.isDataTable('#table-cuaca-group')) {
        $('#table-cuaca-group').DataTable({
            "searching": false,
            "ordering": false,
            "pageLength": 10,
            "language": {
                "lengthMenu": "Tampilkan _MENU_ Tanggal Per Halaman",
                "info": "Menampilkan _START_ Sampai _END_ Dari _TOTAL_ Tanggal",
            },
        });
    }
}

$(document).ready(function () {
    initCuacaDataTable();
});

// Re-inisialisasi DataTable setelah PJAX selesai me-reload tabel
$(document).on('pjax:complete pjax:end', function () {
    initCuacaDataTable();
});

$(document).off('click', '#table-cuaca-group .btn-expand-detail').on('click', '#table-cuaca-group .btn-expand-detail', function () {

    // Ambil data konfigurasi terbaru dari window (jika berubah saat filter PJAX)
    var ajaxUrl = window.cuacaConfig ? window.cuacaConfig.ajaxDetailUrl : '';
    var kelurahanId = window.cuacaConfig ? window.cuacaConfig.kelurahanId : '';
    var pdfBaseUrl = window.cuacaConfig ? window.cuacaConfig.exportPdfBaseUrl : '';

    var table = $('#table-cuaca-group').DataTable();
    var tr = $(this).closest('tr');
    var row = table.row(tr);
    var btn = $(this);
    var tanggal = tr.data('tgl');

    if (row.child.isShown()) {
        // TUTUP / COLLAPSE
        row.child.hide();
        tr.removeClass('shown');
        btn.html('<i class="bi bi-plus-square-fill me-1"></i> Buka Detail')
            .removeClass('btn-danger')
            .addClass('btn-outline-primary');
    } else {
        // BUKA / EXPAND
        row.child(formatSubTable(tanggal, pdfBaseUrl)).show();
        tr.addClass('shown');
        btn.html('<i class="bi bi-dash-square-fill me-1"></i> Tutup')
            .removeClass('btn-outline-primary')
            .addClass('btn-danger');

        var childNode = $(row.child());
        var spinner = childNode.find('.loading-spinner');
        var subTable = childNode.find('.detail-subtable');

        // AJAX request data detail per jam
        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            data: {
                kelurahan_id: kelurahanId,
                tanggal: tanggal
            },
            dataType: 'json',
            success: function (response) {
                spinner.addClass('d-none');
                subTable.removeClass('d-none');

                if (response.status === 'success' && response.data.length > 0) {
                    var rowsHtml = '';
                    $.each(response.data, function (i, item) {
                        rowsHtml += `
                            <tr>
                                <td class="text-center text-muted fs-7">${item.no}</td>
                                <td class="text-center">${item.local_datetime}</td>
                                <td>${item.kondisi_cuaca}</td>
                                <td class="text-center">${item.suhu}</td>
                                <td class="text-center">${item.kelembapan}</td>
                                <td class="text-center">${item.kecepatan_angin}</td>
                                <td class="text-center">${item.arah_angin}</td>
                                <td class="text-center">${item.action}</td>
                            </tr>
                        `;
                    });
                    subTable.find('tbody').html(rowsHtml);
                } else {
                    subTable.find('tbody').html('<tr><td colspan="9" class="text-center text-muted">Tidak ada data cuaca.</td></tr>');
                }
            },
            error: function () {
                spinner.addClass('d-none');
                subTable.removeClass('d-none');
                subTable.find('tbody').html('<tr><td colspan="9" class="text-center text-danger">Gagal memuat data dari server.</td></tr>');
            }
        });
    }
});

// Helper untuk membuat HTML Sub-Tabel Detail
function formatSubTable(tanggalStr, pdfBaseUrl) {
    var exportPdfUrl = pdfBaseUrl + '&tanggal=' + tanggalStr;

    return `
        <div class="p-3 border rounded">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-secondary">
                    <i class="bi bi-clock-history me-1"></i> Detail Rincian Per Jam
                </span>
                <a href="${exportPdfUrl}" target="_blank" data-pjax="0" class="btn btn-sm btn-danger shadow-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF Tanggal Ini
                </a>
            </div>

            <div class="text-center text-muted loading-spinner py-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                Memuat Data Cuaca Per Jam
            </div>
            <table class="table table-sm table-bordered table-hover detail-subtable d-none bg-white align-middle mb-0">
                <thead class="text-center">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Waktu Prakiraan (WIB)</th>
                        <th>Kondisi Cuaca</th>
                        <th>Suhu</th>
                        <th>Kelembapan</th>
                        <th>Kecepatan Angin</th>
                        <th>Arah Angin</th>
                        <th>Galeri Foto</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    `;
}