<?php

namespace App\Http\Controllers;

use App\Http\Requests\Csv\ExportCsvRequest;
use App\Http\Requests\Csv\ImportCsvRequest;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\{ToteResource, ToteTableResource, ToteOrderItemTableResource};
use App\Http\Requests\Tote\{DestroyRequest, PrintBarcodesRequest, StoreRequest, UpdateRequest};
use App\Models\{Tote, Warehouse};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\View\View;

class ToteController extends Controller
{
    /**
     * Display a listing of the tote.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('totes.index', [
            'datatableOrder' => app()->editColumn->getDatatableOrder('totes'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'totes.name';
        $sortDirection = 'asc';
        $filterInputs =  $request->get('filter_form');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $customers = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $totesCollection = app('tote')->getQuery($filterInputs, $sortColumnName, $sortDirection);

        $totesCollection = $totesCollection->whereIn('warehouses.customer_id', $customers);

        if (!empty($request->get('from_date'))) {
            $totesCollection = $totesCollection->where('totes.created_at', '>=', $request->get('from_date'));
        }

        $term = $request->get('search')['value'];

        if ($term) {
            app('tote')->searchQuery($term, $totesCollection);
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $totesCollection = $totesCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $totes = $totesCollection->get();
        $totesCollection = ToteTableResource::collection($totes);

        return response()->json([
            'data' => $totesCollection,
            'visibleFields' => app('editColumn')->getVisibleFields('totes'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX,
        ]);
    }

    public function toteItemsDataTable(Request $request, Tote $tote = null): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'tote_order_items.name';
        $sortDirection = 'asc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $toteItemsCollection = app('tote')->getToteItemsQuery(null, $sortColumnName, $sortDirection);

        $toteItemsCollection = $toteItemsCollection->where('tote_id', $tote->id);

        if (!empty($request->get('from_date'))) {
            $toteItemsCollection = $toteItemsCollection->where('tote_order_items.created_at', '>=', $request->get('from_date'));
        }

        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $toteItemsCollection = $toteItemsCollection->whereIn('orders.customer_id', $customers);

        $term = $request->get('search')['value'];

        if ($term) {
            $term = $term . '%';

            $toteItemsCollection->where(function ($q) use ($term) {
                $q->where('orders.number', 'like', $term)
                    ->orWhere('products.sku', 'like', $term);
            });
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $toteItemsCollection = $toteItemsCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $toteItems = $toteItemsCollection->get();
        $toteItemsCollection = ToteOrderItemTableResource::collection($toteItems);

        return response()->json([
            'data' => $toteItemsCollection,
            'visibleFields' => app()->editColumn->getVisibleFields('tote_order_items'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX,
        ]);
    }

    /**
     * Show the form for creating a new tote.
     *
     * @return Factory|Application|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('totes.create');
    }

    /**
     * Store a newly created tote in storage.
     *
     * @param StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        app('tote')->store($request);

        return redirect()->route('tote.index')->with(__('Tote successfully created!'));
    }

    /**
     * Display the specified tote.
     *
     * @param  Tote  $tote
     * @return ToteResource
     */
    public function show(Tote $tote): ToteResource
    {
        return new ToteResource($tote);
    }

    /**
     * Show the form for editing the specified tote.
     *
     * @param  Tote $tote
     * @return Factory|View
     */
    public function edit(Tote $tote)
    {
        return view('totes.edit', [
            'tote' => $tote,
            'datatableOrder' => app()->editColumn->getDatatableOrder('tote_order_items'),
        ]);
    }

    /**
     * Update the specified tote in storage.
     *
     * @param UpdateRequest $request
     * @param Tote $tote
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Tote $tote): RedirectResponse
    {
        app()->tote->update($request, $tote);

        return redirect()->back()->withStatus(__('Tote successfully updated.'));
    }

    /**
     * Remove the specified tote from storage.
     *
     * @param DestroyRequest $request
     * @return RedirectResponse
     */
    public function destroy(DestroyRequest $request): RedirectResponse
    {
        if (app()->tote->destroy($request)) {
            return redirect()->route('tote.index')->withStatus(__('Tote successfully deleted.'));
        }

        return redirect()->route('tote.index')->withErrors(__('Tote is not empty and could not be deleted.'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function filterWarehouses(Request $request)
    {
        return app()->tote->filterWarehouses($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function filterPickingCarts(Request $request)
    {
        return app('tote')->filterPickingCarts($request);
    }

    /**
     * @param Tote $tote
     * @return RedirectResponse
     */
    public function clearItems(Tote $tote): RedirectResponse
    {
        app('tote')->clearTote($tote);

        return redirect()->back()->withStatus(__('Tote items successfully cleared.'));
    }

    /**
     * @param Tote $tote
     * @return mixed
     */
    public function barcode(Tote $tote)
    {
        return app('tote')->barcode($tote);
    }

    public function printBarcodes(PrintBarcodesRequest $request)
    {
        return app('tote')->printBarcodes($request);
    }

    /**
     * @param ImportCsvRequest $request
     * @return JsonResponse
     */
    public function importCsv(ImportCsvRequest $request): JsonResponse
    {
        $message = app('tote')->importCsv($request);

        return response()->json([
            'success' => true,
            'message' => __($message)
        ]);
    }

    /**
     * @param ExportCsvRequest $request
     * @return mixed
     */
    public function exportCsv(ExportCsvRequest $request)
    {
        return app('tote')->exportCsv($request);
    }
}
