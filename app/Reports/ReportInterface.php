<?php

namespace App\Reports;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ReportInterface
{
    public function view(Request $request): Factory|View|Application;
    public function dataTable(Request $request): JsonResponse;
    public function export(Request $request): StreamedResponse;
    public function widgets(Request $request): Factory|View|Application;
}
