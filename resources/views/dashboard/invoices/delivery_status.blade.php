<!-- Modal -->
<div class="text-left modal fade" id="delivery_invoice_{{ $invoice->id }}" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel10" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info white">
                <h4 class="modal-title white" id="myModalLabel10">
                    هل انت متاكد من تسليم الجهاز </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="delivery_form" action="{{ route('dashboard.invoices.delivery', $invoice->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label> اسم العميل </label>
                        <input type="text" name="delivery_date" class="form-control" value="{{ $invoice->name }}"
                            disabled>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary btn-sm" data-dismiss="modal">رجوع
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="delivery_button"> تسليم الجهاز </button>
                        <div id="loadingMessage" class="spinner-border text-primary" role="status" style="display: none;">
                            <span class="sr-only"> جاري تسليم الجهاز </span>
                        </div>
                    </div>
                </form>

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
    </div>
</div>
