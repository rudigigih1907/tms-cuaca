<?php

namespace app\components;

use kartik\mpdf\Pdf;

class PdfBuilder
{
    private string $content = '';
    private string $title = 'Laporan';
    private string $headerText = '';
    private string $footerText = '|Halaman {PAGENO} dari {nbpg}|';
    private string $orientation = Pdf::ORIENT_PORTRAIT;

    public static function create(): self
    {
        return new self();
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setHeader(string $headerText): self
    {
        $this->headerText = $headerText;
        return $this;
    }

    public function setLandscape(): self
    {
        $this->orientation = Pdf::ORIENT_LANDSCAPE;
        return $this;
    }

    /**
     * Membangun objek Kartik PDF dengan styling terpusat.
     */
    public function build(): Pdf
    {
        return new Pdf([
            'mode'        => Pdf::MODE_UTF8,
            'format'      => Pdf::FORMAT_A4,
            'orientation' => $this->orientation,
            'destination' => Pdf::DEST_BROWSER,
            'content'     => $this->content,
            'cssInline'   => $this->getDefaultCss(),
            'options'     => ['title' => $this->title],
            'methods'     => [
                'SetHeader' => [$this->headerText],
                'SetFooter' => [$this->footerText],
            ]
        ]);
    }

    /**
     * Menyimpan CSS default untuk laporan PDF aplikasi.
     */
    private function getDefaultCss(): string
    {
        return '
        body { font-family: sans-serif; font-size: 9.5pt; color: #333; }
        .meta-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .meta-table td { padding: 3px 5px; vertical-align: top; font-size: 9.5pt; }
        .table-data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-data th { background-color: #0056b3; color: #ffffff; border: 1px solid #004085; padding: 7px; font-weight: bold; font-size: 9pt; }
        .table-data td { border: 1px solid #cccccc; padding: 6px; font-size: 9pt; }
        .table-data tr:nth-child(even) { background-color: #f8f9fa; }
        .footer-text { margin-top: 25px; text-align: right; font-size: 8.5pt; color: #666; }
    ';
    }
}
