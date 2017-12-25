{{-- 
  Name : Tanmaya Patra
  Info : Modal Window for Subcaterory Management
  Date : 13-Nov-2017
 --}}
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
             {{ Form::open(['url' => 'master-fees-save', 'form' => '1']) }} 
             {{ csrf_field() }}
             {{ Form::hidden('id', isset($id) ? $id :'', []) }}
                <div class="row">
                    @foreach($customFields['master'] as $CFkey => $CFvalue)
                      @php $class = isset($CFvalue['class']) ? $CFvalue['class'] : ''; @endphp
                            <div class="col-sm-{{ isset($CFvalue['btn']) ? '2': '2' }} {{ isset($CFvalue['optColDiv']) ? $CFvalue['optColDiv']: '' }}">
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
                                    {{ Form::select($CFkey, $CFvalue['value'],  isset($$CFkey) ? $$CFkey :'', ['class' => 'form-control input-md '.$class.' ' ]) }}
                                  @elseif($CFvalue['type'] == 'file')
                                    {{ Form::file($CFkey, ['id' => '', 'class' => 'form-control']) }}
                                  @elseif($CFvalue['type'] == 'checkbox')
                                    {{ Form::checkbox($CFkey, '1', ['id' => 'exampleInputFile']) }} Yes.
                                  @endif
                                </div>
                                <p class="help-block">
                                  {{ $errors->has($CFkey) ? $errors->first($CFkey, ':message') : 'Please Provide Valid '.$CFvalue['label'] }}
                                </p>
                              </div>
                            </div>
                            @if(isset($CFvalue['btn']) && count($CFvalue['btn']) > 0)
                            <div class="col-sm-1">

                              <div class="form-group ">
                                      <label for="exampleInputFile"></label>
                                <div class="input text">                  
                                  {{ link_to('foo/bar', $title = 'Add', $attributes = array('class' => 'btn btn-info btn-sm', 'data-toggle' => 'modal', 'data-target'=> $CFvalue['btn']['linkTo']), $secure = null) }}
                                </div>
                              </div>
                            </div>
                            @endif
                    @endforeach
                </div>
            <center>
              <div class="box-footer">
                {{ Form::submit($formButton, array('name' => 'form-fees', 'class' => 'btn btn-success')) }}
                {{ Form::reset('Reset', array('class' => 'btn btn-warning')) }}
              </div>
            </center>
          {{ Form::close() }}
        </div>
            {{-- 
              Name : Tanmaya Patra
              Info : Modal Window for Caterory Management
              Date : 13-Nov-2017
             --}}
            <div id="modal-category" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Fees Category Master</h4>
                  </div>
                  <div class="modal-body">
                    {{ Form::open(['url' => 'cat-save', 'form' => '2']) }} 
                         {{ csrf_field() }}
                         {{ Form::hidden('id', isset($id) ? $id :'', []) }}
                   <div class="row">
                      <div class="col-sm-12">
                        <div class="form-group {{ $errors->has($CFkey) ? 'has-error' : ''}}">
                                <label for="exampleInputFile">Category Name <span class="text-danger"> *</span>
                          </label>
                          <div class="input text">
                            {{ Form::text('category_name', isset($category_name) ? $category_name :'', ['class' => 'form-control input-md capsLock alpha', 'placeholder' => 'Category Name', 'autocomplete' => 'off']) }}
                          </div>
                          <p class="help-block">
                            {{ $errors->has('category_name') ? $errors->first('category_name', ':message') : 'Please Provide Valid Category Name' }}
                          </p>
                        </div>
                      </div>
                      <center>
                        <div class="box-footer">
                          {{ Form::submit($formButton, array('name' => 'form-category', 'class' => 'btn btn-success btn-sm')) }}
                        </div>
                      </center>      
                   </div>
                   {{ Form::close() }}
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div> 
            {{-- 
              Name : Tanmaya Patra
              Info : Modal Window for Subcaterory Management
              Date : 13-Nov-2017
             --}}
            <div id="modal-subcategory" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Fees Sub-Category Master</h4>
                  </div>
                  <div class="modal-body">
                    {{ Form::open(['url' => 'subcat-save', 'form' => '3']) }} 
                         {{ csrf_field() }}
                         {{ Form::hidden('id', isset($id) ? $id :'', []) }}
                   <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group {{ $errors->has('category_name') ? 'has-error' : ''}}">
                                <label for="exampleInputFile">Category Name <span class="text-danger"> *</span>
                          </label>
                          <div class="input text">
                            {{ Form::select('category_id', $data['catList'], isset($category_name) ? $category_name :'', ['class' => 'form-control input-md']) }}
                          </div>
                          <p class="help-block">
                            {{ $errors->has('subcategory_name') ? $errors->first('category_name', ':message') : 'Please Provide Valid Category Name' }}
                          </p>
                        </div>
                      </div>

                      <div class="col-sm-6">
                        <div class="form-group {{ $errors->has('subcategory_name') ? 'has-error' : ''}}">
                                <label for="exampleInputFile">Sub Category Name <span class="text-danger"> *</span>
                          </label>
                          <div class="input text">
                            {{ Form::text('subcategory_name', isset($subcategory_name) ? $subcategory_name :'', ['class' => 'form-control input-md capsLock', 'placeholder' => 'Category Name', 'autocomplete' => 'off']) }}
                          </div>
                          <p class="help-block">
                            {{ $errors->has('subcategory_name') ? $errors->first('subcategory_name', ':message') : 'Please Provide Valid Category Name' }}
                          </p>
                        </div>
                      </div>
                      <center>
                        <div class="box-footer">
                          {{ Form::submit($formButton, array('name' => 'form-subcategory', 'class' => 'btn btn-success btn-sm')) }}
                        </div>
                      </center>      
                   </div>
                   {{ Form::close() }}
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div> 

   <!-- /.box-body -->
        <div class="box-footer">
          Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about
          the plugin.
        </div>
          <!-- /.row -->
        </div>
      <!-- /.box -->

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">List View of Master Fees</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        <div id="admission-list">
            <table class="table table-responsive table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Sub-category</th>
                    <th>Class</th>
                    <th>Price</th>
                    <th>Compulsory</th>
                    <th>Remark</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($data['feesMasterData'] as $feesdata)
                  <tr>
                    <td>{{ $data['i'] }}</td>
                    <td>{{ Helper::getCategoryName($feesdata['category_id']) }}</td>
                    <td>{{ Helper::getSubCategoryName($feesdata['subcategory_id']) }}</td>
                    <td>{{ 'STD - '.$feesdata['class'] }}</td>
                    <td>{{ $feesdata['amount'] }}</td>
                    <td>{{ $feesdata['is_mandatory'] }}</td>
                    <td>{{ $feesdata['remark'] }}</td>
                    <td>Edit | Delete</td>
                  </tr>
                  @php $data['i']++; @endphp
                  @endforeach
                </tbody>
            </table>
          </div>
        </div>
      </div>

    </section>
    <!-- /.content -->
    @endsection

    @section('extra-javascript')
      <script type = "text/javascript" language = "javascript">
         $(document).ready(function() {
            $(".category_id").change(function(event){
              $('.overlay').show();
               var category_id = $(this).val();
               console.log(category_id);
               $.ajax({
                type: "POST",
                url: "ajax-get-subcategories",
                data: {category_id: category_id},
                dataType : 'html',
                cache: false,
                success: function(data){
                  $('.overlay').hide();
                  console.log(data);
                   $(".subcategory_name").html(data);
                }
              });
            });
         });
      </script>
    @endsection
