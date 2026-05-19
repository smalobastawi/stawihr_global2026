<div class="modal fade" id="actionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalTitle">Approve/Reject Request</h4>
            </div>
            <form id="actionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="comments">Comments</label>
                        <textarea name="comments" id="comments" class="form-control" rows="3" 
                            placeholder="Enter your comments here..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="modalActionBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>