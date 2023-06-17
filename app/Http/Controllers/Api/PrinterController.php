<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Printer\PrinterJobStartRequest;
use App\Http\Requests\Printer\PrinterJobStatusRequest;
use App\Http\Resources\PrinterResource;
use App\Models\Printer;
use App\Models\PrintJob;
use Illuminate\Http\Request;
use App\JsonApi\V1\Printers\PrinterSchema;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

class PrinterController extends ApiController
{
    public function import(Request $request)
    {
        $userId = auth()->user()->id;
        $printers = $request->printers;
        $customerId = $request->customer_id;

        foreach ($printers as $printer) {
            app('printer')->storeOrUpdate($printer, $userId, $customerId);
        }
    }

    public function userPrintersAndJobs(PrinterSchema $schema, AnonymousCollectionQuery $collectionQuery) : DataResponse
    {
        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', auth()->user()->printers->pluck('id'));

        return new DataResponse($models);
    }

    public function jobStart(PrintJob $printJob, PrinterJobStartRequest $request)
    {
        app('printer')->setJobStart($printJob, $request);
    }

    public function jobStatus(PrintJob $printJob, PrinterJobStatusRequest $request)
    {
        app('printer')->setJobStatus($printJob, $request);
    }
}
