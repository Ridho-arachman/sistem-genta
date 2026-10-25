<?php

namespace App\Exports;

use App\Models\Absensi;

class AbsensiXlsxExport
{
    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?string $dapurId = null,
    ) {}

    public function generate(): string
    {
        $absensi = $this->query()->latest()->get();

        $xmlSheet = $this->buildSheetXml($absensi);
        $contentTypes = $this->buildContentTypes();
        $rels = $this->buildRels();
        $workbookRels = $this->buildWorkbookRels();
        $workbook = $this->buildWorkbook();

        $zip = new \ZipArchive;
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/worksheets/sheet1.xml', $xmlSheet);

        $zip->close();

        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return $content;
    }

    protected function query()
    {
        $query = Absensi::with('user', 'dapur');

        if ($this->dapurId) {
            $query->where('dapur_id', $this->dapurId);
        }

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query;
    }

    protected function buildSheetXml($absensi): string
    {
        $rows = [];

        // Header row
        $rows[] = $this->row([
            ['A1', 'Tanggal', 'str'],
            ['B1', 'Pengawas', 'str'],
            ['C1', 'Dapur', 'str'],
            ['D1', 'Check-in', 'str'],
            ['E1', 'Check-out', 'str'],
            ['F1', 'Status', 'str'],
        ]);

        $rowIndex = 2;
        foreach ($absensi as $a) {
            $rows[] = $this->row([
                ["A{$rowIndex}", $a->created_at->format('Y-m-d H:i:s'), 'str'],
                ["B{$rowIndex}", $a->user->name, 'str'],
                ["C{$rowIndex}", $a->dapur->nama, 'str'],
                ["D{$rowIndex}", $a->check_in_at?->format('Y-m-d H:i:s') ?? '-', 'str'],
                ["E{$rowIndex}", $a->check_out_at?->format('Y-m-d H:i:s') ?? '-', 'str'],
                ["F{$rowIndex}", ucfirst(str_replace('_', ' ', $a->status)), 'str'],
            ]);
            $rowIndex++;
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <sheetData>
        '.implode("\n", $rows).'
    </sheetData>
</worksheet>';
    }

    protected function row(array $cells): string
    {
        $refs = '';
        $vals = '';
        foreach ($cells as [$ref, $val, $type]) {
            $escapedVal = htmlspecialchars((string) $val, \ENT_XML1 | \ENT_QUOTES, 'UTF-8');
            $refs .= "<c r=\"{$ref}\" t=\"{$type}\"><v>{$escapedVal}</v></c>";
        }

        return "<row>{$refs}</row>";
    }

    protected function buildContentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>';
    }

    protected function buildRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
    }

    protected function buildWorkbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>';
    }

    protected function buildWorkbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Laporan Absensi" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
    }
}
