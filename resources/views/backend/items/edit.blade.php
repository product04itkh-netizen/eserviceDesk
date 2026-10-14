@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.edititem.title'))

@section('content')
@php
$mytime = Carbon\Carbon::now()->format('d/m/Y');
@endphp
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.edititem.title')</h4>
                </div><!--card-header-->
<form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.item.update',['id'=>$item->id]) }}">
                <div class="card-body">

                      {{ csrf_field() }}
                      <input type="hidden" name="_method" value="PATCH">
                      <div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
                          <label for="name" class="col-md-2 form-control-label">Name <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','name','')
                                ->class('form-control')
                                ->value(old('name',$item->name))
                                ->required()
                                ->autofocus()
                            }}

                          </div>
                          <label for="category_id" class="col-md-2 form-control-label">Category <span class="required">*</span></label>
                          <div class="col-md-4">
                              {{ html()->select('category_id')
                                       ->class('form-control')
                                       ->value(old('category_id',$item->category_id))
                                       ->options($categorys)
                                       ->required()
                                       ->placeholder('Select category')
                                    }}

                          </div>
                      </div>
                      <!-- Row 2 -->
                      <div class="form-group row{{ $errors->has('saleprice') ? ' has-error' : '' }}">
                          <label for="saleprice" class="col-md-2 form-control-label">Sale Price ($)<span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('number','saleprice',old('saleprice',$item->saleprice))
                                ->class('form-control')
                                ->attribute('step','any')
                                ->required()

                               }}
                          </div>
                          <label for="description" class="col-md-2 form-control-label">Description <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->textarea('description',old('description',$item->description))
                                ->class('form-control')
                                ->required()

                               }}
                          </div>

                      </div>
                  </div>
                    <!--card-body-->
                    <div class="card-footer clearfix">
                                    <div class="row">
                                        <div class="col">
                                            <a class="btn btn-danger btn-sm" href="{{ route('admin.item.index') }}">Cancel</a>
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
$(document).ready(function(){
  var date = new Date();
  var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

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
