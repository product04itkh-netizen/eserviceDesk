@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.editpurchase.title'))

@section('content')
@php
$mytime = Carbon\Carbon::now()->format('d/m/Y');
@endphp
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.editrma.title')</h4>
                </div><!--card-header-->
<form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.purchase.update',['id'=>$purchase->id]) }}">
                <div class="card-body">

                      {{ csrf_field() }}
                      <input type="hidden" name="_method" value="PATCH">
                      @if(request('service')>0)
                          <input type="hidden" name="service" value="{{ request('service') }}">
                      @endif
                      <div class="form-group row{{ $errors->has('date') ? ' has-error' : '' }}">
                          <label for="date" class="col-md-2 form-control-label">Date <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','date','')
                                ->class('form-control datepicker')
                                ->value(old('date',$purchase->date))
                                ->required()
                                ->autofocus()
                            }}

                          </div>
                          <label for="rma" class="col-md-2 form-control-label">RMA <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','rma','')
                                ->class('form-control')
                                ->value(old('rma',$purchase->rma))
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
                                     ->value(old('vendor_id',$purchase->vendor_id))
                                     ->placeholder('Select Vendor')
                                     ->required()
                                  }}
                          </div>
                          <label for="service_id" class="col-md-2 form-control-label">Service# <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->select('service_id')
                                     ->class('form-control')
                                     ->options($services)
                                     ->value(old('service_id',$purchase->service_id))
                                     ->placeholder('Select Service#')
                                     ->required()
                                  }}
                        </div>



                      </div>

                      <!-- Row 3 -->
                      <div class="form-group row{{ $errors->has('description') ? ' has-error' : '' }}">
                      <label for="note" class="col-md-2 form-control-label">Description <span class="required">*</span></label>
                      <div class="col-md-10">
                        {{ html()->textarea('note',old('note',$purchase->note))
                            ->class('form-control')
                            ->required()

                           }}
                      </div>
                    </div>
                    <!-- Row 3 -->
                      <div class="form-group row{{ $errors->has('payment') ? ' has-error' : '' }}">
                          <label for="payment" class="col-md-2 form-control-label">Payment </label>
                          <div class="col-md-4">
                            {{ html()->label(
                                    html()->checkbox('payment[]', $purchase->payment)
                                          ->class('switch-input')
                                          ->id('payment-'. '01')
                                        . '<span class="switch-slider" data-checked="yes"  data-unchecked="no"></span>')
                                    ->class('switch switch-label switch-pill switch-primary mr-2')
                                ->for('payment-'. '01') }}
                          </div>
                          <label for="paydate" class="col-md-2 form-control-label">Pay Date <span class="required">*</span></label>
                          <div class="col-md-4">
                            @php
                            if($purchase->payment==1)
                                $disabled=false;
                            else
                                $disabled=true;
                            @endphp
                            {{ html()->input('text','paydate')
                                ->class('form-control datepicker')
                                ->value(old('paydate',$purchase->paydate))
                                ->attribute((($disabled)?"disabled":""))


                               }}
                          </div>

                      </div>
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
                                <th>{{ html()->label('Price')
                                      ->for('unitprice')
                                      ->class('form-control-label') }}</th>
                                <th>{{ html()->label('Sub Total')
                                      ->for('subtotal')
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
                              <td>  {{ html()->select('category[]','ALL')
                                         ->class('form-control category')
                                         ->options($categorys)
                                         ->value(old('category'))
                                         ->placeholder('Select Category')
                                          ->required()
                                      }}</td>
                                <td>  {{ html()->select('item[]')
                                           ->class('form-control item')

                                           ->value(old('item'))
                                           ->placeholder('Select Item')
                                           ->required()
                                        }}</td>
                                <td> {{ html()->input('number','quantity[]')
                                    ->class('form-control')
                                    ->value(old('quantity'))
                                    ->required()
                                  }} </td>
                                <td> {{ html()->input('number','unitprice[]')
                                    ->class('form-control')
                                    ->value(old('unitprice'))
                                    ->required()
                                  }} </td>
                                <td> {{ html()->input('number','subtotal[]')
                                    ->class('form-control')
                                    ->value(old('subtotal'))
                                  }} </td>

                            </tr>
                            @foreach ($purchase->purchase_detail as $purchase_detail)
                              <tr>
                                <td>
                                  <div class="btn-group btn-group-sm" role="group" aria-label="User Actions">
                                  			  <a href="#" class="btn btn-danger btn_remove"><i class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="Remove"></i></a>
                                  </div>
                                </td>
                                <td>  {{ html()->select('category[]','ALL')
                                           ->class('form-control category')
                                           ->options($categorys)
                                           ->value(old('category',0)) //$purchase_detail->category_id
                                           ->placeholder('Select Category')
                                           ->required()
                                        }}</td>
                                  <td>  {{ html()->select('item[]')
                                             ->class('form-control item')
                                             ->options($items)
                                             ->value(old('item',$purchase_detail->item_id))
                                             ->placeholder('Select Item')
                                             ->required()
                                          }}</td>
                                  <td> {{ html()->input('number','quantity[]')
                                      ->class('form-control')
                                      ->value(old('quantity',$purchase_detail->quantity))
                                       ->required()
                                    }} </td>
                                  <td> {{ html()->input('number','unitprice[]')
                                      ->class('form-control')
                                      ->value(old('unitprice',$purchase_detail->purchaseprice))
                                       ->required()
                                    }} </td>
                                  <td> {{ html()->input('number','subtotal[]')
                                      ->class('form-control')
                                      ->value(old('subtotal',($purchase_detail->quantity * $purchase_detail->purchaseprice)))

                                    }} </td>
                              </tr>
                            @endforeach
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
      var rows=tbl_purchase.rows().count();
    //  console.log(rows);
        if(rows<2)
          $('#purchase_detail tbody tr:eq(1)').find('.btn_remove').hide();

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

  //$(document).find('select.category').trigger('change');

  $(document).on("click","#btn_add",function(e){
    e.preventDefault();
    //RowNew.node().find('input[name="quantity[]"]').attr('required','');
    tbl_purchase.row.add(RowNew).draw();
    var rows=tbl_purchase.rows().count();
      //$(tbl_purchase.row(rows-1).node()).find('select,input').attr('required','');
      if(rows>=2)
        $('#purchase_detail tbody tr:eq(1)').find('.btn_remove').show();
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
//tbl_purchase.row(0).node().find('input[name="quantity[]"]').attr('required','')
RowNew=tbl_purchase.row(0).data();

tbl_purchase.rows(0).remove().draw();

$('#purchase_detail tbody tr:first').find('.btn_remove').hide();
});
</script>

@endsection
