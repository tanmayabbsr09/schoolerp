{{-- 
  Name : Tanmaya Patra
  Info : Modal Window for Subcaterory Management
  Date : 13-Nov-2017
 --}}
@section('payment-overview-table')
      <table class="table table-responsive table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Education Year</th>
              <th>Month</th>
              <th>Payment Status</th>
              <th>View</th>
            </tr>
          </thead>
          <tbody>  
            @if(isset($data['allMonthOverview']['pending_payment_details']) && count($data['allMonthOverview']['pending_payment_details']) > 0)
              @foreach($data['allMonthOverview']['pending_payment_details'] as $overview)
              <tr>
                <td>{{ $counter }}</td>
                <td>{{ $overview['academic_year'] }}</td>
                <td>{{ $overview['academic_month'] }}  </td>
                <td>
                  @if(isset($overview['payment_pendings']) && is_array($overview['payment_pendings']) && count($overview['payment_pendings']) > 0)
                    <div class="label label-danger">Payment Pending</div>
                  @else
                    <div class="label label-success">Payment Done</div>
                  @endif
                </td>
                <td></td>
              </tr>
              @php $counter ++; @endphp
              @endforeach
              @else
              <tr>
                <td colspan="5"><center><div class="alert alert-info">Sorry ! No Data Found.</div></center></td>
              </tr>
              @endif
          </tbody>
      </table>
@endsection

@if(isset($year))
  @yield('payment-overview-table')
  @php return true; @endphp
@endif


@extends('layouts.default')
@section('content')
<section class="content">
<style type="text/css">
  .box-title{
    margin-left: 12px;
  }
</style>
      <!-- SELECT2 EXAMPLE -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Please fill up necessary fields.</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="overlay" style="display: none">
            <i class="fa fa-refresh fa-spin"></i>
          </div>
        @if(session()->has('message.level'))
          <div class="alert alert-{{ session('message.level') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h4>
            {!! session('message.content') !!}
          </div>
        @endif
             {{ Form::open(['url' => url('/').'/payment-save', 'form' => '1']) }} 
             {{ csrf_field() }}
             {{ Form::hidden('id', isset($id) ? $id :'', []) }}
             {{ Form::hidden('sid', isset($sid) ? $sid :'', ['class' => 'sid']) }}
             
              @php
                if(isset($academic_month) && $academic_month != null){
                  $r = json_decode($academic_month);
                  $academic_month = array_values($r);
                }
                if(isset($fees_master_id) && $fees_master_id != null){
                  $fees_master_id = json_decode($fees_master_id);
                }else{
                  $fees_master_id = array();
                }

              @endphp
                <div class="row">
                    <div class="col-md-4">
                      <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa  fa-child"></i></span>
                        <div class="info-box-content">
                          <span class="info-box-text">{{ $data['studentDetails']['name'] }}</span>
                          <span class="progress-description">Father <span class="pull-right badge bg-blue">{{$data['studentDetails']['father_name']}}</span></span>
                          <span class="progress-description">Mother <span class="pull-right badge bg-blue">{{$data['studentDetails']['mother_name']}}</span></span>
                          <span class="progress-description">Studying in Class {{$data['studentDetails']['admission_class']}}</span>
                        </div>
                        <!-- /.info-box-content -->
                      </div>
                    </div>
                      <div class="col-sm-4">
                        <div class="form-group {{ $errors->has('academic_year') ? 'has-error' : ''}}">
                                <label for="exampleInputFile">Academic Year <span class="text-danger"> *</span>
                          </label>
                          <div class="input text">
                            {{ Form::select('academic_year', $data['year_array'] , isset($academic_year) ? $academic_year :'', ['class' => 'form-control academic_year input-md', 'required']) }}
                          </div>
                          <p class="help-block">
                            {{ $errors->has('academic_year') ? $errors->first('academic_year', ':message') : '' }}
                          </p>
                        </div>
                      </div>

                     <div class="col-sm-4">
                        <center>
                          <div class="box-footer">
                            {{ Form::button($data['submitBtnName'], array('name' => 'form-fees', 'class' => 'show-overview-by-year btn btn-success')) }}
                          </div>
                        </center>
                     </div>


                </div>
            {{-- Cut awat Submit button and form end --}}
        </div>
          <!-- /.row -->
        </div>
      <!-- /.box -->

      <div class="box box-default">
        <div class="box-header with-border">
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        <div id="overview-payment-render">
            
@yield('payment-overview-table')
            

          </div>
        </div>
      </div>

          {{ Form::close() }}
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
      <script type = "text/javascript" language = "javascript">
         $(document).ready(function() {
            $(".show-overview-by-year").click(function(event){
              $('.overlay').show();
               var academic_month = $(this).val();
               var academic_year = $('.academic_year :selected').val();
               var sid = $('.sid').val();
               $.ajax({
                type: "GET",
                url: "{{url('/')}}/get-payment-overview-by-year-id/"+sid+"/"+academic_year,
                data: { 
                        "_token": "{{ csrf_token() }}",
                       
                      },
                dataType : 'html',
                cache: false,
                success: function(data){
                  $("#overview-payment-render").html(data);
                  $('.overlay').hide();
                }
              });
            });

            $("#ckbCheckAll").click(function () {
                $(".checkBoxClass").prop('checked', $(this).prop('checked'));
            });

         });
      </script>
    @endsection
