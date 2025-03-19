<!-- Modal -->
<div class="text-left modal fade" id="update_tech_{{ $admin->id }}" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel10" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white" id="myModalLabel10">
                    تعديل صلاحيات الفني </h4>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="{{ route('dashboard.admins.update_tech', $admin->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label> عدد الاجهزة المسموح بها في نفس الوقت </label>
                        <input type="number" min='1' max="10" class="form-control" name="device_nums"
                            value="{{ $admin->device_nums }}">
                    </div>
                    <div class="form-group">
                        <label for="title"> الاعطال المتاحة للفني </label>
                        <div class="skin skin-square">
                            <div class="col-md-12 col-sm-12 d-flex flex-column">
                                @php
                                    // تأكد من أن $admin->problems يتم تحويله إلى مصفوفة صالحة أو اجعل القيمة فارغة افتراضيًا
                                    $admin_problems = json_decode($admin->problems, true) ?: [];
                                @endphp
                                <p> صلاحيات اعطال فحص شامل </p>
                                @foreach ($problems as $problem)
                                    <fieldset>
                                        <input {{ in_array($problem->name, $admin_problems) ? 'checked' : '' }}
                                            type="checkbox" id="input-{{ $problem->id }}" name="problems[]"
                                            value="{{ $problem->name }}">
                                        <label for="input-{{ $problem->id }}">
                                            {{ $problem->name }}
                                        </label>
                                    </fieldset>
                                @endforeach
                                <p> صلاحيات اعطال جهاز برمجة </p>
                                @foreach ($programe_problems as $programe_problem)
                                    <fieldset>
                                        <input {{ in_array($programe_problem->name, $admin_problems) ? 'checked' : '' }}
                                            type="checkbox" id="inputprograme-{{ $programe_problem->id }}"
                                            name="problems[]" value="{{ $programe_problem->name }}">
                                        <label for="inputprograme-{{ $programe_problem->id }}">
                                            {{ $programe_problem->name }}
                                        </label>
                                    </fieldset>
                                @endforeach

                                <p> صلاحيات اعطال جهاز سريع </p>

                                @foreach ($speed_problems as $speed_problem)
                                    <fieldset>
                                        <input {{ in_array($speed_problem->name, $admin_problems) ? 'checked' : '' }}
                                            type="checkbox" id="inputspeed-{{ $speed_problem->id }}" name="problems[]"
                                            value="{{ $speed_problem->name }}">
                                        <label for="inputspeed-{{ $speed_problem->id }}">
                                            {{ $speed_problem->name }}
                                        </label>
                                    </fieldset>
                                @endforeach


                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">رجوع
                        </button>
                        <button type="submit" class="btn btn-primary"> تعديل </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
