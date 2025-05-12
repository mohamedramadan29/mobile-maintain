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
                <form action="{{ route('dashboard.programe_problem_categories.create') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="required"> الاسم </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                            name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="required"> عدد الدقائق للاصلاح </label>
                        <input type="number" min="1" class="form-control @error('solved_time') is-invalid @enderror" 
                            name="solved_time" value="{{ old('solved_time') }}" required>
                        @error('solved_time')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">
                            <i class="la la-times"></i> رجوع
                        </button>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="la la-check"></i> اضافة
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
