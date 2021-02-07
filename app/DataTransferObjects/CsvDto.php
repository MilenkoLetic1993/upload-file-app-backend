<?php


namespace App\DataTransferObjects;

use App\Models\Housing;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\DataTransferObject\DataTransferObject;

class CsvDto extends DataTransferObject
{
    /** @var \Carbon\Carbon */
    public \Carbon\Carbon $date;

    /** @var string */
    public string $area;

    /** @var int */
    public int $average_price;

    /** @var string */
    public string $code;

    /** @var int */
    public int $houses_sold;

    /** @var int */
    public int $number_of_crimes;

    /** @var bool */
    public bool $borough_flag;

    /**
     * @param array $data
     * @param array $dataStructure
     * @return static
     * @throws ValidationException
     */
    public static function fromKaggleSource(array $data, array $dataStructure): self
    {
        self::validate($data, $dataStructure, Housing::KAGGLE_SOURCE);

        try {
            return new self([
                'date' => \Carbon\Carbon::parse($data['date']),
                'area' => (string)$data['area'],
                'average_price' => $data['average_price'] !== "" ? (int)$data['average_price'] : 0,
                'code' => (string)$data['code'],
                'houses_sold' => $data['houses_sold'] !== "" ? (int)$data['houses_sold'] : 0,
                'number_of_crimes' => $data['no_of_crimes'] !== "" ? (int)$data['no_of_crimes'] : 0,
                'borough_flag' => $data['borough_flag'] !== "" ? (bool)$data['borough_flag'] : false
            ]);
        } catch (\Exception $exception) {
            Log::error("fromKaggleSource CsvDto exception", [$exception]);
            throw ValidationException::withMessages([
                Housing::KAGGLE_SOURCE => [__("csv.bad_or_corrupted")]
            ]);
        }
    }

    /**
     * @param array $data
     * @param array $dataStructure
     * @param string $source
     * @throws ValidationException
     */
    private static function validate(array $data, array $dataStructure, string $source)
    {
        if (array_keys($data) !== $dataStructure) {
            throw ValidationException::withMessages([
                $source => [__("csv.not_well_structured")]
            ]);
        }
    }
}
