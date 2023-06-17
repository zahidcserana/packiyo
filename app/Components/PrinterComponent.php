<?php

namespace App\Components;

use App\Http\Requests\Printer\PrinterJobStartRequest;
use App\Http\Requests\Printer\PrinterJobStatusRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Printer;
use App\Models\PrintJob;
use App\Models\UserSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrinterComponent extends BaseComponent
{
    public function storeOrUpdate($printer, $userId, $customerId)
    {
        return Printer::updateOrCreate(
            [
                'hostname' => $printer['hostname'],
                'name' => $printer['name'],
                'user_id' => $userId
            ],
            [
                'customer_id' => $customerId
            ]
        );
    }

    public function disable(Printer $printer)
    {
        return $printer->update(['disabled_at' => now()]);
    }

    public function enable(Printer $printer)
    {
        return $printer->update(['disabled_at' => null]);
    }

    public function getPrinters()
    {
        $userCustomers = app()->user->getCustomers(true);

        $query = Printer::query();
        $customerNum = 0;

        foreach($userCustomers as $customer){
            $customerNum ++;

            if ($customerNum == 1) {
                $query->where('customer_id', $customer->id);//$customer->pivot->customer_id
            }
            else{
                $query->orWhere('customer_id', $customer->id);//$customer->pivot->customer_id
            }
        }

        return $query->get();
    }

    public function createJob(Request $request)
    {
        $order = null;
        // TODO: prepare function for actual request
        return PrintJob::create([
            'object_type' => Order::class,
            'object_id' => $order->id,
            'url' => route('order.getOrderSlip', [
                'order' => $order
            ]),
            'printer_id' => $this->getDefaultBarcodePrinter(),
            'user_id' => auth()->user()->id
        ]);
    }

    public function setJobStart(PrintJob $printJob, PrinterJobStartRequest $request)
    {
        $data = $request->validated();
        $data['job_start'] = Carbon::now();

        $printJob->update($data);
    }

    public function setJobStatus(PrintJob $printJob, PrinterJobStatusRequest $request)
    {
        $data = $request->validated();

        if ($request->job_end == 1) {
            $data['job_end'] = Carbon::now();
        }

        $printJob->update($data);
    }

    public function repeatJob(PrintJob $printJob)
    {
        $newJob = $printJob->replicate([
            'job_start',
            'job_end',
            'job_id_system',
            'status'
        ]);
        $newJob->save();

        return $newJob;
    }

    public function getDefaultLabelPrinter(Customer $customer)
    {
        if ($userPrinter = Printer::find(user_settings(UserSetting::USER_SETTING_LABEL_PRINTER_ID))) {
            return $userPrinter;
        }

        return $customer->labelPrinter();
    }

    public function getDefaultBarcodePrinter(Customer $customer)
    {
        if ($userPrinter = Printer::find(user_settings(UserSetting::USER_SETTING_BARCODE_PRINTER_ID))) {
            return $userPrinter;
        }

        return $customer->barcodePrinter();
    }

    public function getDefaultSlipPrinter(Customer $customer)
    {
        if ($userPrinter = Printer::find(user_settings(UserSetting::USER_SETTING_SLIP_PRINTER_ID))) {
            return $userPrinter;
        }

        return $customer->slipPrinter();
    }
}
