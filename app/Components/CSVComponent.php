<?php

namespace App\Components;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CSVComponent extends BaseComponent
{
    public function export(
        Request $request,
        $data,
        array $columns,
        string $csvFileName,
        string $exportResourceClass
    ): StreamedResponse {
        $callback = function () use ($data, $exportResourceClass, $columns, $request) {
            $file = fopen('php://output', 'wb');
            fputcsv($file, $columns);

            foreach ($data as $obj) {
                $row = new $exportResourceClass($obj);

                $resource = $row->toArray($request);

                if (Arr::exists($resource, 0) && is_array($resource[0])) {
                    foreach ($resource as $value) {
                        fputcsv($file, $value);
                    }
                } else if ($resource) {
                    fputcsv($file, $resource);
                }
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $csvFileName, $this->setHeaders($csvFileName));
    }

    public function unsetCsvHeader(&$data, $condition): array
    {
        $header = array_map('strtolower', $data[0]);

        if (in_array(strtolower($condition), $header, true)) {
            unset($data[0]);

            return $header;
        }

        return [];
    }

    private function setHeaders($csvFilename): array
    {
        return [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=file.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'X-Export-Filename' => $csvFilename
        ];
    }

    public function getCsvData($inputCsv): array
    {
        $data = [];

        if (($open = fopen($inputCsv, 'rb')) !== false) {
            while (($items = fgetcsv($open, 1000)) !== false) {
                $data[] = $items;
            }

            fclose($open);
        }

        return $data;
    }
}
