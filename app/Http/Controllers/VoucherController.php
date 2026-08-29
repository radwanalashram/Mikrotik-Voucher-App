<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VoucherGeneratorService;
use App\Models\Voucher;
use RouterOS\Client;
use Barryvdh\DomPDF\Facade\Pdf;

class VoucherController extends Controller
{
    public function create()
    {
        $vouchers = Voucher::latest()->take(30)->get();
        return view('vouchers.create', compact('vouchers'));
    }

    public function store(Request $request, VoucherGeneratorService $generatorService)
    {
        $request->validate([
            'middle_char' => 'required|string|in:H,D,W,M',
            'count'       => 'required|integer|min:1|max:500',
        ]);

        // Validate Mikrotik config
        $host = config('mikrotik.host');
        $user = config('mikrotik.user');
        $pass = config('mikrotik.pass');
        $port = config('mikrotik.port');

        if (empty($host) || empty($user) || $pass === null) {
            return redirect()->back()->with('error', 'إعدادات Mikrotik غير مكتملة. تحقق من config/mikrotik.php وملف .env.');
        }

        try {
            $client = new Client([
                'host' => $host,
                'user' => $user,
                'pass' => $pass,
                'port' => $port,
            ]);

            $newVouchers = $generatorService->generateAndSave(
                $client,
                $request->input('middle_char'),
                $request->input('count')
            );

            // Store generated vouchers in session so exportPdf can use them
            session()->put('vouchers', $newVouchers);

            return redirect()->back()->with([
                'success'  => "تم إنشاؤها وحفظها بنجاح.",
                'vouchers' => $newVouchers
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'فشل التنفيذ: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $vouchers = session('vouchers', []);
        if (empty($vouchers)) {
            return redirect()->back()->with('error', 'لا توجد كروت متاحة للتصدير.');
        }

        $pdf = Pdf::loadView('vouchers.pdf', compact('vouchers'));
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait');

        return $pdf->stream('vouchers-' . time() . '.pdf');
    }
}
