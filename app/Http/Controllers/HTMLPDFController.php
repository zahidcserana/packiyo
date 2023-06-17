<?php

namespace App\Http\Controllers;

use PDF;
use Illuminate\Http\Request;

class HTMLPDFController extends Controller
{
    /**
     * generate PDF file from blade view.
     *
     * @return \Illuminate\Http\Response
     */
    public function htmlPdf()
    {
        // selecting PDF view
        $pdf = PDF::loadView('htmlView');

        // download pdf file
        return $pdf->download('pdfview.pdf');
    }
}
