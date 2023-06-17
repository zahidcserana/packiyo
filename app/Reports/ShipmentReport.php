<?php

namespace App\Reports;

use App\Http\Dto\Filters\Reports\ShipmentReportFilterDto;
use App\Http\Resources\ExportResources\ShipmentReportExportResource;
use App\Http\Resources\ShipmentReportTableResource;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShippingCarrier;
use App\Models\ShippingMethod;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipmentReport extends Report
{
    protected $reportId = 'shipment';
    protected $dataTableResourceClass = ShipmentReportTableResource::class;
    protected $exportResourceClass = ShipmentReportExportResource::class;
    protected $widget = true;

    protected function reportTitle()
    {
        return __('Shipments Report');
    }

    protected function getQuery(Request $request): ?Builder
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'created_at';
        $sortDirection = 'desc';
        $filterInputs =  $request->get('filter_form');

        $search = $request->get('search');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        if ($term = Arr::get($search, 'value')) {
            $filterInputs = [];
        }

        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $filterCustomerId = Arr::get($filterInputs, 'customer_id');

        if ($filterCustomerId && $filterCustomerId != 'all') {
            $customerIds = array_intersect($customerIds, [$filterCustomerId]);
        }

        $query = Shipment::with([
            'order.orderItems',
            'order.shippingContactInformation.country',
            'contactInformation.country',
            'shipmentItems.orderItem',
            'shipmentLabels',
            'shipmentTrackings',
            'shippingMethod.shippingCarrier',
            'user.contactInformation'
        ])
            ->join('orders', 'shipments.order_id', '=', 'orders.id')
            ->join('contact_informations AS shipping_contact_information', 'orders.shipping_contact_information_id', '=', 'shipping_contact_information.id')
            ->join('countries AS country', 'shipping_contact_information.country_id', '=', 'country.id')
            ->whereIn('orders.customer_id', $customerIds)
            ->when(!empty($filterInputs), static function (Builder $query) use ($filterInputs) {
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('shipments.created_at', [$startDate, $endDate]);
                }

                // Shipping Method
                if (Arr::get($filterInputs, 'shipping_method')) {
                    $query->join('shipping_methods', 'shipping_methods.id', '=', 'shipments.shipping_method_id');
                    $query->where('shipping_methods.name', $filterInputs['shipping_method']);
                }

                // Carriers
                if (Arr::get($filterInputs, 'shipping_carrier')) {
                    if (!collect($query->getQuery()->joins)->pluck('table')->contains('shipping_methods')) {
                        $query->join('shipping_methods', 'shipping_methods.id', '=', 'shipments.shipping_method_id');
                    }

                    $query->join('shipping_carriers', 'shipping_carriers.id', '=', 'shipping_methods.shipping_carrier_id');
                    $query->where('shipping_carriers.name', $filterInputs['shipping_carrier']);
                }

                if (Arr::get($filterInputs, 'shipping_method')) {
                    $query->where('shipping_methods.name', $filterInputs['shipping_method']);
                }

                if (Arr::get($filterInputs, 'shipping_carrier')) {
                    $query->where('shipping_carriers.name', $filterInputs['shipping_carrier']);
                }
            })
            ->when(!empty($term), static function (Builder $query) use ($term) {
                $term = $term . '%';

                $query->where('orders.number', 'like', $term);
            })
            ->select('shipments.*')
            ->groupBy('shipments.id')
            ->orderBy($sortColumnName, $sortDirection);

        return $query;
    }

    protected function getFilterDto(Request $request): ?Arrayable
    {
        $customerIds = app('user')->getSelectedCustomers()->pluck('id');

        $shippingCarriers = ShippingCarrier::whereIn('customer_id', $customerIds)->get();

        return new ShipmentReportFilterDto(
            $shippingCarriers->pluck('name')->unique(),
            ShippingMethod::whereIn('shipping_carrier_id', $shippingCarriers->pluck('id'))->get()->pluck('name')->unique()
        );
    }

    protected function getWidgetsQuery(Request $request): array
    {
        $filterInputs =  $request->get('filter_form');
        $search = $request->get('search');

        if ($term = Arr::get($search, 'value')) {
            $filterInputs = [];
        }

        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $filterCustomerId = Arr::get($filterInputs, 'customer_id');

        if ($filterCustomerId && $filterCustomerId != 'all') {
            $customerIds = array_intersect($customerIds, [$filterCustomerId]);
        }

        $query = Shipment::query()
            ->leftJoin('shipment_items', 'shipments.id', '=', 'shipment_items.shipment_id')
            ->leftJoin('orders', 'shipments.order_id', '=', 'orders.id')
            ->whereIn('orders.customer_id', $customerIds)
            ->when(!empty($filterInputs), static function (Builder $query) use ($filterInputs) {
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('shipments.created_at', [$startDate, $endDate]);
                }

                // Shipping Method
                if (Arr::get($filterInputs, 'shipping_method')) {
                    $query->join('shipping_methods', 'shipping_methods.id', '=', 'shipments.shipping_method_id');
                    $query->where('shipping_methods.name', $filterInputs['shipping_method']);
                }

                // Carriers
                if (Arr::get($filterInputs, 'shipping_carrier')) {
                    if (!collect($query->getQuery()->joins)->pluck('table')->contains('shipping_methods')) {
                        $query->join('shipping_methods', 'shipping_methods.id', '=', 'shipments.shipping_method_id');
                    }

                    $query->join('shipping_carriers', 'shipping_carriers.id', '=', 'shipping_methods.shipping_carrier_id');
                    $query->where('shipping_carriers.name', $filterInputs['shipping_carrier']);
                }
            })
            ->when(!empty($term), static function (Builder $query) use ($term) {
                $term = $term . '%';

                $query->where('orders.number', 'like', $term);
            })
            ->select('shipments.*')
            ->addSelect(DB::raw('COUNT(DISTINCT shipments.id) as shipmentCount'))
            ->addSelect(DB::raw('SUM(shipment_items.quantity) as totalItemsShipped'))
            ->addSelect(DB::raw('COUNT(shipment_items.id) as distinctItemsShipped'));

        return $query->first()
            ->toArray();
    }
}
