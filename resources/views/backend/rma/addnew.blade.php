@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.newservice.title'))


@push('after-styles')
  <style>
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
                    <h4 class="card-title">@lang('strings.backend.newrma.title')</h4>
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
                          <label for="receiptno" class="col-md-2 form-control-label">RMA <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','rma','Enter RMA')
                                ->class('form-control')
                                ->value('')

                                 }}

                          </div>
                          <label for="date" class="col-md-2 form-control-label">Date<span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','date')
                                ->class('form-control')
                                ->value(old('date',$mytime))
                                ->required()
                                ->autofocus()
                               }}
                          </div>

                      </div>
                      <!-- Row 2 -->
                      <div class="form-group row{{ $errors->has('ordered_supplyer') ? ' has-error' : '' }}">
                        <label for="ordered_supplyer" class="col-md-2 form-control-label">Supplyer</label>
                        <div class="col-md-4">
                          {{ html()->select('ordered_supplyer')
                                   ->class('form-control')
                                   ->options($vendors)
                                  // ->value($service->ordered_supplyer)
                                   ->placeholder('Select Supplyer')
                                   
                                }}
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

    $('input[name="serialno"]')
      .change( function () {
        var filter = $(this).val();
        if(filter) {
          // this finds all links in a list that contain the input,
          // and hide the ones not containing the input while showing the ones that do
          $(list).find("a:not(:Contains(" + filter + "))").parent().slideUp();
          $(list).find("a:Contains(" + filter + ")").parent().slideDown();
        } else {
          $(list).find("li").slideUp();
        }

        return false;
      })
    .keyup( function () {
        // fire the above change event after every letter
        $(this).change();
    }).change();
  }


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
