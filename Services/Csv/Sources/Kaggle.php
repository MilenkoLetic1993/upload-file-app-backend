<?php


namespace Services\Csv\Sources;

use App\Contracts\Services\Csv\CsvFileReaderInterface;
use App\DataTransferObjects\CsvDto;
use App\Models\Housing;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Services\Csv\CsvService;
use Services\Csv\HousingService;
use Services\Csv\StatisticService;

class Kaggle implements CsvFileReaderInterface
{
    /** @var array */
    private array $parsedData = [];

    /** @var int */
    private int $parsedDataNumber = 0;

    /** @var StatisticService */
    private StatisticService $statisticService;

    /** @var HousingService */
    private HousingService $housingService;

    const CHUNKED_DATA_SIZE = 500;
    const CSV_DATA_STRUCTURE = ['date', 'area', 'average_price', 'code', 'houses_sold', 'no_of_crimes', 'borough_flag'];

    /**
     * Kaggle constructor.
     */
    public function __construct()
    {
        $this->statisticService = new StatisticService();
        $this->housingService = new HousingService();
    }

    /**
     * @param UploadedFile $uploadedFile
     */
    public function readFromFile(UploadedFile $uploadedFile): void
    {
        $this->parsedData = CsvService::parseUploadedFile($uploadedFile);
        $this->parsedDataNumber = sizeof($this->parsedData);
    }

    /**
     * @param bool $insertParsedDataInDatabase
     * @throws ValidationException
     */
    public function setStatistic(bool $insertParsedDataInDatabase = false): void
    {
        if (!$this->parsedDataNumber) {
            throw ValidationException::withMessages([
                Housing::KAGGLE_SOURCE => [__("csv.empty_file")]
            ]);

        }

        $numberOfFormattedData = 0;

        foreach (array_chunk($this->parsedData, self::CHUNKED_DATA_SIZE) as $chunkedData) {
            foreach ($chunkedData as $data) {
                $csvDtoObject = CsvDto::fromKaggleSource($data, self::CSV_DATA_STRUCTURE);
                $numberOfFormattedData++;

                $this->statisticService->setData($csvDtoObject);

                if ($insertParsedDataInDatabase) {
                    $this->housingService->insertInDatabase($csvDtoObject, $numberOfFormattedData, $this->parsedDataNumber);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getStatistic(): array
    {
        return [
            'averagePrice' => round($this->statisticService->getTotalPrice() / $this->parsedDataNumber, 2),
            'totalHousesSold' => $this->statisticService->getHousesSoldNumber(),
            'numberOfCrimesIn2011' => $this->statisticService->getNumberOfCrimesIn2011(),
            'averagePricePerYearInLondonArea' => $this->statisticService->getAveragePricePerYearInLondonArea()
        ];
    }
}
