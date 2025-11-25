<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MemberCardController extends Controller
{
    public function download(Customer $customer)
    {
        $pdf = Pdf::loadView('pdf.member-card', [
            'customer' => $customer
        ]);
        
        // Set paper size to credit card size (landscape)
        // 85.6mm x 53.98mm
        $pdf->setPaper([0, 0, 242.64, 153.07], 'landscape'); // Points (1mm = 2.83465pt)

        return $pdf->download('member-card-' . $customer->card_number . '.pdf');
    }
}
