<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>PDF Vouchers</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; text-align: center; direction: rtl; }
        .card { border-bottom: 2px dashed #000; padding: 10px 0; margin-bottom: 10px; }
        .code { font-size: 20px; font-weight: bold; margin: 5px 0; }
    </style>
</head>
<body>
    @foreach($vouchers as $voucher)
        <div class="card">
            <div>شبكة الإنترنت المحلية</div>
            <div class="code">{{ $voucher['code'] }}</div>
            <div>الفئة: {{ $voucher['profile'] }}</div>
        </div>
    @endforeach
</body>
</html>
