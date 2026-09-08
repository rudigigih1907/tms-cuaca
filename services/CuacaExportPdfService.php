<?php

namespace app\services;

use app\components\PdfBuilder;
use app\models\Cuaca;
use app\models\Wilayah;
use kartik\mpdf\Pdf;
use Yii;

class CuacaExportPdfService
{
    public function generatePdf(string $kelurahanId, string $tanggal, ?string $viewPath = null): ?Pdf
    {
        // Ambil data
        $dataCuaca = Cuaca::find()
            ->where(['kode_adm4' => $kelurahanId])
            ->andWhere(['DATE(local_datetime)' => $tanggal])
            ->orderBy(['local_datetime' => SORT_ASC])
            ->all();

        if (empty($dataCuaca)) {
            return null;
        }

        $detailWilayah = Wilayah::getDetailWilayahByKode($kelurahanId);

        $logoPath = Yii::getAlias('@webroot/images/logo2.png');
        $sourceUrl = 'https://data.bmkg.go.id/prakiraan-cuaca/';
        $namaPerusahaan = 'PT. Pelayaran Tresnamuda Sejati';

        $targetView = $viewPath ?? '@app/views/cuaca/_report_pdf.php';
        $content = Yii::$app->view->renderFile($targetView, [
            'dataCuaca'     => $dataCuaca,
            'detailWilayah' => $detailWilayah,
            'kodeAdm4'      => $kelurahanId,
            'tanggal'       => $tanggal,
            'logoPath'      => $logoPath,
            'sourceUrl'     => $sourceUrl,
            'namaPerusahaan' => $namaPerusahaan,
        ]);

        $namaKelurahan = $detailWilayah['kelurahan'] ?? $kelurahanId;

        return PdfBuilder::create()
            ->setContent($content)
            ->setTitle("Laporan Cuaca - {$namaPerusahaan} - {$namaKelurahan} ({$tanggal})")
            ->setTitle("Laporan Cuaca - {$namaKelurahan} ({$tanggal})")
            ->setHeader('LAPORAN PRAKIRAAN CUACA BMKG||Tgl Cetak: ' . date('d/m/Y H:i'))
            ->build();
    }

    public function generatePdfOutput(string $kelurahanId, string $tanggal): ?string
    {
        $pdf = $this->generatePdf($kelurahanId, $tanggal);
        if (!$pdf) {
            return null;
        }

        $pdf->destination = Pdf::DEST_STRING;
        return $pdf->render();
    }
}
