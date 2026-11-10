@extends('dashboard.layouts.app')
@section('title', 'تقرير أوقات الذروة')
@section('css')
<style>
    .peak-card { background:#fff; border-radius:.5rem; padding:1.25rem; height:100%; }
    .peak-kpi { font-size:2rem; font-weight:700; line-height:1.2; }
    .peak-kpi.is-range { font-size:1.4rem; }
    .peak-kpi small { font-size:.85rem; font-weight:400; color:#6b7280; }
    /* عزل النطاق الزمني عن اتجاه الصفحة حتى لا تنقلب الشرطة بين الرقمين */
    .ltr-num { direction:ltr; unicode-bidi:isolate; display:inline-block; white-space:nowrap; }

    /* أعمدة التوزيع */
    .bar-row { display:flex; align-items:center; gap:.5rem; margin-bottom:.35rem; }
    .bar-label { width:92px; flex:none; font-size:.82rem; color:#1f2937; text-align:right; line-height:1.25; }
    .bar-label .h24-dim { display:block; font-size:.68rem; color:#9ca3af; direction:ltr; unicode-bidi:isolate; }
    .bar-track { flex:1; background:#f1f3f5; border-radius:.25rem; height:22px; position:relative; overflow:hidden; display:block; }
    /* display:block ضروري: العنصر span سطري ويتجاهل width/height */
    .bar-fill { display:block; height:100%; min-width:2px; border-radius:.25rem;
                background:linear-gradient(90deg,#60a5fa,#2563eb); }
    .bar-fill.is-peak { background:linear-gradient(90deg,#f87171,#dc2626); }
    .bar-value { width:96px; flex:none; font-size:.8rem; color:#374151; }

    /* الخريطة الحرارية */
    .heat-wrap { overflow-x:auto; }
    .heat { border-collapse:separate; border-spacing:2px; }
    .heat th { font-size:.65rem; color:#6b7280; font-weight:500; padding:2px; text-align:center; white-space:nowrap; }
    .heat td { width:26px; height:24px; text-align:center; font-size:.68rem; border-radius:3px;
               color:#1f2937; background:#f1f3f5; }
    .heat td.v1{background:#dbeafe}.heat td.v2{background:#bfdbfe}.heat td.v3{background:#93c5fd}
    .heat td.v4{background:#60a5fa;color:#fff}.heat td.v5{background:#2563eb;color:#fff}
    .heat-legend { display:flex; align-items:center; gap:.35rem; font-size:.75rem; color:#6b7280; }
    .heat-legend i { width:18px; height:14px; border-radius:3px; display:inline-block; }
    .closed-note { font-size:.8rem; color:#6b7280; }

    /* بطاقات فترات اليوم */
    .block-card { border:1px solid #e5e7eb; border-radius:.4rem; padding:.6rem .5rem; text-align:center; height:100%; }
    .block-card.is-peak { border-color:#dc2626; background:#fef2f2; }
    .block-range { font-size:.78rem; color:#4b5563; margin-bottom:.25rem; }
    .block-count { font-size:1.35rem; font-weight:700; color:#1f2937; line-height:1.1; }
    .block-card.is-peak .block-count { color:#dc2626; }
    .block-pct { font-size:.75rem; color:#6b7280; }
    .block-table { table-layout:fixed; width:100%; }
    .block-table th, .block-table td { text-align:center; font-size:.78rem; padding:.3rem .15rem; vertical-align:middle; }
    /* الأرقام لا تنكسر أبدًا، بينما يُسمح لعناوين الفترات بالالتفاف على سطرين */
    .block-table td, .block-table tbody th { white-space:nowrap; }
    .block-table thead th { font-size:.7rem; color:#4b5563; white-space:normal; line-height:1.3; word-break:keep-all; }
    .block-table th:first-child, .block-table td:first-child { width:14%; }
    .block-table th:last-child,  .block-table td:last-child  { width:14%; }
    /* "اليوم" و"الإجمالي" كلمتان لا يصح كسرهما */
    .block-table thead th:first-child, .block-table thead th:last-child { white-space:nowrap; }
    .block-table td.v1{background:#dbeafe}.block-table td.v2{background:#bfdbfe}.block-table td.v3{background:#93c5fd}
    .block-table td.v4{background:#60a5fa;color:#fff}.block-table td.v5{background:#2563eb;color:#fff}
</style>
@endsection
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="mb-2 content-header-left col-12 breadcrumb-new">
                <h3 class="mb-0 content-header-title d-inline-block">تقرير أوقات الذروة</h3>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}">الفواتير</a></li>
                            <li class="breadcrumb-item active">أوقات الذروة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">

            {{-- فلتر المدة --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="get" action="{{ route('dashboard.invoices.peak-times') }}"
                                  class="flex-wrap d-flex align-items-end" style="gap:1rem">
                                <div class="form-group mb-0">
                                    <label>من تاريخ</label>
                                    <input type="date" name="from_date" value="{{ $from }}" class="form-control">
                                </div>
                                <div class="form-group mb-0">
                                    <label>الي تاريخ</label>
                                    <input type="date" name="to_date" value="{{ $to }}" class="form-control">
                                </div>
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-primary btn-sm">عرض التقرير</button>
                                </div>
                                @if ($from || $to)
                                    <div class="form-group mb-0">
                                        <a href="{{ route('dashboard.invoices.peak-times') }}"
                                           class="btn btn-secondary btn-sm">كل الفترات</a>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if ($total == 0)
                <div class="alert alert-warning">لا توجد فواتير في هذه الفترة.</div>
            @else

            {{-- مؤشرات مختصرة --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 mb-2">
                    <div class="card mb-0"><div class="card-body peak-card">
                        <div class="peak-kpi text-danger">{{ $hour_labels[$peak_hour]['long'] }}</div>
                        <div>أكثر ساعة ازدحامًا <span class="text-muted">({{ $hour_labels[$peak_hour]['h24'] }})</span></div>
                        <small class="text-muted">{{ number_format($peak_hour_count) }} فاتورة
                            ({{ round($peak_hour_count / $total * 100, 1) }}%)</small>
                    </div></div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-2">
                    <div class="card mb-0"><div class="card-body peak-card">
                        <div class="peak-kpi is-range text-primary">
                            {{ $hour_labels[$best_window['start']]['short'] }} &rarr; {{ $hour_labels[($best_window['start'] + 3) % 24]['short'] }}
                        </div>
                        <div>أزحم ٣ ساعات متصلة</div>
                        <small class="text-muted">{{ number_format($best_window['count']) }} فاتورة
                            ({{ round($best_window['count'] / $total * 100, 1) }}%)</small>
                    </div></div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-2">
                    <div class="card mb-0"><div class="card-body peak-card">
                        <div class="peak-kpi text-success">{{ $days_ar[$peak_day] }}</div>
                        <div>أكثر يوم ازدحامًا</div>
                        <small class="text-muted">{{ number_format($peak_day_count) }} فاتورة</small>
                    </div></div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-2">
                    <div class="card mb-0"><div class="card-body peak-card">
                        <div class="peak-kpi">{{ $avg_per_day }}</div>
                        <div>متوسط الفواتير يوميًا</div>
                        <small class="text-muted">{{ number_format($total) }} فاتورة على {{ $working_days }} يوم عمل</small>
                    </div></div>
                </div>
            </div>

            {{-- فترات اليوم: كل 4 ساعات --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">التوزيع حسب فترات اليوم (كل ٤ ساعات)</h4>
                        </div>
                        <div class="card-body">
                            @php $max_block = collect($blocks)->max('count'); @endphp

                            {{-- بطاقات الفترات الست --}}
                            <div class="row mb-2">
                                @foreach ($blocks as $b)
                                    <div class="col-lg-2 col-md-4 col-6 mb-2">
                                        <div class="block-card {{ $peak_block && $b['start'] == $peak_block['start'] ? 'is-peak' : '' }}">
                                            <div class="block-range">{{ $b['short'] }}</div>
                                            <div class="block-count">{{ number_format($b['count']) }}</div>
                                            <div class="block-pct">{{ $b['pct'] }}%</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- أعمدة الفترات --}}
                            @foreach ($blocks as $b)
                                <div class="bar-row">
                                    <span class="bar-label" style="width:170px">{{ $b['name'] }}</span>
                                    <span class="bar-track">
                                        @if ($b['count'] > 0)
                                            <span class="bar-fill {{ $peak_block && $b['start'] == $peak_block['start'] ? 'is-peak' : '' }}"
                                                  style="width: {{ $max_block ? round($b['count'] / $max_block * 100, 2) : 0 }}%"></span>
                                        @endif
                                    </span>
                                    <span class="bar-value">
                                        {{ number_format($b['count']) }}
                                        <span class="text-muted">({{ $b['pct'] }}%)</span>
                                    </span>
                                </div>
                            @endforeach

                            {{-- الفترات لكل يوم --}}
                            <h5 class="mt-3 mb-1" style="font-size:.95rem">الفترات حسب أيام الأسبوع</h5>
                            <div class="heat-wrap">
                                <table class="table table-sm table-bordered block-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>اليوم</th>
                                            @foreach ($blocks as $b)
                                                <th title="{{ $b['name'] }}">{{ $b['short'] }}</th>
                                            @endforeach
                                            <th>الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $max_cell_block = 0;
                                             foreach ($blocks_by_day as $r) { $max_cell_block = max($max_cell_block, max($r)); } @endphp
                                        @foreach ($days_ar as $num => $name)
                                            <tr>
                                                <th>{{ $name }}</th>
                                                @foreach ($blocks as $b)
                                                    @php
                                                        $v = $blocks_by_day[$num][$b['start']];
                                                        $lvl = $v == 0 ? 0 : (int) ceil($v / max($max_cell_block, 1) * 5);
                                                    @endphp
                                                    <td class="v{{ $lvl }}" title="{{ $name }} — {{ $b['name'] }}">{{ $v ?: '—' }}</td>
                                                @endforeach
                                                <td><strong>{{ number_format($weekly[$num]) }}</strong></td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th>الإجمالي</th>
                                            @foreach ($blocks as $b)
                                                <td><strong>{{ number_format($b['count']) }}</strong></td>
                                            @endforeach
                                            <td><strong>{{ number_format($total) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- التوزيع حسب الساعة --}}
            <div class="row">
                <div class="col-lg-7 col-12">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">التوزيع حسب ساعة اليوم</h4></div>
                        <div class="card-body">
                            @php $max_hour = max($hourly); @endphp
                            @for ($h = 0; $h < 24; $h++)
                                <div class="bar-row">
                                    <span class="bar-label">
                                        {{ $hour_labels[$h]['short'] }}
                                        <span class="h24-dim">{{ $hour_labels[$h]['h24'] }}</span>
                                    </span>
                                    <span class="bar-track">
                                        @if ($hourly[$h] > 0)
                                            <span class="bar-fill {{ $h == $peak_hour ? 'is-peak' : '' }}"
                                                  style="width: {{ $max_hour ? round($hourly[$h] / $max_hour * 100, 2) : 0 }}%"></span>
                                        @endif
                                    </span>
                                    <span class="bar-value">
                                        {{ number_format($hourly[$h]) }}
                                        <span class="text-muted">({{ round($hourly[$h] / $total * 100, 1) }}%)</span>
                                    </span>
                                </div>
                            @endfor
                            @php $closed = collect(range(0,23))->filter(fn($h) => $hourly[$h] == 0); @endphp
                            @if ($closed->count())
                                <p class="mt-2 mb-0 closed-note">
                                    لم تُسجَّل أي فاتورة في الساعات:
                                    {{ $closed->map(fn($h) => $hour_labels[$h]['short'])->implode('، ') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- التوزيع حسب اليوم + أزحم التواريخ --}}
                <div class="col-lg-5 col-12">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">التوزيع حسب يوم الأسبوع</h4></div>
                        <div class="card-body">
                            @php $max_day = max($weekly); @endphp
                            @foreach ($days_ar as $num => $name)
                                <div class="bar-row">
                                    <span class="bar-label" style="text-align:right">{{ $name }}</span>
                                    <span class="bar-track">
                                        @if ($weekly[$num] > 0)
                                            <span class="bar-fill {{ $num == $peak_day ? 'is-peak' : '' }}"
                                                  style="width: {{ $max_day ? round($weekly[$num] / $max_day * 100, 2) : 0 }}%"></span>
                                        @endif
                                    </span>
                                    <span class="bar-value">
                                        {{ number_format($weekly[$num]) }}
                                        <span class="text-muted">({{ round($weekly[$num] / $total * 100, 1) }}%)</span>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h4 class="card-title">أزحم الأيام فعليًا</h4></div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>التاريخ</th><th>اليوم</th><th>عدد الفواتير</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($busiest_dates as $row)
                                        <tr>
                                            <td>{{ $row->the_date }}</td>
                                            <td>{{ $days_ar[(int) date('w', strtotime($row->the_date)) + 1] }}</td>
                                            <td><strong>{{ $row->total }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الخريطة الحرارية --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <h4 class="card-title mb-0">خريطة الضغط: اليوم × الساعة</h4>
                            <div class="heat-legend">
                                <span>أقل</span>
                                <i style="background:#f1f3f5"></i><i style="background:#dbeafe"></i>
                                <i style="background:#bfdbfe"></i><i style="background:#93c5fd"></i>
                                <i style="background:#60a5fa"></i><i style="background:#2563eb"></i>
                                <span>أكثر</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @php $max_cell = 0; foreach ($heatmap as $r) { $max_cell = max($max_cell, max($r)); } @endphp
                            <div class="heat-wrap">
                                <table class="heat">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            @for ($h = 0; $h < 24; $h++)
                                                <th title="{{ $hour_labels[$h]['long'] }}">{{ $hour_labels[$h]['short'] }}</th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($days_ar as $num => $name)
                                            <tr>
                                                <th style="text-align:right">{{ $name }}</th>
                                                @for ($h = 0; $h < 24; $h++)
                                                    @php
                                                        $v = $heatmap[$num][$h];
                                                        $lvl = $v == 0 ? 0 : (int) ceil($v / max($max_cell, 1) * 5);
                                                    @endphp
                                                    <td class="v{{ $lvl }}"
                                                        title="{{ $name }} — {{ $hour_labels[$h]['long'] }} ({{ $hour_labels[$h]['h24'] }}) — {{ $v }} فاتورة">
                                                        {{ $v ?: '' }}
                                                    </td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-2 mb-0 closed-note">
                                كل خانة تمثل عدد الفواتير المُدخلة في ذلك اليوم وتلك الساعة. (ص = صباحًا، م = مساءً — فـ &rlm;12 ص هي منتصف الليل و&rlm;12 م هي الظهر). مرّر المؤشر فوق الخانة لعرض التفاصيل.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @endif
        </div>
    </div>
</div>
@endsection
