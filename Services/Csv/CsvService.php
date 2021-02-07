<?php


namespace Services\Csv;


use Illuminate\Http\UploadedFile;

class CsvService
{
    /**
     * @param UploadedFile $uploadedFile
     * @return array
     */
    public static function parseUploadedFile(UploadedFile $uploadedFile): array
    {
        $file = fopen($uploadedFile->getRealPath(), "r");
        $header = true;
        $headerNames = [];
        $data = [];

        while ($csvLine = fgetcsv($file)) {
            if ($header) {
                $header = false;
                $headerNames = $csvLine;
            } else {
                $tempData = [];
                foreach ($headerNames as $key => $headerName) {
                    $tempData[$headerName] = $csvLine[$key];
                }
                $data[] = $tempData;
            }
        }

        return $data;
    }
}
