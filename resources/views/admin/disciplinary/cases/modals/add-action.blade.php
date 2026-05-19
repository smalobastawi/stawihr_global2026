 <!-- Modal -->
 <div class="modal fade" id="addAction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('disciplinary.cases.action', $case->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="">
                        <label for="action_type">Action Type</label>
                        <select name="action_type" id="action_type" class="form-control">
                            <option value="">Choose an Action</option>
                            @foreach (\DisciplinaryActionTypes::toArray() as $key => $value)
                            <option value="{{ $key }}" >
                                {{ $value }}
                            </option>
                        @endforeach
                        </select>
                    </div>

                    <div class="">
                        <label for="description">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="">
                        <label for="action_date">Action Date</label>
                        <input type="date" name="action_date" id="action_date" class="form-control" required>
                    </div>

                    <div class="">
                        <label for="status">Action Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Choose Status</option>
                            @foreach (\DisciplinaryCaseStatus::toArray() as $key => $value)
                            <option value="{{ $key }}" >
                                {{ $value }}
                            </option>
                        @endforeach
                        </select>
                    </div>
                    <div class="">
                        <label for="case_status">Case Status</label>
                        <select name="case_status" id="case_status" class="form-control" required>
                            <option value="">Choose Case</option>
                            @foreach (\DisciplinaryCaseStatus::toArray() as $key => $value)
                            <option value="{{ $key }}" >
                                {{ $value }}
                            </option>
                        @endforeach
                        </select>
                    </div>

                    <div class="">
                        <label for="attachment">Attachment</label>
                        <input type="file" name="attachment" id="attachment" class="form-control">
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