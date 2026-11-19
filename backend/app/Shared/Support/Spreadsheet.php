<?php

namespace App\Shared\Support;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Streaming .xlsx export / import (OpenSpout), used for every "Excel" button and import in the panel. */
class Spreadsheet
{
    /**
     * @param  list<string>  $headers
     * @param  iterable<list<scalar|null>>  $rows
     */
    public static function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $writer = new Writer;
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues($headers));

            foreach ($rows as $row) {
                $writer->addRow(Row::fromValues(array_map(fn ($v) => $v ?? '', $row)));
            }

            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /**
     * Rows of the first sheet keyed by the header row (lower-cased, trimmed); empty rows are skipped.
     *
     * @return \Generator<int, array<string, string>> row number (2 = first data row) => values
     */
    public static function read(string $path): \Generator
    {
        $reader = new Reader;
        $reader->open($path);

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $headers = null;
                $number = 0;

                foreach ($sheet->getRowIterator() as $row) {
                    $number++;
                    $values = array_map(fn ($v) => $v instanceof \DateTimeInterface ? $v->format('Y-m-d H:i:s') : trim((string) $v), $row->toArray());

                    if ($headers === null) {
                        $headers = array_map(fn ($h) => mb_strtolower($h), $values);

                        continue;
                    }

                    if (implode('', $values) === '') {
                        continue;
                    }

                    yield $number => array_combine($headers, array_pad(array_slice($values, 0, count($headers)), count($headers), ''));
                }

                break; // first sheet only
            }
        } finally {
            $reader->close();
        }
    }
}
