<!-- Modal -->
<div class="text-left modal fade" id="delete_problem_{{ $problem->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger white">
                <h4 class="modal-title white" id="myModalLabel10">
                    <i class="la la-trash"></i> حذف القسم
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dashboard.programe_problem_categories.destroy', $problem->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <h5 class="text-danger mb-2">
                            <i class="la la-exclamation-triangle"></i> هل انت متاكد من حذف القسم ؟
                        </h5>
                        <label>الاسم</label>
                        <input type="text" class="form-control bg-light" value="{{ $problem->name }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>عدد الدقائق للاصلاح</label>
                        <input type="text" class="form-control bg-light" value="{{ $problem->solved_time }}" disabled>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">
                            <i class="la la-times"></i> الغاء
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="la la-trash"></i> تأكيد الحذف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
