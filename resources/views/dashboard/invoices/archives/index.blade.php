@extends('dashboard.layouts.app')

@section('title', 'أرشيف الفواتير')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block">أرشيف الفواتير</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}">الفواتير</a></li>
                                <li class="breadcrumb-item active">الأرشيف</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">الفواتير المؤرشفة</h4>
                                <div class="heading-elements">
                                    <ul class="mb-0 list-inline">
                                        <li>
                                            <a href="{{ route('dashboard.invoices.index') }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ft-arrow-left"></i> العودة للفواتير
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <!-- Search and Filter Form -->
                                    <form method="GET" action="{{ route('dashboard.invoices.archives.index') }}" class="mb-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="text" name="search" class="form-control"
                                                       placeholder="البحث بالاسم، الهاتف، أو رقم الفاتورة"
                                                       value="{{ request('search') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" name="date_from" class="form-control"
                                                       placeholder="من تاريخ" value="{{ request('date_from') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" name="date_to" class="form-control"
                                                       placeholder="إلى تاريخ" value="{{ request('date_to') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="ft-search"></i> بحث
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Archives Table -->
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>رقم الفاتورة</th>
                                                    <th>اسم العميل</th>
                                                    <th>الهاتف</th>
                                                    <th>تاريخ الأرشفة</th>
                                                    <th>الأرشيف بواسطة</th>
                                                    <th>السبب</th>
                                                    <th>الحالة</th>
                                                    <th>الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($archives as $archive)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="{{ route('dashboard.invoices.show-details', $archive->invoice->id) }}"
                                                               class="btn btn-sm btn-outline-primary">
                                                                #{{ $archive->invoice->id }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $archive->invoice->name }}</td>
                                                        <td>{{ $archive->invoice->phone }}</td>
                                                        <td>{{ $archive->archive_date->format('d-m-Y') }}</td>
                                                        <td>{{ $archive->archivedBy->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge badge-info">
                                                                {{ $archive->reason ?? 'بدون سبب' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($archive->status == 'archived')
                                                                <span class="badge badge-warning">مؤرشفة</span>
                                                            @else
                                                                <span class="badge badge-success">مسترجعة</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                @if($archive->status == 'archived')
                                                                    <button type="button"
                                                                            class="btn btn-success"
                                                                            onclick="confirmRestore({{ $archive->id }})">
                                                                        <i class="ft-refresh-ccw"></i> استرجاع
                                                                    </button>
                                                                @endif
                                                                <button type="button"
                                                                        class="btn btn-danger"
                                                                        onclick="confirmDelete({{ $archive->id }})">
                                                                    <i class="ft-trash-2"></i> حذف
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center">
                                                            <div class="alert alert-info">
                                                                <i class="ft-info"></i> لا توجد فواتير مؤرشفة
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    @if($archives->hasPages())
                                        <div class="d-flex justify-content-center">
                                            {{ $archives->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تأكيد الاسترجاع</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من استرجاع هذه الفاتورة من الأرشيف؟</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <form id="restoreForm" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success">استرجاع</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تأكيد الحذف</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف هذه الفاتورة من الأرشيف نهائياً؟</p>
                    <p class="text-danger"><strong>هذا الإجراء لا يمكن التراجع عنه!</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
function confirmRestore(id) {
    document.getElementById('restoreForm').action = '{{ route("dashboard.invoices.archives.restore", ":id") }}'.replace(':id', id);
    $('#restoreModal').modal('show');
}

function confirmDelete(id) {
    document.getElementById('deleteForm').action = '{{ route("dashboard.invoices.archives.destroy", ":id") }}'.replace(':id', id);
    $('#deleteModal').modal('show');
}
</script>
@endsection
