<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CsvResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'avgPrice' => $this['averagePrice'],
            'totalHousesSold' => $this['totalHousesSold'],
            'numberOfCrimesIn2011' => $this['numberOfCrimesIn2011'],
            'avgPricePerYearInLondonArea' => json_encode($this['averagePricePerYearInLondonArea'])
        ];
    }
}
