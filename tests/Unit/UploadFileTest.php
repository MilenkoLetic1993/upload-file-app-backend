<?php

namespace Tests\Unit;

use App\Models\Housing;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadFileTest extends TestCase
{
    /**
     * @param array $header
     * @param array $fileData
     * @return UploadedFile
     */
    private function generateUploadedCsvFile(array $header, array $fileData = []): UploadedFile
    {
        $csvData = [];
        array_push($csvData, $header);

        if (!empty($fileData)) {
            array_push($csvData, $fileData);
        }

        $fp = fopen(storage_path("app/" . "test.csv"), 'w');

        foreach ($csvData as $data) {
            fputcsv($fp, $data);
        }

        fclose($fp);

        return new UploadedFile(storage_path("app/test.csv"), "test.csv", "text/csv",null, true);
    }
    /**
     *
     * @return void
     */
    public function testUploadingFileValidationFailed()
    {
        $this->json('POST', '/api/upload-file')
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'file' => [
                        'The file field is required.'
                    ],
                    'source' => [
                        'The source field is required.'
                    ],
                    'saveToDatabase' => [
                        'The save to database field is required.'
                    ]
                ]
            ]);

        $this->json('POST', '/api/upload-file', [
            'file' => UploadedFile::fake()->image('random.jpg'),
            'source' => 'dummySource',
            'saveToDatabase' => '5'
        ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'file' => [
                        'The file must be a file of type: csv, txt.'
                    ],
                    'source' => [
                        'The selected source is invalid.'
                    ],
                    'saveToDatabase' => [
                        'The save to database field must be true or false.'
                    ]
                ]
            ]);
    }

    /**
     * @return void
     */
    public function testUploadNotWellStructuredFile()
    {
        $this->json('POST', '/api/upload-file', [
            'file' => UploadedFile::fake()->image('random.csv'),
            'source' => Housing::KAGGLE_SOURCE,
            'saveToDatabase' => true
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    Housing::KAGGLE_SOURCE => [
                        __("csv.not_well_structured")
                    ]
                ]
            ]);
    }

    /**
     * @return void
     */
    public function testUploadEmptyFile()
    {
        $emptyFile = $this->generateUploadedCsvFile(['date', 'area', 'average_price', 'code', 'houses_sold', 'no_of_crimes', 'borough_flag']);

        $this->json('POST', '/api/upload-file', [
            'file' => $emptyFile,
            'source' => Housing::KAGGLE_SOURCE,
            'saveToDatabase' => true
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    Housing::KAGGLE_SOURCE => [
                        __("csv.empty_file")
                    ]
                ]
            ]);
    }

    /**
     * @return void
     */
    public function testUploadBadOrCorruptedFile()
    {
        $header = ['date', 'area', 'average_price', 'code', 'houses_sold', 'no_of_crimes', 'borough_flag'];
        $fileData = ['string', 'string', 'string', 'string', 'string', 'string', 'string'];

        $emptyFile = $this->generateUploadedCsvFile($header, $fileData);

        $this->json('POST', '/api/upload-file', [
            'file' => $emptyFile,
            'source' => Housing::KAGGLE_SOURCE,
            'saveToDatabase' => true
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    Housing::KAGGLE_SOURCE => [
                        __("csv.bad_or_corrupted")
                    ]
                ]
            ]);
    }

    /**
     * @return void
     */
    public function testUploadingFileSuccessfully()
    {
        /***************** Test upload file successfully while inserting data in database **********/
        $averagePrice = rand(1, 100);
        $housesSold = rand(1, 100);
        $noOfCrimes = rand(1, 100);

        $header = ['date', 'area', 'average_price', 'code', 'houses_sold', 'no_of_crimes', 'borough_flag'];
        $fileData = ['2011-01-01', 'test-area', $averagePrice, 'test-code', $housesSold, $noOfCrimes, 0];
        $file = $this->generateUploadedCsvFile($header, $fileData);

        $this->json('POST', '/api/upload-file', [
            'file' => $file,
            'source' => Housing::KAGGLE_SOURCE,
            'saveToDatabase' => true,
            'type' => 'file'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'avgPrice' => $averagePrice,
                    'totalHousesSold' => $housesSold,
                    'numberOfCrimesIn2011' => $noOfCrimes,
                    'avgPricePerYearInLondonArea' => json_encode([])
                ]

            ]);

        $this->assertDatabaseHas('housings', [
            'date' => $fileData[0],
            'area' => $fileData[1],
            'average_price' => $fileData[2],
            'code' => $fileData[3],
            'houses_sold' => $fileData[4],
            'number_of_crimes' => $fileData[5],
            'borough_flag' => $fileData[6]
        ]);

        /***************** Test upload file successfully without inserting data in database **********/
        $averagePrice = rand(1, 100);
        $housesSold = rand(1, 100);
        $noOfCrimes = rand(1, 100);

        $header = ['date', 'area', 'average_price', 'code', 'houses_sold', 'no_of_crimes', 'borough_flag'];
        $fileData = ['2011-01-01', 'test-area', $averagePrice, 'test-code', $housesSold, $noOfCrimes, 0];
        $file = $this->generateUploadedCsvFile($header, $fileData);

        $this->json('POST', '/api/upload-file', [
            'file' => $file,
            'source' => Housing::KAGGLE_SOURCE,
            'saveToDatabase' => false,
            'type' => 'file'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'avgPrice' => $averagePrice,
                    'totalHousesSold' => $housesSold,
                    'numberOfCrimesIn2011' => $noOfCrimes,
                    'avgPricePerYearInLondonArea' => json_encode([])
                ]

            ]);

        $this->assertDatabaseMissing('housings', [
            'date' => $fileData[0],
            'area' => $fileData[1],
            'average_price' => $fileData[2],
            'code' => $fileData[3],
            'houses_sold' => $fileData[4],
            'number_of_crimes' => $fileData[5],
            'borough_flag' => $fileData[6]
        ]);
    }
}
