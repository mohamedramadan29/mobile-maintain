@extends('dashboard.layouts.public_app')
@section('title', ' الشروط والاحكام ')
@section('content')
    @php
        $setting = \App\Models\dashboard\Setting::first();
    @endphp
    <div class="app-content content" style="margin-right:0px">
        <div class="content-wrapper">
            <div class="content-body">
                <section class="card">
                    <div id="invoice-template" class="card-body">
                        <h1 class="text-center"> الشروط والأحكام </h1>
                        {!! $setting->terms !!}
                        <hr>
                        <h5 class="text-center"> نشكر ثقتكم بنا ونتطلع إلى الأفضل، اقتراحاتكم وملاحظاتكم تهمنا! </h5>
                        <p><strong>للملاحظات:</strong> التواصل على الرقم: {{ $setting->phone2 }}</p>
                        <p><strong>للاستفسارات أو متابعة حالة الصيانة:</strong> قسم الصيانة: {{ $setting->phone1 }}</p>
                    </div>
                </section>

            </div>
        </div>
    </div>

@endsection
