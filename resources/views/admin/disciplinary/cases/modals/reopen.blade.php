<div class="modal fade" id="reopen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reopen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('disciplinary.cases.close', $case->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="">
                        <label for="remarks">Reopen Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="">
                        <label for="action_date">Open Date</label>
                        <input type="date" name="closed_date" id="closed_date" class="form-control" required>
                    </div>

                    <div class="">

                        <input type="hidden" name="case_status" id="case_status" class="form-control" required value="{{ DisciplinaryCaseStatus::PENDING }}" readonly>

                    </div>

                    <div class="">
                        <label for="attachment">Attachment</label>
                        <input type="file" name="attachment" id="attachment" class="form-control">
                    </div>

                    <div class="">
                        <label for="notify_user">
                            <input type="checkbox" name="notify_user" id="notify_user">
                            Notify Employee
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Action</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>
