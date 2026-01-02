@extends('dashboard.layouts.app')

@section('title', ' تفاصيل وتسليم الجهاز ')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/forms/icheck/icheck.css">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/forms/icheck/custom.css">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/css-rtl/plugins/forms/checkboxes-radios.css">
@endsection
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                <h3 class="mb-0 content-header-title d-inline-block"> تفاصيل وتسليم الجهاز </h3>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}"> الفواتير
                                </a>
                            </li>
                            <li class="breadcrumb-item active"><a href="#"> تفاصيل وتسليم الجهاز </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

        </div>
        <div class="content-body">
            <!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="basic-layout-form"> تفاصيل وتسليم الجهاز </h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <form class="form" method="POST" id='invoice-form'
                                        action="{{ route('dashboard.tech_invoices.update', $invoice->id) }}') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-body">


                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name"> اسم الفني <span class="required_span">
                                                                *
                                                            </span> </label>
                                                        <input disabled type="text" id="name" class="form-control"
                                                            placeholder="" name="name"
                                                            value="{{ $invoice->Technical->name ?? 'غير معروف' }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name"> اسم الاستقبال <span class="required_span">
                                                                *
                                                            </span> </label>
                                                        <input disabled type="text" id="name" class="form-control"
                                                            placeholder="" name="name"
                                                            value="{{ $invoice->Recieved->name ?? 'غير معروف' }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name"> اسم العميل <span class="required_span">
                                                                *
                                                            </span> </label>
                                                        <input disabled type="text" id="name" class="form-control"
                                                            placeholder="" name="name" value="{{ $invoice->name }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="phone"> رقم الهاتف <span class="required_span">
                                                                *
                                                            </span> </label>
                                                        <input disabled type="text" id="phone" class="form-control"
                                                            placeholder="" name="phone" value="{{ $invoice->phone }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="title"> اسم الجهاز <span class="required_span">
                                                                *
                                                            </span> </label>
                                                        <input disabled type="text" id="title" class="form-control"
                                                            placeholder="" name="title" value="{{ $invoice->title }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="title"> الاعطال <span class="required_span">
                                                                * </span> </label>
                                                        <div class="skin skin-square">
                                                            <!-- ########## Start All Check ####################### -->
                                                            <div class="col-md-12 col-sm-12 problem_check_box"
                                                                style="{{ $invoice->checkout_type === 'فحص كامل' ? 'display: flex; flex-wrap: wrap; word-wrap: break-word;' : 'display: none' }}"
                                                                id="problem_all_check">
                                                                @foreach ($problems as $problem)
                                                                <fieldset style="min-width: 120px">
                                                                    <input disabled {{ in_array($problem->name,
                                                                    json_decode($invoice->problems)) ? 'checked' : '' }}
                                                                    type="checkbox"
                                                                    id="input-{{ $problem->id }}"
                                                                    name="problems[]"
                                                                    value="{{ $problem->name }}">
                                                                    <label for="input-{{ $problem->id }}">
                                                                        {{ $problem->name }} </label>
                                                                </fieldset>
                                                                @endforeach
                                                            </div>
                                                            <!-- ############# End All Check ################# -->

                                                            <!-------############# Start Programe Check ##################-------------->
                                                            <div class="col-md-12 col-sm-12 problem_check_box"
                                                                style="{{ $invoice->checkout_type === 'فحص جهاز برمجة' ? 'display: flex; flex-wrap: wrap; word-wrap: break-word;' : 'display: none' }}"
                                                                id="problem_programe_check">
                                                                @foreach ($programe_problems as $programe_problem)
                                                                <fieldset style="min-width: 120px">
                                                                    <input disabled {{ in_array($programe_problem->name,
                                                                    json_decode($invoice->problems)) ? 'checked' : '' }}
                                                                    type="checkbox"
                                                                    id="inputprograme-{{ $programe_problem->id }}"
                                                                    name="problems[]"
                                                                    value="{{ $programe_problem->name }}">
                                                                    <label
                                                                        for="inputprograme-{{ $programe_problem->id }}">
                                                                        {{ $programe_problem->name }} </label>
                                                                </fieldset>
                                                                @endforeach
                                                            </div>
                                                            <!-------############# End  Programe Check ##################-------------->

                                                            <!-------############# Start Programe Check ##################-------------->
                                                            <div class="col-md-12 col-sm-12 problem_check_box"
                                                                style="{{ $invoice->checkout_type === 'فحص جهاز سريع' ? 'display: flex; flex-wrap: wrap; word-wrap: break-word;' : 'display: none' }}"
                                                                id="problem_speed_check">
                                                                @foreach ($speed_problems as $speed_problem)
                                                                <fieldset style="min-width: 120px">
                                                                    <input disabled {{ in_array($speed_problem->name,
                                                                    json_decode($invoice->problems)) ? 'checked' : '' }}
                                                                    type="checkbox"
                                                                    id="inputspeed-{{ $speed_problem->id }}"
                                                                    name="problems[]"
                                                                    value="{{ $speed_problem->name }}">
                                                                    <label for="inputspeed-{{ $speed_problem->id }}">
                                                                        {{ $speed_problem->name }} </label>
                                                                </fieldset>
                                                                @endforeach
                                                            </div>
                                                            <!-------############# End  Programe Check ##################-------------->


                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="title"> ملاحظات </label>
                                                        <textarea disabled name="description" id=""
                                                            class="form-control">{{ $invoice->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="price">السعر <span class="required_span"> *
                                                            </span></label>
                                                        <input readonly type="number" step="0.01" id="price"
                                                            class="form-control" name="price"
                                                            value="{{ $invoice->price }}">
                                                    </div>


                                                    <!-- تفاصيل السعر القديمة -->
                                                    <div id="price-details-wrapper">
                                                        @php $detailIndex = 0; @endphp
                                                        @foreach ($invoice->priceDetails as $detail)
                                                        <div class="mb-2 form-row">
                                                            <input type="hidden"
                                                                name="price_details[{{ $detailIndex }}][id]"
                                                                value="{{ $detail->id }}">
                                                            <div class="col-4">
                                                                <input type="text" disabled
                                                                    name="price_details[{{ $detailIndex }}][title]"
                                                                    class="form-control" placeholder="عنوان التفصيلة"
                                                                    value="{{ $detail->title }}">
                                                            </div>
                                                            <div class="col-4">
                                                                <input type="number" step="0.01" disabled
                                                                    name="price_details[{{ $detailIndex }}][amount]"
                                                                    class="form-control" placeholder="السعر"
                                                                    value="{{ $detail->amount }}" required
                                                                    oninput="updateTotalPrice()">
                                                            </div>
                                                            <div class="col-3">
                                                                <select disabled
                                                                    name="price_details[{{ $detailIndex }}][piece_resource]"
                                                                    class="form-control">
                                                                    @foreach ($piece_resources as $resource)
                                                                    <option @selected($detail->piece_resource ==
                                                                        $resource->id)
                                                                        value="{{ $resource->id }}">
                                                                        {{ $resource->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                        </div>
                                                        @php $detailIndex++; @endphp
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <script>
                                                    let detailIndex = {{ $detailIndex ?? 0 }};

                                                        function addPriceDetail() {
                                                            const wrapper = document.getElementById('price-details-wrapper');
                                                            const detailDiv = document.createElement('div');
                                                            detailDiv.classList.add('form-row', 'mb-2');
                                                            detailDiv.innerHTML = `
                                                                <div class="col-4">
                                                                    <input type="text" name="price_details[${detailIndex}][title]" class="form-control" placeholder="عنوان التفصيلة">
                                                                </div>
                                                                <div class="col-4">
                                                                    <input type="number" step="0.01" name="price_details[${detailIndex}][amount]" class="form-control"
                                                                           placeholder="السعر" required oninput="updateTotalPrice()">
                                                                </div>
                                                                <div class="col-3">
                                                                    <select name="price_details[${detailIndex}][piece_resource]" class="form-control">
                                                                        @foreach ($piece_resources as $resource)
                                                                            <option @selected(old('piece_resource') == $resource->id)
                                                                                value="{{ $resource->id }}">
                                                                                {{ $resource->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-1">
                                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removePriceDetail(this)">-</button>
                                                                </div>
                                                            `;
                                                            wrapper.appendChild(detailDiv);
                                                            detailIndex++;
                                                            updateTotalPrice(); // تحديث المجموع بعد إضافة تفصيلة
                                                        }

                                                        function removePriceDetail(button) {
                                                            button.parentElement.parentElement.remove();
                                                            updateTotalPrice(); // تحديث المجموع بعد إزالة تفصيلة
                                                        }

                                                        function updateTotalPrice() {
                                                            const priceInputs = document.querySelectorAll('input[name*="price_details"][name$="[amount]"]');
                                                            let total = 0;

                                                            priceInputs.forEach(input => {
                                                                const value = parseFloat(input.value);
                                                                if (!isNaN(value)) {
                                                                    total += value;
                                                                }
                                                            });

                                                            const priceField = document.getElementById('price');
                                                            priceField.value = total.toFixed(2); // تحديث حقل السعر الأولي
                                                        }

                                                        // تحديث المجموع عند تحميل الصفحة إذا كانت هناك بيانات محفوظة
                                                        document.addEventListener('DOMContentLoaded', () => {
                                                            updateTotalPrice();
                                                        });
                                                </script>
                                                <div class="col-md-6">
                                                    <label for="price"> تاريخ ووقت التسليم <span class="required_span">
                                                            * </span> </label>
                                                    <div class="justify-between d-flex flex-column">
                                                        <div class="form-group" style="min-width: 100%">

                                                            <input disabled type="date" name="date_delivery"
                                                                class="form-control"
                                                                value="{{ $invoice->date_delivery }}"
                                                                class="form-control">
                                                        </div>
                                                        <div class="form-group" style="min-width: 100%">
                                                            <input disabled type="time" name="time_delivery"
                                                                value="{{ $invoice->time_delivery }}"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="title"> ملاحظات فني الصيانة </label>
                                                        <textarea disabled name="tech_notes" id=""
                                                            class="form-control">{{ $invoice->tech_notes }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="price"> حالة الجهاز </label>
                                                        <select disabled name="status" id="" class="form-control">
                                                            <option {{ $invoice->status == 'تحت الصيانة' ? 'selected' :
                                                                '' }}
                                                                value="تحت الصيانة">تحت الصيانة</option>
                                                            <option {{ $invoice->status == 'تم الاصلاح' ? 'selected' :
                                                                '' }}
                                                                value="تم الاصلاح"> تم الاصلاح </option>
                                                            <option {{ $invoice->status == 'لم يتم الاصلاح' ? 'selected'
                                                                : '' }}
                                                                value="لم يتم الاصلاح">لم يتم الاصلاح</option>
                                                            <option {{ $invoice->status == 'معلق' ? 'selected' : '' }}
                                                                value="معلق">معلق</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="basic-layout-form"> المرفقات </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>
                                                    المرفق
                                                </th>
                                                <th>
                                                    عنوان المرفق
                                                </th>
                                                <th>
                                                    السعر
                                                </th>
                                                <th>
                                                    تفاصيل اضافية
                                                </th>
                                            </tr>
                                            @forelse ($invoice->files as $file)
                                            @if ($file->price > 0)
                                            <tr>
                                                <td>
                                                    <a target="_blank"
                                                        href="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}">
                                                        <img width="100" height="100" class="file_image"
                                                            src="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                            alt="Card image cap">
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $file->title }}
                                                </td>
                                                <td>
                                                    {{ number_format($file->price, 2) }} ريال
                                                </td>
                                                <td>
                                                    {{ $file->description }}
                                                </td>

                                            </tr>
                                            @endif
                                            @empty
                                            لا يوجد مرفقات
                                            @endforelse
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="basic-layout-form"> صور حالة الجهاز </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>
                                                    المرفق
                                                </th>
                                                <th>
                                                    عنوان المرفق
                                                </th>
                                                <th>
                                                    تفاصيل اضافية
                                                </th>
                                            </tr>
                                            <tbody>
                                                @forelse ($invoice->files as $file)
                                                @if ($file->price == 0)
                                                <tr>
                                                    <td>
                                                        <a target="_blank"
                                                            href="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}">
                                                            <img width="100" height="100" class="file_image"
                                                                src="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                                alt="Card image cap">
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {{ $file->title }}
                                                    </td>
                                                    <td>
                                                        {{ $file->description }}
                                                    </td>

                                                </tr>
                                                @endif

                                                @empty
                                                لا يوجد صور لحالة الجهاز
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <form id="delivery_form"
                                        action="{{ route('dashboard.invoices.delivery', $invoice->id) }}" method="POST">
                                        @csrf
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary" id="delivery_button">
                                                تسليم الجهاز
                                            </button>
                                            <div id="loadingMessage" class="spinner-border text-primary" role="status"
                                                style="display: none;">
                                                <span class="sr-only"> جاري تسليم الجهاز </span>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.getElementById("delivery_form").addEventListener("submit", function(e) {
                                e.preventDefault();
                                let submitBtn = this.querySelector('button[type="submit"]');
                                let loadingMessage = document.getElementById('loadingMessage');

                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<i class="la la-spinner la-spin"></i> جاري الحفظ...';

                                loadingMessage.style.display = 'block';
                                this.submit();

                            });
                        </script>
                    </div>

                </div>
            </section>
            <!-- // Basic form layout section end -->
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="{{ asset('assets/admin/') }}/vendors/js/forms/icheck/icheck.min.js" type="text/javascript"></script>
<script src="{{ asset('assets/admin/') }}/js/scripts/forms/checkbox-radio.js" type="text/javascript"></script>

@endsection
