@extends('layouts.default')

@section('content')
<section class="content">

  <div class="box">
            <div class="box-header">
              <h3 class="box-title">List of all Employees</h3>
            </div>
            <!-- /.box-header -->
 <div class="box-body">
          
        
        @if(session()->has('message.level'))
          <div class="alert alert-{{ session('message.level') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h4>
            {!! session('message.content') !!}
          </div>
        @endif

        {{-- @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}
             {{ Form::open(['url' => 'employee-list', 'files' => true]) }} 
             {{ csrf_field() }}
             {{ Form::hidden('id', isset($id) ? $id :'', []) }}
                <div class="row">
                    @foreach($customFields['basic'] as $CFkey => $CFvalue)
                      @php $class = isset($CFvalue['class']) ? $CFvalue['class'] : ''; @endphp
                            <div class="col-sm-3 {{ isset($CFvalue['optColDiv']) ? $CFvalue['optColDiv']: '' }}">
                              <div class="form-group {{ $errors->has($CFkey) ? 'has-error' : ''}}">
                                      <label for="exampleInputFile">{{ $CFvalue['label'] }} 
                                    @if($CFvalue['mandatory'])
                                        <span class="text-danger"> *</span>
                                    @endif
                                </label>
                                <div class="input text">
                                  @if($CFvalue['type'] == 'text')
                                    {{ Form::text($CFkey, isset($$CFkey) ? $$CFkey :'', ['class' => 'form-control input-md '.$class.' ', 'id' => isset($CFvalue['id']) ? $CFvalue['id']: '', 'style' => isset($CFvalue['style']) ? $CFvalue['style']: '', 'placeholder' => $CFvalue['label'], 'autocomplete' => 'off']) }}
                                  @elseif($CFvalue['type'] == 'select')
                                    {{ Form::select($CFkey, $CFvalue['value'],  isset($$CFkey) ? $$CFkey :'', ['class' => 'form-control input-md' ]) }}
                                  @elseif($CFvalue['type'] == 'file')
                                    {{ Form::file($CFkey, ['id' => '', 'class' => 'form-control']) }}
                                  @elseif($CFvalue['type'] == 'password')
                                    {{ Form::password($CFkey, ['id' => '', 'class' => 'form-control']) }}
                                  @endif
                                </div>
                                <p class="help-block">
                                  {{ $errors->has($CFkey) ? $errors->first($CFkey, ':message') : '' }}
                                </p>
                              </div>
                            </div>
                    @endforeach
                </div>






                                            

            <div class="box-footer">
              {{ Form::submit('Search', array('class' => 'btn btn-success')) }}
              {{ Form::reset('Reset', array('class' => 'btn btn-warning')) }}
            </div>

          <!-- /.row -->
        </div>



            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>SL no.</th>
                  <th>Employee ID</th>
                  <th><a href="{{ URL::to('employee-list?sort=id') }}">Employee Name</a></th>
                  <th>Designation</th>
                  <th>Employee Email</th>
                  <th>Department</th>
                  <th>Date of Birth</th>
                  <th>Blood Group</th>
                  <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                  @foreach($users as $user)
                <tr>
                  <td>{{ $loopInit }}</td>
                  <td>{{ 'YPPS/EMP-'.$user->id }}</td>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->role->name }}</td>
                  <td>{{ $user->email}}</td>
                  <td>{{ strtoupper($user->department_name) }}</td>
                  <td>{{ date('d-m-Y', strtotime($user->dob)) }}</td>
                  <td>{{ strtoupper($user->blood_group) }}</td>
                  <td>
                    @if($user->role->id !== 1)
                    <a href="/employee-edit/{{ $user->id }}" class="btn btn-success btn-xs"><i class="fa fa-edit"></i></a> 
                    <a href="/admission-delete/{{ $user->id}}" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-trash"></i></a> 
                    <a href="/block-user/{{ Hash::make($user->id) }}" class="btn btn-danger btn-xs" title="Block User" onclick="return confirm('Are you sure you want to Block this User?');"><i class="fa fa-ban"></i></a> 
                    @endif
                {{--     {!! Html::decode(link_to(URL::previous(),
                          '<i class="fa fa-chevron-left" aria-hidden="true"></i> ',
                          ['class' => 'btn btn-primary btn-sm'])) !!} --}}

                  </td>
                </tr>
                @php $loopInit++ @endphp
                @endforeach
              
                </tbody>
              </table>
              {{ $users->links() }}
            </div>
            <!-- /.box-body -->
          </div>


    </section>
    <!-- /.content -->
    @endsection