<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>طباعة باركود الفاتورة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Zain', sans-serif;
            direction: rtl;
            text-align: right;
            padding: 20px;
        }

        .btns {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="btns">
        <a href="{{ route('dashboard.invoices.index') }}" class="btn btn-secondary">رجوع إلى الفواتير</a>
        <a href="{{ url('dashboard/invoice/print-barcode/' . $invoice->id) }}" class="btn btn-primary" target="_blank">
            طباعة الباركود
        </a>
    </div>

    <h4>فاتورة رقم: {{ $invoice->id }}</h4>
    <p>الاسم: {{ $invoice->name }}</p>
    <p>الهاتف: {{ $invoice->phone }}</p>
    <img src="data:image/png;base64,{{ $qrCodeBase64 }}" style="width:100px; height:36px">

</body>

</html>
