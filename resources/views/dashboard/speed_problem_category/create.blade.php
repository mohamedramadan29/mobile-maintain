<!-- Modal -->
<div class="text-left modal fade" id="add_problem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel10">
                    اضافة قسم جديد </h4>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('dashboard.speed_problem_categories.create') }}" method="POST">
                    @csrf
                    <div>
                        <label> الاسم </label>
                        <input type="text" class="form-control" name="name" value="">
                    </div>
                    <div>
                        <label> عدد الدقائق للاصلاح  </label>
                        <input type="number" min="1" class="form-control" name="solved_time" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">رجوع
                        </button>
                        <button type="submit" class="btn btn-outline-primary"> اضافة </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
