<?php


namespace App\Contracts\Services\Csv;


use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

interface CsvFileReaderInterface
{
    /**
     * @param UploadedFile $uploadedFile
     */
    public function readFromFile(UploadedFile $uploadedFile): void;

    /**
     * @param bool $insertParsedDataInDatabase
     * @throws ValidationException
     */
    public function setStatistic(bool $insertParsedDataInDatabase = false): void;

    /**
     * @return array
     */
    public function getStatistic(): array;
}
