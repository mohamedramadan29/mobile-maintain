<html>

<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Zain:ital,wght@0,200;0,300;0,400;0,700;0,800;0,900;1,300;1,400&display=swap');

        body {
            font-family: 'Zain', sans-serif;
            text-align: right;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="back-button">
        <a href="{{ route('invoices.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-right"></i> رجوع للفواتير
        </a>
    </div>
    <div class="text-center">

        <table class="table" style="margin-bottom: 1px;padding: 0px">
            <tbody style="padding: 0;margin: 0">
                <tr style="background-color: #000">
                    <td>
                        <img width="15px" src="{{ asset('assets/admin/') }}/images/logo_mobile.png" alt="">
                    </td>
                    <td style="text-align: right">
                        <h4 class="invoice_header"> Ticket : T - {{ $invoice->id }}</h4>
                        <span style="font-size: 10px;color:#fff"> {{ $invoice->created_at->format('d-m-Y') }} </span>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="invoice_title"> {{ $invoice->title }}</p>
        <p class="problems">
            @foreach (json_decode($invoice->problems) as $problem)
                / <span class="badge badge-danger"> {{ $problem }}
                </span>
            @endforeach
        </p>
        <div class="barcode_users">
            <table class="table" style="border-collapse: collapse; width: 100%;">
                <tbody>
                    <tr>
                        <td style="text-align: center; border: 1px solid #000;">
                            <p>{{ $invoice->name }}</p>
                            <p>{{ $invoice->phone }}</p>
                        </td>
                        <td style="text-align: right; margin: auto; border: 1px solid #000;">
                            <img src="data:image/png;base64,{{ $qrCodeBase64 }}" style="width: 150px; height: 30px; object-fit: contain;">
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

    </div>
    <style>
        .invoice_header {
            margin: 0px;
            padding: 2px;
            background-color: #000;
            color: #fff;
            margin-bottom: 2px;
            font-size: 10px
        }

        .table .data {
            border: 1px solid #000;
            text-align: center;
        }

        .invoice_title {
            margin: 0;
            padding: 2px;
            background-color: #000;
            color: #fff;
            font-size: 10px
        }

        .problems {
            margin: 0;
            padding: 2px;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .barcode_users {
            text-align: center;
            width: 100%;
        }




        .barcode_users .user_info p {
            font-size: 10px;
            margin: 0;
            padding: 2px;
        }

        .back-button {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
