<div class="table-responsive">
    <table id="myTable" class="table table-bordered">
        <thead class="tr_header">
        <tr>
            <th>Department</th>
            <th>Employee</th>
            <th>Date</th>  
            <th>Actions</th> 
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $dt)
            <tr>
                <td>{{$dt->department->department_name}}</td> 
               <td>{{$dt->employee->fullName()}}</td>
               <td>{{$dt->created_at}}</td> 
                <td> 
                <a title="Delete" href="{{route('trainingInfo.attendants.delete',[$dt->training,$dt])}}" data-token="{!! csrf_token() !!}" data-id="{!! $dt->id  !!}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                 </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>