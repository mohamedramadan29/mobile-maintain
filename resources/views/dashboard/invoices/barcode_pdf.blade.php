 <!DOCTYPE html>
 <html>

 <head>
     <style>
         @import url('https://fonts.googleapis.com/css2?family=Zain:ital,wght@0,200;0,300;0,400;0,700;0,800;0,900;1,300;1,400&display=swap');

         body {
             font-family: 'Zain', sans-serif;
             text-align: right;
         }

         /* التأكد من ظهور الرابط في العرض العادي */
         .print-hidden {
             display: block;
         }

         /* إخفاء الرابط عند الطباعة فقط */
         @media print {
             .print-hidden {
                 display: none !important;
             }
         }
     </style>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
         integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
 </head>

 <body>
     <div class="text-center">
         <table class="table" style="margin-bottom: 1px;padding: 0px">
             <tbody style="padding: 0;margin: 0">
                 <tr style="background-color: #000">
                     <td>
                         <img width="15px" src="{{ asset('assets/admin/') }}/images/logo_mobile.png" alt="">
                     </td>
                     <td style="text-align: center">
                         <h4 class="invoice_header"> {{ $invoice->created_at->format('d-m-Y') }} // Ticket :
                             T-{{ $invoice->id }} </h4>

                         <span style="font-size: 5.5px;color:#fff"> {{ $invoice->date_delivery }}
                             {{ date('h:i A', strtotime($invoice->time_delivery)) }}</span>
                     </td>
                 </tr>
             </tbody>
         </table>
         <table class="table" style="margin-bottom: 1px;padding: 0px">
             <tbody style="padding: 0;margin: 0">
                 <tr style="background-color: #000;color:#fff">
                     <td>
                         <p style="font-size: 5.5px;color:#fff;text-align:center">{{ $invoice->name }}</p>
                         <p style="font-size: 5.5px;color:#fff;text-align:center">{{ $invoice->phone }}</p>
                     </td>
                     <td style="text-align: right">
                         <p style="font-size: 5.5px;color:#fff;text-align:center"> {{ $invoice->title }} </p>
                     </td>
                 </tr>
             </tbody>
         </table>
         <p class="problems" style="background-color: #000;color#fff">
             @foreach (json_decode($invoice->problems) as $problem)
                 <span class="badge badge-danger" style="font-size: 5.5px;color:#fff;"> / {{ $problem }} </span>
             @endforeach
         </p>
         <div class="barcode_users">
             <table class="table" style="width: 100%; border-spacing: 0;">
                 <tbody>
                     <tr style="">
                         <td style="text-align: right; font-size: 6px; padding: 2px;">
                             <p style="font-size: 5.5px;text-algin:center"> ملاحظات
                                 الاستقبال:{{ $invoice->description ?? 'لا توجد ملاحظات' }}</p>
                             <p style="font-size: 5.5px;text-align:center"> ملاحظات
                                 الفني:{{ $invoice->tech_notes ?? 'لا توجد ملاحظات' }}</p>
                         </td>
                     </tr>
                 </tbody>
             </table>
         </div>
         <table class="table" style="margin-bottom: 1px;padding-right:10px" dir="rtl">
             <tbody>
                 @foreach ($invoice->priceDetails as $detail)
                     <tr style="background-color: #000;color:#fff;">
                         <td style="font-size: 5.5px;color:#fff;text-align:center">
                             {{ $detail->title }}
                         </td>
                         <td style="font-size: 5.5px;color:#fff">
                             {{ $detail->amount }}
                         </td>
                         <td style="font-size: 5.5px;color:#fff">
                             @foreach ($piece_resources as $resource)
                                 @if ($detail->piece_resource == $resource->id)
                                     {{ $resource->name }}
                                 @endif
                             @endforeach
                         </td>
                     </tr>
                 @endforeach
             </tbody>
         </table>

     </div>
     <style>
         /* إخفاء الرابط عند الطباعة */

         .invoice_header {
             margin: 0px;
             padding: 2px;
             background-color: #000;
             color: #fff;
             margin-bottom: 2px;
             font-size: 6px;
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
             font-size: 6px;
         }

         .problems {
             margin: 0;
             padding: 2px;
             font-size: 6px;
         }

         .text-center {
             text-align: center;
         }

         .barcode_users {
             text-align: center;
             width: 100%;
         }

         .barcode_users .user_info p {
             font-size: 6px;
             margin: 0;
             padding: 2px;
         }
     </style>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
         integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
     </script>
 </body>

 </html>
