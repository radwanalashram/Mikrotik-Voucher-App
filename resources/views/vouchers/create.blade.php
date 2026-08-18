<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>توليد وطباعة الكروت</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: 80mm auto; margin: 0; }
            body { width: 80mm; margin: 0; background: #fff !important; font-family: monospace; }
            .no-print { display: none !important; }
            .thermal-card { width: 100% !important; border-bottom: 2px dashed #000 !important; padding: 10px 0 !important; text-align: center !important; }
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow no-print mb-6">
        <h2 class="text-xl font-bold mb-4">توليد كروت جديدة</h2>
        <form action="{{ route('vouchers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label>الفئة:</label>
                <select name="middle_char" class="w-full border p-2 rounded">
                    <option value="H">ساعات (H)</option>
                    <option value="D">يومي (D)</option>
                    <option value="W">أسبوعي (W)</option>
                    <option value="M">شهري (M)</option>
                </select>
	            </div>
            <div>
                <label>العدد:</label>
                <input type="number" name="count" value="10" class="w-full border p-2 rounded">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">توليد الكروت</button>
        </form>
    </div>

    @if(session('vouchers'))
        <div class="max-w-sm mx-auto">
            <div class="flex justify-between mb-4 no-print">
                <button onclick="window.print()" class="bg-black text-white px-4 py-2 rounded">طباعة حرارية 80mm</button>
                <a href="{{ route('vouchers.exportPdf') }}" target="_blank" class="bg-red-600 text-white px-4 py-2 rounded">تصدير PDF</a>
            </div>
            @foreach(session('vouchers') as $voucher)
                <div class="thermal-card border-2 border-black border-dashed p-4 mb-3 text-center bg-white">
                    <div class="font-bold border-b border-black pb-1">شبكة الإنترنت المحلية</div>
                    <div class="text-2xl font-black my-2 tracking-wider">{{ $voucher['code'] }}</div>
                    <div class="text-xs border-t border-black pt-1">الفئة: {{ $voucher['profile'] }}</div>
                </div>
            @endforeach
        </div>
    @endif
</body>
</html>
