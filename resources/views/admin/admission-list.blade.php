@extends('layouts.default')

@section('content')
<section class="content">

  <div class="box">
            <div class="box-header">
              <h3 class="box-title">List of all Admitted Students</h3>
            </div>
            <!-- /.box-header -->
             
            <div class="box-body">

              {{ Form::open(['url' => 'admission-list', 'files' => true]) }} 
             {{ csrf_field() }}
             {{ Form::hidden('id', isset($id) ? $id :'', []) }}
                <div class="row">
                    @foreach($customFields['search'] as $CFkey => $CFvalue)
                      @php $class = isset($CFvalue['class']) ? $CFvalue['class'] : ''; @endphp
                            <div class="col-sm-{{ $CFvalue['col_num'] }} {{ isset($CFvalue['optColDiv']) ? $CFvalue['optColDiv']: '' }}">
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

          <center>
            <div class="box-footer">
              {{ Form::submit('Search', array('class' => 'btn btn-success btn-sm')) }}
              {{ Form::reset('Reset', array('class' => 'btn btn-warning btn-sm', 'onclick' => 'resetForm()')) }}
            </div>
          </center>


              <div class="col-sm-7 pull-left">
                {{ $admissionList->links() }}</div>
              <div class="col-sm-3 pull-right" style="margin-top: 22px">
                Showing {{ $admissionList->firstItem() }} to {{ $admissionList->lastItem() }} of {{ $admissionList->total() }} Students
              </div>
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>SL no.</th>
                  <th>Enroll ID</th>
                  <th>Student Name</th>
                  <th>Father's Name</th>
                  <th>Mother's Name</th>
                  <th>Gender</th>
                  <th>Date of Birth</th>
                  <th>Class</th>
                  <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                  @foreach($admissionList as $key => $student)
                <tr>
                  <td> {{ ($admissionList->currentpage()-1) * $admissionList->perpage() + $key + 1 }}</td>
                  <td>{{ 'YPPS-'.$student['id']}}</td>
                  <td>{{ $student['name']}}</td>
                  <td>{{ $student['father_name']}}</td>
                  <td>{{ $student['mother_name']}}</td>
                  <td>{{ $student['gender']}}</td>
                  <td>{{ $student['dob']}}</td>
                  <td>{{ $student['admission_class']}}</td>
                  <td>
                    <a href="{{url('/')}}/admission/{{ $student['id'] }}" class="btn btn-success btn-xs"><i class="fa fa-edit"></i></a> 
                    <a href="{{url('/')}}/admission/{{ $student['id']}}" class="btn btn-info btn-xs"><i class="fa fa-plane"></i></a> 
                    <a href="{{url('/')}}/admission-delete/{{ $student['id']}}" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a> 
                    <a href="{{url('/')}}/payment/{{ $student['id']}}" class="btn btn-primary btn-xs" title="Make Payment" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-inr"></i></a> 
                {{--     {!! Html::decode(link_to(URL::previous(),
                          '<i class="fa fa-chevron-left" aria-hidden="true"></i> ',
                          ['class' => 'btn btn-primary btn-sm'])) !!} --}}

                  </td>
                </tr>
                @php $loopInit++ @endphp
                @endforeach
              
                </tbody>
              </table>
              {{ $admissionList->links() }}
            </div>
            <!-- /.box-body -->
          </div>


    </section>
    <!-- /.content -->
    @endsection