@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.editservice.title'))


@section('content')
@push('after-styles')
<style>
  .dz-image img{width: 100%;}
</style>
@endpush

@php ($disabled=false)

@if($logged_in_user->isServiceReceptionist() || $logged_in_user->isServiceEnginner())
    @php ($disabled=true)
@endif


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.editservice.title')</h4>
                </div><!--card-header-->
<form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.service.update',['id'=>$service->id]) }}" enctype="multipart/form-data">
                <div class="card-body">

                      {{ csrf_field() }}
                      <input type="hidden" name="_method" value="PATCH">
                      <div class="row">

                        <div class="col">

                          <h5 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> General Information</h5>
                        </div>

                      </div>
                    <div class="row border">
                      <div class="col">
                        <br>
                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="receiptno" class="col-md-2 form-control-label">Receipt No <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','receiptno','auto generate')
                                ->class('form-control')
                                ->value($service->receiptno)
                                ->disabled()
                            }}

                          </div>
                          <label for="assignto" class="col-md-2 form-control-label">Assign To </label>
                          <div class="col-md-4">
                              {{ html()->select('assignto','Select enginner')
                                       ->class('form-control')
                                       ->attribute((($disabled)?"disabled":""))
                                       ->value($service->assignto)
                                       ->options($user_enginners)

                                    }}

                          </div>
                      </div>

                      <!-- Row 2 -->
                      <div class="form-group row{{ $errors->has('date') ? ' has-error' : '' }}">
                          <label for="date" class="col-md-2 form-control-label">Date in <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','date')
                                ->class('form-control datepicker')
                                ->value(old('date',$service->date))
                                ->required()

                                ->disabled($disabled)
                               }}
                          </div>
                          <label for="warrenty" class="col-md-2 form-control-label">Warrenty</label>
                          <div class="col-md-4">
                            {{ html()->label(
                                    html()->checkbox('warrenty[]', $service->warrenty)
                                          ->disabled($disabled)
                                          ->class('switch-input')
                                          ->id('warrenty-'. '01')
                                        . '<span class="switch-slider" data-checked="in" data-unchecked="out"></span>')
                                    ->class('switch switch-label switch-pill switch-primary mr-2')

                                ->for('warrenty-'. '01') }}

                          </div>
                          @if($disabled)
                            <input type="hidden" name="warrenty" value="{{ $service->warrenty }}">
                          @endif
                      </div>
                      <!-- Row 3 -->
                      <div class="form-group row{{ $errors->has('buyingdate') ? ' has-error' : '' }}">
                          <label for="buyingdate" class="col-md-2 form-control-label">Buying Date </label>
                          <div class="col-md-4">
                              {{ html()->input('text','buyingdate')
                                  ->class('form-control')
                                  ->value($service->buyingdate)
                                  ->disabled($disabled)
                                 }}
                          </div>
                          <label for="status" class="col-md-2 form-control-label">Status <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->select('status')
                                     ->class('form-control')
                                     ->options($status)
                                     ->value($service->status)
                                     ->placeholder('Select status')
                                     ->required()
                                  }}
                          </div>
                      </div>
                      <!-- Row 4 -->
                      <div class="form-group row{{ $errors->has('serialno') ? ' has-error' : '' }}">
                          <label for="serialno" class="col-md-2 form-control-label">Serial No <span class="required">*</span></label>
                          <div class="col-md-4">
                              {{ html()->input('text','serialno')
                                  ->class('form-control')
                                  ->value($service->serialno)
                                  ->required()
                                  
                                 }}
                          </div>
                          <label for="model" class="col-md-2 form-control-label">Model <span class="required">*</span></label>
                          <div class="col-md-4">
                              {{ html()->input('text','model')
                                  ->class('form-control')
                                  ->value($service->model)
                                  ->required()
                                  ->disabled($disabled)
                                 }}
                          </div>
                      </div>
                      <!-- Row 5 -->
                      @if($logged_in_user->isServiceEnginner())
                          @php ($disabled=false)
                      @endif
                      <div class="form-group row{{ $errors->has('chargetype') ? ' has-error' : '' }}">
                          <label for="chargetype" class="col-md-2 form-control-label">Charge Type </label>
                          <div class="col-md-4">
                            {{ html()->label(
                                    html()->checkbox('chargetype[]', (($service->chargetype=='free')?0:1))
                                          ->class('switch-input')
                                          ->disabled($disabled)
                                          ->id('chargetype-'. '01')
                                        . '<span class="switch-slider" data-checked="yes"  data-unchecked="no"></span>')
                                    ->class('switch switch-label switch-pill switch-primary mr-2')
                                ->for('chargetype-'. '01') }}
                                @if($disabled)
                                  <input type="hidden" name="chargetype" value="{{ (($service->chargetype=='free')?0:1) }}">
                                @endif
                          </div>
                          <label for="chargeamount" class="col-md-2 form-control-label">Amount <span class="required">*</span></label>
                          <div class="col-md-4">
                            @if($service->chargetype=='free')
                              @php ($status_amount=true)
                            @else
                              @php ($status_amount=false)
                            @endif

                              @php (
                                $display=html()->input('number','chargeamount',0)
                                ->class('form-control')
                                ->value($service->chargeamount)
                                ->required())

                                {{ (($status_amount || $disabled)?$display->readonly():$display) }}


                          </div>
                      </div>
                      <!-- Row 6 -->
                      @if($logged_in_user->isServiceEnginner())
                          @php ($disabled=true)
                      @endif
                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="problem" class="col-md-2 form-control-label">Problem <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->select('problem')
                                     ->class('form-control')
                                     ->options($problems)
                                     ->value($service->problem()->pluck('problems.id')->implode(' '))
                                     ->placeholder('Select problem')
                                     ->required()
                                     ->attribute((($disabled)?"disabled":""))
                                  }}
                          </div>

                          @if($logged_in_user->isServiceReceptionist())
                              {{ $disabled=false }}
                          @endif

                          <label for="description" class="col-md-2 form-control-label">Description <span class="required">*</span></label>
                          <div class="col-md-4">
                              <textarea {{ (($disabled)?"disabled":"") }} id="description" class="form-control col-md-12 col-xs-12" name="description" required>{{ $service->description }}</textarea>
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
                                   ->value($service->customerid)
                                   ->placeholder('Select customer')
                                   ->required()
                                   ->attribute((($disabled)?"disabled":""))
                                }}
                        </div>
                        <label for="phone" class="col-md-2 form-control-label">Phone <span class="required">*</span></label>
                        <div class="col-md-4">
                          {{ html()->input('text','phone',0)
                              ->class('form-control')
                              ->value($service->phone)
                              ->required()
                              ->disabled($disabled)
                            }}
                        </div>
                      </div>
                    </div>
                    </div>
                    <div class="row">

                      <div class="col">
                        <br>
                        <h5 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> Customer Payment</h5>
                      </div>

                    </div>
                      <!-- Row 8 -->


                      <div class="row border">
                        <div class="col">
                          <br>
                      <div class="form-group row{{ $errors->has('payment') ? ' has-error' : '' }}">
                        <label for="payment" class="col-md-2 form-control-label">Payment </label>
                        <div class="col-md-4">
                          {{ html()->label(
                                  html()->checkbox('payment[]', $service->payment)
                                        ->class('switch-input')
                                        ->disabled($disabled)
                                        ->id('payment-'. '01')
                                      . '<span class="switch-slider" data-checked="yes"  data-unchecked="no"></span>')
                                  ->class('switch switch-label switch-pill switch-primary mr-2')
                              ->for('payment-'. '01') }}
                        </div>
                        @if($disabled)
                          <input type="hidden" name="payment" value="{{ $service->payment }}">
                        @endif
                        <label for="paydate" class="col-md-2 form-control-label">Paid Date</label>
                        <div class="col-md-4">
                          @if($service->payment==0)
                            @php ($status_pay=true)
                          @else
                            @php ($status_pay=false)
                          @endif
                          {{ html()->input('text','paydate',0)
                              ->class('form-control')
                              ->value($service->paydate)
                              ->disabled($status_pay || $disabled)

                            }}
                        </div>
                      </div>
                    </div>
                  </div>

                      <!-- Row 9 -->
                      @if($logged_in_user->isServiceReceptionist())
                          @php ($disabled=true)
                      @endif

                      @if($logged_in_user->isServiceEnginner())
                          @php ($disabled=false)
                      @endif

                      <div class="row">

                        <div class="col">
                          <br>
                          <h5 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> Enginner Solution</h5>
                        </div>

                      </div>
                      <div class="row border">
                        <div class="col">
                          <br>
                      <div class="form-group row{{ $errors->has('ordered') ? ' has-error' : '' }}">
                        <label for="ordered" class="col-md-2 form-control-label">Order </label>
                        <div class="col-md-4">
                          {{ html()->label(
                                  html()->checkbox('ordered[]', old('ordered',$service->ordered))
                                        ->class('switch-input')
                                        ->disabled($disabled)
                                        ->id('ordered-'. '01')
                                      . '<span class="switch-slider" data-checked="yes"  data-unchecked="no"></span>')
                                  ->class('switch switch-label switch-pill switch-primary mr-2')
                              ->for('ordered-'. '01') }}
                        </div>
                        @if($disabled)
                          <input type="hidden" name="ordered" value="{{ $service->ordered }}">
                        @endif
                        <label for="ordered_comment" class="col-md-2 form-control-label">Comment</label>
                        <div class="col-md-4">
                          @if(empty(old('ordered')) && $service->ordered==0)
                            @php ($status_order=true)
                          @else
                            @php ($status_order=false)
                          @endif
                          {{ html()->input('text','ordered_comment',0)
                              ->class('form-control')
                              ->value(old('ordered_comment',$service->ordered_comment))
                              ->disabled($status_order || $disabled)

                            }}
                        </div>
                      </div>


                        <div class="form-group row{{ $errors->has('solution') ? ' has-error' : '' }}">
                          <label for="solution" class="col-md-2 form-control-label">Solution</label>
                          <div class="col-md-10">

                            {{ html()->textarea('solution',old('solution',$service->solution))
                                ->attribute((($disabled)?"disabled":""))
                                ->class('form-control')
                              }}
                          </div>
                      </div>
                    </div>
                  </div>
                      <!-- Row 10 -->
                      @if($logged_in_user->isServiceEnginner())
                          @php ($disabled=true)
                      @endif

                      <div class="row">

                        <div class="col">
                          <br>
                          <h5 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> Transfer to Vendor</h5>
                        </div>

                      </div>
                      <div class="row border transfer_vendor">
                        <div class="col">
                          <br>
                      <div class="form-group row{{ $errors->has('destination') ? ' has-error' : '' }}">
                        <label for="destination" class="col-md-2 form-control-label">Vendor</label>
                        <div class="col-md-4">
                          {{ html()->select('destination')
                                   ->class('form-control')
                                   ->options($vendors)
                                   ->value($service->destination)
                                   ->placeholder('Select Vendor')
                                   ->attribute((($disabled)?"disabled":""))
                                }}
                        </div>
                        <label for="transferdate" class="col-md-2 form-control-label">Transfer Date</label>
                        <div class="col-md-4">
                          @if(empty($service->destination))
                            @php ($status_transfer=true)
                          @else
                            @php ($status_transfer=false)
                          @endif
                          {{ html()->input('date','transferdate')
                              ->class('form-control datepicker')
                              ->value($service->transferdate)
                              ->disabled($status_transfer || $disabled)
                              ->required()



                            }}
                        </div>
                      </div>
                      <div class="form-group row{{ $errors->has('returndate') ? ' has-error' : '' }}">
                        <label for="returndate" class="col-md-2 form-control-label">Return Date</label>
                        <div class="col-md-4">
                          @if(empty($service->destination))
                            @php ($status_return=true)
                          @else
                            @php ($status_return=false)
                          @endif

                          {{ html()->input('date','returndate')
                              ->class('form-control')
                              ->value($service->returndate)
                              ->disabled($status_return || $disabled)

                            }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">

                    <div class="col">
                      <br>
                      <h5 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> Picture</h5>
                    </div>
                  </div>
                  <div class="row border">
                <div class="col">
                  <div class="form-group {{ $errors->has('logo') ? 'has-error' : '' }}">
                      <label for="logo">Picture</label>
                      <div class="needsclick dropzone" id="logo-dropzone">
                      </div>
                  </div>
                </div>
                    </div><!--card-body-->
                    <div class="card-footer clearfix">
                                    <div class="row">
                                        <div class="col">
                                            <a class="btn btn-danger btn-sm" href="{{ route('admin.service.index') }}">Cancel</a>
                                        </div><!--col-->

                                        <div class="col text-right">
                                            <button class="btn btn-success btn-sm pull-right" type="submit">Update</button>
                                        </div><!--col-->
                                    </div><!--row-->
                                </div><!--card-footer-->

                  </form>

                </div>
            </div><!--card-->
        </div><!--col-->
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
        $('input[name="chargeamount"]').prop( "readonly", false );
        $('input[name="chargeamount"]').focus();
      }else {
        $('input[name="chargeamount"]').prop( "readonly", true );
      //  $('input[name="chargeamount"]').val('0');
      }

  });

  $("#payment-01").on("change", function(){
      $('input[name="paydate"]').attr('readonly','true');
      if($(this).is(':checked')){
        $('input[name="paydate"]').prop( "disabled", false );
        $('input[name="paydate"]').val("{{ (date('d/m/Y')) }}");

      }else {
        $('input[name="paydate"]').prop( "disabled", true );
        $('input[name="paydate"]').val("{{ (date('d/m/Y')) }}");
      }

  });

  $("#warrenty-01").on("change", function(){
      if($(this).is(':checked')){
        $('.transfer_vendor').find("input, button, submit, textarea, select").prop( "disabled", false );

      }else {
        $('.transfer_vendor').find("input, button, submit, textarea, select").prop( "disabled", true );
      }

      //$('#destination').trigger('change');
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
            iconsLibrary:"materialicons"
  });


  $('#buyingdate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

/*
  $('#paydate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });
  */
/*
  $('#returndate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons",

  });

  $('#transferdate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });
*/
  var old_datereturn='{{ $service->returndate }}';
  var old_datetransfer='{{ $service->transferdate }}';
  $("#destination").on("change", function(){
      if($(this).val()!=''){
        $('input[name="transferdate"]').prop( "disabled", false );
        $('input[name="returndate"]').prop( "disabled", false );
        $('input[name="transferdate"]').focus();

        $('input[name="transferdate"]').val(old_datetransfer);
        $('input[name="returndate"]').val(old_datereturn);


      }else {
        $('input[name="transferdate"]').prop( "disabled", true );
        old_datetransfer=$('input[name="transferdate"]').val();

        $('input[name="transferdate"]').val('');
        $('input[name="returndate"]').prop( "disabled", true );
        old_datereturn=$('input[name="returndate"]').val();
        $('input[name="returndate"]').val('');
      }

  });

});
</script>
@endsection
