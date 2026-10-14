@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.newservice.title'))


@push('after-styles')
  <style>
  /* #serial-list{float:left;list-style:none;margin-top:-3px;padding:0;width:190px;position: absolute;} */
  #serial-list li{padding: 10px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid;}
  #serial-list li:hover{background:#ece3d2;cursor: pointer;}
  #search-box{padding: 10px;border: #a8d4b1 1px solid;border-radius:4px;float:left;height: 30px}
  #header{position:relative;line-height:1.5em;}
      .filterform {
        width:220px;
        font-size:12px;
        display:block;
      }

  </style>
@endpush

@section('content')
@php
$mytime = Carbon\Carbon::now()->format('d/m/Y');
@endphp
<form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.service.store') }}" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <div class="row">
                  <div class="col text-left">
                    <h4 class="card-title">@lang('strings.backend.newservice.title')</h4>
                  </div>
                    <div class="col text-right">
                        <a class="btn btn-danger btn-big" href="{{ route('admin.service.index') }}">Cancel</a>
                        <button class="btn btn-primary btn-bg pull-right" type="submit">Create</button>
                    </div><!--col-->
                  </div>
                </div><!--card-header-->
                <div class="card-body">

                      {{ csrf_field() }}
                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="receiptno" class="col-md-2 form-control-label">Receipt No <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','receiptno','auto generate')
                                ->class('form-control')
                                ->value('auto generate')
                                ->disabled()
                                 }}

                          </div>
                          <label for="assignto" class="col-md-2 form-control-label">Assign To <span class="required">*</span></label>
                          <div class="col-md-4">
                              {{ html()->select('assignto')
                                       ->class('form-control')
                                       ->value(old('assignto'))
                                       ->options($user_enginners)
                                       ->placeholder('Select enginner')
                                       ->required()
                                    }}



                          </div>
                      </div>
                      <!-- Row 2 -->
                      <div class="form-group row{{ $errors->has('date') ? ' has-error' : '' }}">
                          <label for="date" class="col-md-2 form-control-label">Date in <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','date')
                                ->class('form-control')
                                ->value(old('date',$mytime))
                                ->required()
                                ->autofocus()
                               }}
                          </div>
                          <label for="warrenty" class="col-md-2 form-control-label">Warrenty</label>
                          <div class="col-md-4">
                            {{ html()->label(
                                    html()->checkbox('warrenty[]', old('warrenty'))
                                          ->class('switch-input')
                                          ->id('warrenty-'. '01')
                                        . '<span class="switch-slider" data-checked="in" data-unchecked="out"></span>')
                                    ->class('switch switch-label switch-pill switch-primary mr-2')
                                ->for('warrenty-'. '01') }}
                          </div>

                      </div>
                      <!-- Row 3 -->
                      <div class="form-group row{{ $errors->has('buyingdate') ? ' has-error' : '' }}">
                          <label for="buyingdate" class="col-md-2 form-control-label">Buying Date </label>
                          <div class="col-md-4">
                              {{ html()->input('text','buyingdate')
                                  ->class('form-control')
                                  ->value(old('buyingdate'))
                                   }}
                          </div>
                          <label for="status" class="col-md-2 form-control-label">Status <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->select('status')
                                     ->class('form-control')
                                     ->options($status)
                                     ->value('not start')
                                     ->placeholder('Select status')
                                     ->required()

                                  }}
                          </div>
                      </div>
                      <!-- Row 4 -->
                      <div class="form-group row{{ $errors->has('serialno') ? ' has-error' : '' }}">
                          <label for="serialno" class="col-md-2 form-control-label">Serial No <span class="required">*</span></label>
                          <div class="col-md-4" id="header">

                              {{ html()->input('text','serialno')
                                  ->class('form-control')
                                  ->value(old('serialno'))
                                  ->required() }}


                                     <div id="suggesstion-box"></div>
                          </div>
                          <label for="model" class="col-md-2 form-control-label">Model <span class="required">*</span></label>
                          <div class="col-md-4">
                              {{ html()->input('text','model')
                                  ->class('form-control')
                                  ->value(old('model'))
                                  ->required() }}
                          </div>
                      </div>

                      <!-- Row 5 -->
                      <div class="form-group row{{ $errors->has('chargetype') ? ' has-error' : '' }}">


                          {{-- <label for="chargetype" class="col-md-2 form-control-label">Serial# History </label> --}}

                          {{-- <div class="col-md-4">
                            <ul id="list" class="form-control" style="height:70px;position:block;overflow-y:auto;background:#ddd">
                            @foreach ($service_lists as $service)
                              <li><a href="{{ route('admin.service.show',['id'=>$service->id]) }}" title="Receipt#: {{ $service->receiptno  }}">{{ $service->serialno }}</a></li>
                            @endforeach

                            </ul>
                          </div> --}}
                          <div class="col-md-6">
                            <div class="row">
                            <label for="brand" class="col-md-4 form-control-label">Brand <span class="required">*</span></label>
                            <div class="col-md-8">
                              {{ html()->select('brand')
                                       ->class('form-control')
                                       ->options($brands)
                                       ->value(old('brand'))
                                       ->placeholder('Select brand')
                                       ->required()
                                    }}
                            </div>
                          </div>
                          <div class="row">
                          <label for="chargetype" class="col-md-4 form-control-label">Charge Type </label>
                          <div class="col-md-8">
                            {{ html()->label(
                                    html()->checkbox('chargetype[]', ((old('chargetype')=='cash')?1:0))
                                          ->class('switch-input')
                                          ->id('chargetype-'. '01')
                                        . '<span class="switch-slider" data-checked="yes"  data-unchecked="no"></span>')
                                    ->class('switch switch-label switch-pill switch-primary mr-2')
                                ->for('chargetype-'. '01') }}
                          </div>
                        </div>
                        <div class="row">
                          <label for="chargeamount" class="col-md-4 form-control-label">Amount <span class="required">*</span></label>

                          <div class="col-md-8">
                            @if(empty(old('chargetype')) || (old('chargetype')=='free'))
                              @php ($status_amount=true)
                            @else
                              @php ($status_amount=false)
                            @endif
                            {{ html()->input('number','chargeamount',0)
                                ->class('form-control')
                                ->value(old('chargeamount'))
                                ->disabled($status_amount)
                              }}

                          </div>
                        </div>

                        </div>
                      </div>
                      <!-- Row 6 -->
                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="problem" class="col-md-2 form-control-label">Problem <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->select('problem')
                                     ->class('form-control')
                                     ->options($problems)
                                     ->value(old('problem'))
                                     ->placeholder('Select problem')
                                     ->required()
                                  }}
                          </div>
                          <label for="description" class="col-md-2 form-control-label">Description <span class="required">*</span></label>
                          <div class="col-md-4">
                              <textarea id="description" class="form-control col-md-12 col-xs-12" name="description" required>{{ old('description') }}</textarea>
                              @if ($errors->has('description'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('description') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>
                      <!-- Row 7 -->
                      <div class="form-group row{{ $errors->has('customerid') ? ' has-error' : '' }}">
                        <label for="customerid" class="col-md-2 form-control-label">Customer <span class="required">*</span></label>
                        <div class="col-md-4">
                          {{ html()->select('customerid')
                                   ->class('form-control')
                                   ->options($customers)
                                   ->value(old('customerid'))
                                   ->placeholder('Select customer')
                                   ->required()
                                }}
                        </div>
                        <label for="phone" class="col-md-2 form-control-label">Phone <span class="required">*</span></label>
                        <div class="col-md-4">
                          {{ html()->input('text','phone',0)
                              ->class('form-control')
                              ->value(old('phone'))

                            }}
                        </div>
                      </div>
                      <!-- Row 8 -->
                      <div class="form-group {{ $errors->has('logo') ? 'has-error' : '' }}">
                  <label for="logo">Picture</label>
                  <div class="needsclick dropzone" id="logo-dropzone">

                  </div>


              </div>
<!--
                      <div class="form-group row{{ $errors->has('ordered') ? ' has-error' : '' }}">
                        <label for="ordered" class="col-md-2 form-control-label">Order </label>
                        <div class="col-md-4">
                          {{ html()->label(
                                  html()->checkbox('ordered[]', old('ordered'))
                                        ->class('switch-input')
                                        ->id('ordered-'. '01')
                                      . '<span class="switch-slider" data-checked="yes"  data-unchecked="no"></span>')
                                  ->class('switch switch-label switch-pill switch-primary mr-2')
                              ->for('ordered-'. '01') }}
                        </div>
                        <label for="ordered_comment" class="col-md-2 form-control-label">Comment</label>
                        <div class="col-md-4">
                          @if(old('ordered')==0)
                            @php ($status_order=true)
                          @else
                            @php ($status_order=false)
                          @endif
                          {{ html()->input('text','ordered_comment',0)
                              ->class('form-control')
                              ->value(old('ordered_comment'))
                              ->disabled($status_order)

                            }}
                        </div>
                      </div>
-->
                      <!-- Row 10 -->
<!--
                      <div class="form-group row{{ $errors->has('solution') ? ' has-error' : '' }}">
                        <label for="solution" class="col-md-2 form-control-label">Solution</label>
                        <div class="col-md-4">

                          {{ html()->textarea('solution',old('solution'))
                              ->class('form-control')
                            }}
                        </div>


                        <label for="destination" class="col-md-2 form-control-label">Vendor</label>
                        <div class="col-md-4">
                          {{ html()->select('destination')
                                   ->class('form-control')
                                   ->options($vendors)
                                   ->value(old('destination'))
                                   ->placeholder('Select Vendor')

                                }}
                        </div>
                      </div>
-->


                    </div>
                    <!--card-body-->
                </div>
            </div><!--card-->
        </div><!--col-->
      </form>
    </div><!--row-->

@endsection

@section('custom_script')
<script>

Dropzone.options.logoDropzone = {
  url: '{{ route('admin.service.storeMedia') }}',
  maxFilesize: 2, // MB
  acceptedFiles: '.jpeg,.jpg,.png,.gif',
  maxFiles: 1,
  addRemoveLinks: true,
  headers: {
    'X-CSRF-TOKEN': "{{ csrf_token() }}"
  },
  params: {
    size: 2,
    width: 4096,
    height: 4096
  },
  success: function (file, response) {
    $('form').find('input[name="logo"]').remove()
    $('form').append('<input type="hidden" name="logo" value="' + response.name + '">')
  },
  removedfile: function (file) {
    file.previewElement.remove()
    if (file.status !== 'error') {
      $('form').find('input[name="logo"]').remove()
      this.options.maxFiles = this.options.maxFiles + 1
    }
  },
  init: function () {
  @if(isset($service) && $service->logo)
    var file = {!! json_encode($service->logo) !!}
        this.options.addedfile.call(this, file)
    this.options.thumbnail.call(this, file, file.url)
    file.previewElement.classList.add('dz-complete')
    $('form').append('<input type="hidden" name="logo" value="' + file.file_name + '">')
    this.options.maxFiles = this.options.maxFiles - 1
  @endif
  },
  error: function (file, response) {
      if ($.type(response) === 'string') {
          var message = response //dropzone sends it's own error messages in string
      } else {
          var message = response.errors.file
      }
      file.previewElement.classList.add('dz-error')
      _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
      _results = []
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          node = _ref[_i]
          _results.push(node.textContent = message)
      }

      return _results
  }
  }

$(document).ready(function(){

  jQuery.expr[':'].Contains = function(a,i,m){
      return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())===0;
  };


  function listFilter(list) { // header is any element, list is an unordered list
    // create and add the filter form to the header
    //var form = $("<form>").attr({"class":"filterform","action":"#"}),
      //  input = $("<input>").attr({"class":"filterinput form-control","type":"text","name":"serialno"});

    //$(form).append(input).appendTo(header);

  //   $('input[name="serialno"]')
  //     .change( function () {
  //       var filter = $(this).val();
  //       if(filter) {
  //         // this finds all links in a list that contain the input,
  //         // and hide the ones not containing the input while showing the ones that do
  //         $(list).find("a:not(:Contains(" + filter + "))").parent().slideUp();
  //         $(list).find("a:Contains(" + filter + ")").parent().slideDown();
  //       } else {
  //         $(list).find("li").slideUp();
  //       }
  //
  //       return false;
  //     })
  //   .keyup( function () {
  //       // fire the above change event after every letter
  //       $(this).change();
  //   }).change();
   }

   $('input[name="serialno"]').keyup(function(){
       $.ajax({
       type: "POST",
       url: "{{ route('admin.service.suggesstion') }}",
       data:{'keyword':$(this).val()},
       beforeSend: function(){
           $('input[name="serialno"]').css("background","#FFF url('{{ asset('images/LoaderIcon.gif') }}') no-repeat 165px");
       },
       success: function(data){
           $("#suggesstion-box").show();
           $("#suggesstion-box").html(data);
           $('input[name="serialno"]').css("background","#FFF");
       }
       });
   });


  //ondomready
  $(function () {
    listFilter($("#list"));
  });

  //
  var date = new Date();
  var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $('#customerid').on('change',function(){
    var customerid=$(this).val();
    $.ajax({
          type:'POST',
          url:'{{ route('admin.service.getcustomerphone') }}',
          data:{id:customerid},
          success:function(data){
              $('#phone').val(data.success);
          }
       });
  });

  $("#chargetype-01").on("change", function(){
      if($(this).is(':checked')){
        $('input[name="chargeamount"]').prop( "disabled", false );
        $('input[name="chargeamount"]').focus();
      }else {
        $('input[name="chargeamount"]').prop( "disabled", true );
        $('input[name="chargeamount"]').val('0');
      }

  });

  $("#payment-01").on("change", function(){
      if($(this).is(':checked')){
        $('input[name="paydate"]').prop( "disabled", false );
        $('input[name="paydate"]').focus();

      }else {
        $('input[name="paydate"]').prop( "disabled", true );
        //$('input[name="paydate"]').val();
      }

  });

  $("#ordered-01").on("change", function(){
      if($(this).is(':checked')){
        $('input[name="ordered_comment"]').prop( "disabled", false );
        $('input[name="ordered_comment"]').focus();

      }else {
        $('input[name="ordered_comment"]').prop( "disabled", true );
        $('input[name="ordered_comment"]').val('');
      }

  });


  $('#date').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons",

  });

  $('#buyingdate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

});
</script>

@endsection
