@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.newpurchase.title'))

@section('content')
@php
$mytime = Carbon\Carbon::now()->format('d/m/Y');
@endphp
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.newrma.title')</h4>
                </div><!--card-header-->
<form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.purchase.store') }}">
                <div class="card-body">

                      {{ csrf_field() }}
                      <div class="form-group row{{ $errors->has('date') ? ' has-error' : '' }}">
                          <label for="date" class="col-md-2 form-control-label">Date <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','date','')
                                ->class('form-control datepicker')
                                ->value(old('date',$mytime))
                                ->required()
                                ->autofocus()
                            }}

                          </div>
                          <label for="rma" class="col-md-2 form-control-label">RMA <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','rma','')
                                ->class('form-control')
                                ->value(old('rma'))
                                ->required()

                            }}
                          </div>
                      </div>
                      <!-- Row 2 -->
                      <div class="form-group row{{ $errors->has('vendor_id') ? ' has-error' : '' }}">
                          <label for="vendor_id" class="col-md-2 form-control-label">Vendor <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->select('vendor_id')
                                     ->class('form-control')
                                     ->options($vendors)
                                     ->value(old('vendor_id'))
                                     ->placeholder('Select Vendor')
                                     ->required()
                                  }}
                          </div>

                          <label for="service_id" class="col-md-2 form-control-label">Service# <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->select('service_id')
                                     ->class('form-control')
                                     ->options($services)
                                     ->value(old('service_id',request('sid')))
                                     ->placeholder('Select Service#')
                                     ->required()
                                  }}
                        </div>



                      </div>

                      <!-- Row 3 -->
                      <div class="form-group row{{ $errors->has('description') ? ' has-error' : '' }}">
                      <label for="note" class="col-md-2 form-control-label">Description <span class="required">*</span></label>
                      <div class="col-md-10">
                        {{ html()->textarea('note',old('note'))
                            ->class('form-control')

                            ->required()

                           }}
                      </div>
                    </div>
                      <div class="form-group row{{ $errors->has('payment') ? ' has-error' : '' }}">
                          <label for="payment" class="col-md-2 form-control-label">Payment </label>
                          <div class="col-md-4">
                            {{ html()->label(
                                    html()->checkbox('payment[]', '0')
                                          ->class('switch-input')
                                          ->id('payment-'. '01')
                                        . '<span class="switch-slider" data-checked="yes"  data-unchecked="no"></span>')
                                    ->class('switch switch-label switch-pill switch-primary mr-2')
                                ->for('payment-'. '01') }}
                          </div>
                          <label for="paydate" class="col-md-2 form-control-label">Pay Date <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','paydate')
                                ->class('form-control datepicker')
                                ->value(old('paydate'))
                                ->disabled()


                               }}
                          </div>

                      </div>
                      @php ($disabled=false)

                      @if($logged_in_user->isServiceEnginner())
                          @php ($disabled=true)
                      @endif
                      <div class="row">
                        <table class="table table-striped table-bordered" width="100%" id="purchase_detail">
                          <thead>
                            <tr>
                              <td><div class="btn-group btn-group-sm" role="group" aria-label="User Actions">
                                      <a href="#" class="btn btn-primary" id="btn_add"><i class="fas fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add"></i></a>
                              </div></td>
                              <th>{{ html()->label('Category')
                                    ->for('category')
                                    ->class('form-control-label') }}</th>
                              <th>{{ html()->label('Item')
                                    ->for('item')
                                    ->class('form-control-label') }}</th>
                              <th>{{ html()->label('Quantity')
                                    ->for('quantity')
                                    ->class('form-control-label') }}</td>
                                <th>{{ html()->label('Purchase Price')
                                      ->for('purchaseprice')
                                      ->class('form-control-label') }}</th>

                            </tr>
                          </thead>
                          <tbody>

                            <tr>
                              <td>
                                <div class="btn-group btn-group-sm" role="group" aria-label="User Actions">
                                			  <a href="#" class="btn btn-danger btn_remove"><i class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="Remove"></i></a>
                                </div>
                              </td>
                              <td>  {{ html()->select('category[]')
                                         ->class('form-control category')
                                         ->options($categorys)
                                         ->value(old('category'))
                                         ->placeholder('Select Category')
                                         ->required()
                                      }}</td>
                                <td>  {{ html()->select('item[]')
                                           ->class('form-control item')
                                           ->options($items)
                                           ->value(old('item'))
                                           ->placeholder('Select Item')
                                           ->required()
                                        }}</td>

                                <td> {{ html()->input('number','quantity[]')
                                    ->class('form-control')
                                    ->value(old('quantity'))
                                    ->required()
                                  }} </td>
                                <td> {{ html()->input('number','purchaseprice[]')
                                    ->class('form-control')
                                    ->value(old('purchaseprice'))
                                    ->disabled($disabled)
                                  }} </td>


                            </tr>
                          </tbody>
                        </table>
                      </div>
                  </div>




                    <!--card-body-->
                    <div class="card-footer clearfix">
                                    <div class="row">
                                        <div class="col">
                                            <a class="btn btn-danger btn-sm" href="{{ route('admin.purchase.index') }}">Cancel</a>
                                        </div><!--col-->

                                        <div class="col text-right">
                                            <button class="btn btn-success btn-sm pull-right" type="submit">Create</button>
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


  $("#service_id").select2( {
   //placeholder: "Select Service#",
   allowClear: true
   } );

  $("#payment-01").on("change", function(){
      if($(this).is(':checked')){
        $('input[name="paydate"]').prop( "disabled", false );
        $('input[name="paydate"]').focus();

      }else {
        $('input[name="paydate"]').prop( "disabled", true );
        //$('input[name="paydate"]').val();
      }

  });


  $('#date').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons",

  });

  $('#paydate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

  var RowNew;


  $(document).on("click",".btn_remove",function(e){
      e.preventDefault();
      var row=tbl_purchase.rows( $(this).parents('tr') );
      row.remove().draw();

  });

  $(document).on("change",'select.category',function(){
      var cur_item= $(this).parents('tr').find('.item');
      var value= $(this).val();
      var _token = $('input[name="_token"]').val();
      $.ajax({
        method: 'post',
        url: '{{ route('admin.category.fetch') }}',
        data:{'_token':_token,'value':value},
        success: function (data) {
          cur_item.html(data);

        }
      })

  });

  $(document).on("click","#btn_add",function(e){
    e.preventDefault();
    tbl_purchase.row.add(RowNew).draw();
//  newrow.appendTo("#purchase_detail tbody");


  });


  $(document).on("change",'input[name="quantity[]"],input[name="unitprice[]"]',function(){
      var price= $(this).closest('tr').find('input[name="unitprice[]"]').val();
      var qty =  $(this).closest('tr').find('input[name="quantity[]"]').val();

      if(!$.isNumeric(price))
          price=0;
      if(!$.isNumeric(qty))
          qty=0;

      $(this).closest('tr').find('input[name="subtotal[]"]').val((price * qty));


  });

  var tbl_purchase=$('#purchase_detail').DataTable({
      "info":false,
      "paging":false,
      "ordering": false,
      "searching":false,

  });

RowNew=tbl_purchase.row(0).data();
$('#purchase_detail tbody tr:first').find('.btn_remove').hide();
});
</script>

@endsection
