<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
class PdfController extends Controller
{
    public function generatePdf($filename, $view, $data){
        try {
            $pdf = Pdf::loadView($view, ['data' => $data]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($filename.'.pdf');
        }
        catch (Exception $e) {
            // Manejar la excepción y devolver una respuesta adecuada
            return response()->json(['error' => 'Error al generar el PDF: ' . $e->getMessage()], 500);
        }
    }
}
