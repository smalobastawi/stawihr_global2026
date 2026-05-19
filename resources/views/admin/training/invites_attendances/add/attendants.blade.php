<div class="table-responsive">
    <table id="attendandingTbl" class="table table-bordered">
        <thead class="tr_header">
        <tr>
            <th>Department</th>
            <th>Employee</th>
            <th>Designation</th>  
            <th>Actions</th> 
        </tr>
        </thead>
        <tbody>
            @foreach ($emps as $dt)
          
            <tr>
                <td>{{$dt->department->department_name}}</td> 
               <td>{{$dt->fullName()}}</td>
               <td>{{$dt->designation->designation_name?? ''}}</td>
               

               <td> 
                <button id="tr__{{$training->training_id  }}__emp__{{ $dt->employee_id }}" addRoute="{{ route('trainingInfo.attendants.add',$training,$dt->employee_id) }}" type="button" title="Add" href="#" class="btn btn-success btn-xs btnColor adding_invite">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button> </td>
                 </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>