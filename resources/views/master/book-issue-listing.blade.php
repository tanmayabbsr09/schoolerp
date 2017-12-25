@extends('layouts.default')

@section('content')
<section class="content">

  <div class="box">
            <div class="box-header">
              <h3 class="box-title">List of all Events</h3>
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
              <div class="table-bootstrap">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>SL no.</th>
                    <th>Student Name</th>
                    <th>Book Name</th>
                    <th>Class</th>
                    <th>Issue Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @php $datepickerString = ''; @endphp
                    @foreach($events as $event)
                    @if($event['to_date'] >= date('Y-m-d'))

                    @endif
                  <tr class="{{ ($event['to_date'] >= date('Y-m-d')) ? 'text-primary':'text-danger' }}">
                    <td>{{ $loopInit }}</td>
                    <td>{{ Helper::getStudentInfo($event['admission_id']) }}</td>
                    <td>{{ Helper::getBookInfo($event['library_id']) }}</td>
                    <td>{{ 'STD-'.Helper::integerToRoman($event['admission_class']) }}</td>
                    <td>{{ Helper::changeDateFormat('d-m-Y', $event['from_date']) }}</td>
                    <td>{{ Helper::changeDateFormat('d-m-Y', $event['to_date']) }}</td>
                    <td>
                      @if($event['is_paid'] == 0)
                      <span class="text-danger">Nor Returned!</span>
                      @else
                      <span class="text-success">Returned</span>
                      @endif
                    </td>
                    
                    <td>
                      <a href="{{ url('/')}}/library-edit/{{ $event['id'] }}" class="btn btn-success btn-xs"><i class="fa fa-edit"></i></a> 
                      @if($event['is_paid'] == 0)
                      <a href="javascript:void(0)" class="btn btn-primary btn-xs return_{{$event['id']}}" title="Return Book" data-toggle="modal" data-target="#bookRet_{{$event['id']}}"><i class="fa fa-undo" aria-hidden="true"></i></a>
                      @else
                      <a href="javascript:void(0)" disabled class="btn btn-success btn-xs" title="Book Returned"><i class="fa fa-check" aria-hidden="true"></i>
</a>
                      @endif
                      <a href="{{ url('/')}}/library-invoice/{{ $event['id'] }}" class="btn btn-warning btn-xs" title="Invoice of Issue">INV</a>
                      <a href="{{-- {{ url('/')}}/library-delete/{{ $event['id']}} --}}" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-trash"></i></a>
                    {{-- ISSUES BOOK'S RETURN MODAL --}}
                    <!-- Modal -->
                    <div id="bookRet_{{$event['id']}}" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Library Fine Details</h4>
                          </div>
                          <div class="modal-body">
                            {{-- Modal Form Start --}}
                            <div class="row">
                              <div class="col-sm-2">
                                @if(strtotime($event['to_date']) < strtotime(date('Y-m-d')) )
                                {{ $dateDiff = Helper::dateDiff(date('Y-m-d'), $event['to_date']) }}
                                <small>Total Delay Days</small>
                                @else
                                {{ $dateDiff = '0' }}
                                @endif
                                {{-- <input type="text" class="form-control" placeholder=".col-sm-3"> --}}
                                
                              </div>
                              <div class="col-sm-2">
                                @php 
                                  setlocale(LC_MONETARY, 'en_IN');
                                  $amount = (float)$dateDiff*15;
                                @endphp 
                                {{ 'Rs. '.Helper::moneyFormatIndia($amount)  }}
                                {{-- <input type="text" class="form-control" placeholder=".col-sm-4"> --}}
                              </div>
                              <div class="col-md-3">
                                {{ Form::select('is_any_damage', array('0' => 'Not Damaged', '1' => 'Damaged'), '', ['class' => 'form-control is_any_damage_'.$event['id']])}} 
                              </div>
                              <div class="col-sm-3">
                                {{ Form::text('return_date', '', ['class' => 'form-control return_date_'.$event['id'], 'id' => 'datepicker', 'placeholder' => 'Return Date']) }}
                                {{ Form::hidden('total_delays', $dateDiff, ['class' => 'form-control total_delays_'.$event['id'], 'id' => '']) }}
                                {{ Form::hidden('total_fine', $amount, ['class' => 'form-control total_fine_'.$event['id'], 'id' => '']) }}
                              </div>
                              <div class="col-sm-2">
                                {{ Form::button('<i class="fa fa-star"></i> Submit', array(
                                        'type' => 'button',
                                        'class'=> 'btn btn-success',
                                        'data-id' => $event['id'],
                                        'onclick'=> "saveReturnDate(".$event['id'].")"
                                )) }}
                              </div>
                            </div>
                            {{-- Modal Form ends --}}
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    {{-- Modal end --}}
                    </td>
                  </tr>
                  @php $loopInit++ @endphp
                  @endforeach
                
                  </tbody>
                </table>
              </div>
              {{ $events->links() }}
            </div>
            <!-- /.box-body -->
            <div class="alert alert-dismissible" style="border:1px solid"> 
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <ul>
                  <a href="javascript:void(0)" class="btn btn-success btn-xs"><i class="fa fa-edit"></i></a> : Edit ;
                  <a href="javascript:void(0)" disabled="" class="btn btn-success btn-xs" title="Book Returned"><i class="fa fa-check" aria-hidden="true"></i>
</a> : Book Returned ;
                  <a href="javascript:void(0)" class="btn btn-primary btn-xs return_3" title="Return Book" data-toggle="modal" data-target="#bookRet_3"><i class="fa fa-undo" aria-hidden="true"></i></a> : Return Book ;
                  <a href="javascript:void(0)" class="btn btn-warning btn-xs" title="Invoice of Issue">INV</a> : Generate Invoice; <a href="" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a> : Trash/Delete 
                </ul>
              </div>


          </div>


    </section>
    <!-- /.content -->
    @endsection
@section('extra-javascript')
<script type="text/javascript">
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
</script>
<script type="text/javascript">
  function saveReturnDate(issue_id) {
    var return_date = $(".return_date_"+issue_id).val();
    var total_delays = $(".total_delays_"+issue_id).val();
    var total_fine = $(".total_fine_"+issue_id).val();
    var is_any_damage = $(".is_any_damage_"+issue_id+" :selected").val();
    $.ajax({
      type: "POST",
      url: "{{url('/')}}/library-return-date-save",
      data: { 
              "_token": "{{ csrf_token() }}",
             return_date : return_date,
             issueid : issue_id,
             total_delays:total_delays,
             total_fine:total_fine,
             is_any_damage:is_any_damage
            },
      dataType : 'html',
      cache: false,
      success: function(data){
        console.log(data);
        if(data == 'done'){
          $(function () {
             $('#bookRet_'+issue_id).modal('toggle');
          });
        }
      /*  students = $.parseJSON(data);
        $('.library_id')
                .empty()
                .append('<option selected="selected" value="">-Select Student -</option>');
        $.each(students, function(i, item) {
            $('.library_id').append(
                  '<option value="'+i+'">'+item+'</option>'
             );
        });*/
        $('.overlay').hide();
      }
    });
  }
</script>
@endsection