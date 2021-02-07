<?php


namespace Services\Csv;


use App\DataTransferObjects\CsvDto;
use App\Jobs\InsertHousingDataJob;

class HousingService
{
    /** @var array  */
    private array $housingBulkData = [];

    const CHUNKED_DATA_SIZE = 500;

    /**
     * @param CsvDto $csvDto
     * @param int $numberOfFormattedData
     * @param int $parsedDataNumber
     */
    public function insertInDatabase(CsvDto $csvDto, int $numberOfFormattedData, int $parsedDataNumber)
    {
        $this->housingBulkData[] = $csvDto->toArray();

        if ((sizeof($this->housingBulkData) === self::CHUNKED_DATA_SIZE) || ($numberOfFormattedData === $parsedDataNumber)) {
            dispatch(new InsertHousingDataJob($this->housingBulkData));
            $this->housingBulkData = [];
        }
    }
}
