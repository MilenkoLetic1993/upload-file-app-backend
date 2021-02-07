<?php


namespace Services\Csv;


use App\DataTransferObjects\CsvDto;
use Carbon\Carbon;

class StatisticService
{
    /** @var int */
    private int $totalPrice = 0;

    /** @var int */
    private int $housesSoldNumber = 0;

    /** @var int */
    private int $numberOfCrimesIn2011 = 0;

    /** @var array */
    private array $averagePricePerYearInLondonArea = [];

    /** @var array */
    private array $tempAveragePricePerYear = [];

    const LONDON_AREA = 'london';

    /**
     * @param int $year
     * @param int $price
     */
    private function setAveragePricePerYearInLondonArea(int $year, int $price)
    {
        if (!isset($this->tempAveragePricePerYear[$year]['price']) && !isset($this->tempAveragePricePerYear[$year]['itemsCount'])) {
            $this->tempAveragePricePerYear[$year]['price'] = $price;
            $this->tempAveragePricePerYear[$year]['itemsCount'] = 1;
        } else {
            $this->tempAveragePricePerYear[$year]['price'] += $price;
            $this->tempAveragePricePerYear[$year]['itemsCount'] += 1;
        }

        $result = $this->tempAveragePricePerYear[$year]['price'] / $this->tempAveragePricePerYear[$year]['itemsCount'];
        $this->averagePricePerYearInLondonArea[$year] = round($result, 2);
    }

    /**
     * @param CsvDto $csvDto
     */
    public function setData(CsvDto $csvDto)
    {
        $this->totalPrice += $csvDto->average_price;
        $this->housesSoldNumber += $csvDto->houses_sold;

        if ((int)Carbon::parse($csvDto->date)->format('Y') === 2011) {
            $this->numberOfCrimesIn2011 += $csvDto->number_of_crimes;
        }

        if ($csvDto->area === self::LONDON_AREA) {
            $this->setAveragePricePerYearInLondonArea(Carbon::parse($csvDto->date)->format('Y'), $csvDto->average_price);
        }
    }

    /**
     * @return int
     */
    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    /**
     * @return int
     */
    public function getHousesSoldNumber(): int
    {
        return $this->housesSoldNumber;
    }

    /**
     * @return int
     */
    public function getNumberOfCrimesIn2011(): int
    {
        return $this->numberOfCrimesIn2011;
    }

    /**
     * @return array
     */
    public function getAveragePricePerYearInLondonArea(): array
    {
        return $this->averagePricePerYearInLondonArea;
    }
}
