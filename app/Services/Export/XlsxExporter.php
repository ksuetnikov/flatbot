<?php

declare(strict_types=1);

namespace App\Services\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class XlsxExporter
{
    /**
     * @param list<string>                $headers
     * @param iterable<int, list<mixed>>  $rows
     * @param iterable<int, list<mixed>>  $footerRows
     */
    public function download(
        string $filename,
        string $sheetTitle,
        array $headers,
        iterable $rows,
        iterable $footerRows = [],
    ): StreamedResponse {
        return response()->streamDownload(
            function () use ($headers, $rows, $footerRows, $sheetTitle): void {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle($this->sanitizeSheetTitle($sheetTitle));

                foreach ($headers as $columnIndex => $header) {
                    $sheet->setCellValue([$columnIndex + 1, 1], $header);
                }

                $headerRange = 'A1:'.$sheet->getHighestColumn().'1';
                $sheet->getStyle($headerRange)->getFont()->setBold(true);

                $rowIndex = 2;
                foreach ($rows as $row) {
                    foreach ($row as $columnIndex => $value) {
                        $sheet->setCellValue([$columnIndex + 1, $rowIndex], $value);
                    }
                    $rowIndex++;
                }

                $footerStartRow = $rowIndex;
                foreach ($footerRows as $row) {
                    foreach ($row as $columnIndex => $value) {
                        $sheet->setCellValue([$columnIndex + 1, $rowIndex], $value);
                    }
                    $rowIndex++;
                }

                if ($footerStartRow < $rowIndex) {
                    $footerRange = 'A'.$footerStartRow.':'.$sheet->getHighestColumn().($rowIndex - 1);
                    $sheet->getStyle($footerRange)->getFont()->setBold(true);
                }

                foreach (range(1, count($headers)) as $column) {
                    $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
                }

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                $spreadsheet->disconnectWorksheets();
            },
            $filename,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
        );
    }

    private function sanitizeSheetTitle(string $title): string
    {
        $sanitized = preg_replace('/[\\\\\\/\\?\\*\\[\\]:]/', '', $title) ?? 'Export';

        return mb_substr($sanitized, 0, 31) ?: 'Export';
    }
}
