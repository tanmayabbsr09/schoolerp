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
             <div class="overlay">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
        
        @if(session()->has('message.level'))
          <div class="alert alert-{{ session('message.level') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h4>
            {!! session('message.content') !!}
          </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <ul>
                  <li>Warning ! Please resolve following errors.</li>
                </ul>
            </div>
        @endif
             {{ Form::open(['url' => 'hostel-room-save']) }} 
             {{ csrf_field() }}
             {{ Form::hidden('id', isset($id) ? $id :'', []) }}
                <div class="row">
                  <h4 class="box-title text-purple">Enter Book Details</h4>
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
                                    {{ Form::select($CFkey, $CFvalue['value'],  isset($$CFkey) ? $$CFkey :'', ['class' => 'form-control input-md '.$class.' ' , 'style' => isset($CFvalue['style']) ? $CFvalue['style']: '', isset($CFvalue['multiple']) ? $CFvalue['multiple'] : 'not-multiple' ]) }}
                                  @elseif($CFvalue['type'] == 'file')
                                    {{ Form::file($CFkey, ['id' => '', 'class' => 'form-control']) }}
                                  @elseif($CFvalue['type'] == 'password')
                                    {{ Form::password($CFkey, ['id' => '', 'class' => 'form-control']) }}
                                  @endif
                                </div>
                                <p class="help-block">
                                  {{ $errors->has($CFkey) ? $errors->first($CFkey, ':message') : 'Enter Valid Information' }}
                                </p>
                              </div>
                            </div>
                    @endforeach
                </div>


            <div class="box-footer">
              {{ Form::submit($formButton, array('class' => 'btn btn-success')) }}
              {{ Form::reset('Reset', array('class' => 'btn btn-warning')) }}
            </div>

          <!-- /.row -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about
          the plugin.
        </div>
      </div>
      <!-- /.box -->


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
          $('.overlay').hide();
            $(".room_no").keyup(function(event){
              $('.overlay').show();
               var room_no = $(this).val();
               var hostel_id = $('.hostels_id :selected').val();
               
               $.ajax({
                type: "POST",
                url: "{{url('/')}}/validate-hostel-roomno",
                data: { 
                        "_token": "{{ csrf_token() }}",
                       room_no : room_no,
                       hostel_id : hostel_id
                      },
                dataType : 'html',
                cache: false,
                success: function(data){
                  if(data == 1){
                    $(".room_no").val('');
                    alert('You have Entered Room Details for this Room Previously! Please try another room Number...');
                  }
                

                 /* $("#overview-payment-render").html(data);*/
                  $('.overlay').hide();
                }
              });
            });

            

         });
      </script>
    @endsection