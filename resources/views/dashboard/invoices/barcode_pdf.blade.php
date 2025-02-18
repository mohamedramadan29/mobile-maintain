<html>

<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Zain:ital,wght@0,200;0,300;0,400;0,700;0,800;0,900;1,300;1,400&display=swap');
        body {
            font-family: "Zain", serif;
            text-align: right;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="text-center">
        <h4 class="invoice_header">فاتورة  : {{ $invoice->id }}</h4>
        <p class="invoice_title"> {{ $invoice->title }}</p>
        <p class="problems">
            @foreach (json_decode($invoice->problems) as $problem)
                / <span class="badge badge-danger"> {{ $problem }}
                </span>
            @endforeach
        </p>
        <div class="barcode_users">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <div class="data">
                                <p> {{ $invoice->name }} </p>
                                <p> {{ $invoice->phone }} </p>
                            </div>
                        </td>
                        <td>
                            <img src="data:image/png;base64,{{ base64_encode($barcode) }}" />
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


        .barcode_users .user_info {
            border: 1px solid #000;
        }

        .barcode_users .user_info p {
            font-size: 10px;
            margin: 0;
            padding: 2px;
        }
    </style>
</body>

</html>
