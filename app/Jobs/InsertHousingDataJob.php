<?php

namespace App\Jobs;

use App\Models\Housing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InsertHousingDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * @var array
     */
    public array $chunkedHousingData;

    /**
     * Create a new job instance.
     *
     * @param array $chunkedHousingData
     */
    public function __construct(array $chunkedHousingData)
    {
        $this->chunkedHousingData = $chunkedHousingData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Housing::insert($this->chunkedHousingData);
        } catch (\Exception $exception) {
            $this->release(1);
            Log::error("error while inserting housing data", ["message" => $exception->getMessage()]);
        }
    }
}
