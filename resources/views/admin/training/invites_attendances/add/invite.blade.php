<div class="table-responsive">
    <div class="mb-3" style="margin-bottom: 5px;">
        <button id="selectAllBtn" class="btn btn-info">
            <i class="fa fa-check-square"></i> Select All
        </button>
        <button id="addSelectedBtn" class="btn btn-success" disabled>
            <i class="fa fa-plus"></i> Add Selected Employees
        </button>
    </div>
    <table id="attendandingTbl" class="table table-bordered">
        <thead class="tr_header">
        <tr>
            <th width="5%">
                <input type="checkbox" id="selectAllCheckbox">
            </th>
            <th>Department</th>
            <th>Employee</th>
            <th>Designation</th>  
            {{-- <th>Actions</th>  --}}
        </tr>
        </thead>
        <tbody>
            @foreach ($emps as $dt)
          
                <tr>
                    <td>
                        <input type="checkbox" class="employee-checkbox" 
                            data-employee-id="{{ $dt->employee_id }}"
                            data-training-id="{{ $training->id }}">
                    </td>
                    <td>{{$dt->department->department_name ?? ''}}</td> 
                    <td>{{$dt->fullName()}}</td>
                    <td>{{$dt->designation->designation_name?? ''}}</td>
                    {{-- <td> 
                        <button id="tr__{{$training->training_id  }}__emp__{{ $dt->employee_id }}" addRoute="{{ route('trainingInfo.invitees.add',$training,$dt->employee_id) }}" type="button" title="Add" href="#" class="btn btn-success btn-xs btnColor adding_invite">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button> 
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</div>