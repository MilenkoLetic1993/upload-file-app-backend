<?php

namespace App\Http\Controllers;

use App\Contracts\Services\Csv\CsvFileReaderInterface;
use App\Http\Requests\UploadFileRequest;
use App\Http\Resources\CsvResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UploadController extends Controller
{
    /**
     * @param UploadFileRequest $request
     * @return \Illuminate\Http\JsonResponse|CsvResource
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Exception
     */
    public function uploadFile(UploadFileRequest $request): \Illuminate\Http\JsonResponse|CsvResource
    {
        $file = $request->file('file');
        $source = $request->input('source');

        /** @var CsvFileReaderInterface $csvFileReader */
        $csvFileReader = app()->make('csvFileReader.source.' . $source);

        if (!$csvFileReader instanceof CsvFileReaderInterface) {
            throw new \Exception("File reader must implement interface " . CsvFileReaderInterface::class);
        }

        try {
            DB::beginTransaction();

            $csvFileReader->readFromFile($file);
            $csvFileReader->setStatistic($request->input('saveToDatabase'));

            DB::commit();
        } catch (ValidationException $validationException) {
            DB::rollBack();
            return response()->json(["errors" => $validationException->errors()],$validationException->status);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("uploadFileException", [$exception]);

            return response()->json(["errors" => [
                $source => [__("csv.error_while_uploading_file")]
            ]]);
        }

        // Added resource in case we need to adapt response for frontend
        return new CsvResource($csvFileReader->getStatistic());
    }
}
