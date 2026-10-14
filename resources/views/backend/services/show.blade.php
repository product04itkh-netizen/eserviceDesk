@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.viewservice.title'))

@push('after-styles')
<style>
#tabs {
  overflow: hidden;
  width: 100%;
  margin: 0;
  padding: 0;
  list-style: none;
}

#tabs li {
  float: left;
  margin: 0 .5em 0 0;
}

#tabs a {
  position: relative;
  background: #ddd;
  background-image: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#ddd));
  background-image: -webkit-linear-gradient(top, #fff, #ddd);
  background-image: -moz-linear-gradient(top, #fff, #ddd);
  background-image: -ms-linear-gradient(top, #fff, #ddd);
  background-image: -o-linear-gradient(top, #fff, #ddd);
  background-image: linear-gradient(to bottom, #fff, #ddd);
  padding: .7em 3.5em;
  float: left;
  text-decoration: none;
  color: #444;
  text-shadow: 0 1px 0 rgba(255,255,255,.8);
  -webkit-border-radius: 5px 0 0 0;
  -moz-border-radius: 5px 0 0 0;
  border-radius: 5px 0 0 0;
  -moz-box-shadow: 0 2px 2px rgba(0,0,0,.4);
  -webkit-box-shadow: 0 2px 2px rgba(0,0,0,.4);
  box-shadow: 0 2px 2px rgba(0,0,0,.4);
}

#tabs a:hover,
#tabs a:hover::after,
#tabs a:focus,
#tabs a:focus::after {
  background: #fff;
}

#tabs a:focus {
  outline: 0;
}

#tabs a::after {
  content:'';
  position:absolute;
  z-index: 1;
  top: 0;
  right: -.5em;
  bottom: 0;
  width: 1em;
  background: #ddd;
  background-image: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#ddd));
  background-image: -webkit-linear-gradient(top, #fff, #ddd);
  background-image: -moz-linear-gradient(top, #fff, #ddd);
  background-image: -ms-linear-gradient(top, #fff, #ddd);
  background-image: -o-linear-gradient(top, #fff, #ddd);
  background-image: linear-gradient(to bottom, #fff, #ddd);
  -moz-box-shadow: 2px 2px 2px rgba(0,0,0,.4);
  -webkit-box-shadow: 2px 2px 2px rgba(0,0,0,.4);
  box-shadow: 2px 2px 2px rgba(0,0,0,.4);
  -webkit-transform: skew(10deg);
  -moz-transform: skew(10deg);
  -ms-transform: skew(10deg);
  -o-transform: skew(10deg);
  transform: skew(10deg);
  -webkit-border-radius: 0 5px 0 0;
  -moz-border-radius: 0 5px 0 0;
  border-radius: 0 5px 0 0;
}

#tabs #current a {
  background: #fff;
  z-index: 3;
}

#tabs #current a::after {
  background: #fff;
  z-index: 3;
}

/* ------------------------------------------------- */

#content {
    background: #fff;
    padding: 2em;
    height: 750px;
    position: relative;
    z-index: 2;
    -moz-border-radius: 0 5px 5px 5px;
    -webkit-border-radius: 0 5px 5px 5px;
    border-radius: 0 5px 5px 5px;
    -moz-box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
    -webkit-box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
    box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
}
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col text-left">
                      <h4 class="card-title"><strong>Service No:</strong> {{ $service->receiptno }}</h4>
                    </div>
                    <div class="col text-right">

                          <a class="btn btn-danger btn-bg" href="{{ route('admin.service.index') }}">Back to List</a>
                      @if(Auth()->user()->isServiceAdmin() || Auth()->user()->isServiceReceptionist() || Auth()->user()->isAdmin())
                          <a class="btn btn-info btn-bg" href="{{ route('admin.service.receipt',['id'=>$service->id]) }}">Print Receipt</a>
                          <a class="btn btn-danger btn-bg" href="{{ route('admin.service.create') }}">Add New</a>
                          <a class="btn btn-info btn-bg" href="{{ route('admin.service.invoice',['id'=>$service->id]) }}">Print Invoice</a>
                      @endif

                        @php
                        if(Auth()->user()->isServiceAdmin() || Auth()->user()->isAdmin()){
                          $disabled='';

                        }else {
                          if(Auth()->user()->isServiceEnginner() && Auth()->user()->id==$service->assignto){
                            if(in_array(strtolower($service->status),array('delivered')))
                              $disabled='disabled';
                            else
                              $disabled='';
                          }elseif(Auth()->user()->isServiceAdmin())
                            $disabled='';
                          elseif(Auth()->user()->isServiceReceptionist()){
                              if(in_array(strtolower($service->status),array('delivered','start','not start','pending','progress')))
                                $disabled='disabled';
                              else
                                $disabled='';
                          }else
                            $disabled='disabled';
                          //if(($service->payment=='0') || empty($service->payment)){
/*
                              if(Auth()->user()->isServiceEnginner() && Auth()->user()->id==$service->assignto)
                                $disabled='';
                              elseif(Auth()->user()->isServiceAdmin())
                                $disabled='';
                              elseif(Auth()->user()->isServiceReceptionist())
                                  $disabled='';
                              else
                                $disabled='disabled';
*/

                          //}else
                            //$disabled='disabled';

                        }@endphp

                        <form method="post" action="{{ route('admin.service.edit',[$service]) }}" class="btn" enctype="multipart/form-data">
                          {{ csrf_field() }}
                          <input type="hidden" name="_method" value="PATCH">
                          <button type="submit" class="btn btn-primary {{ $disabled }}">Edit</button>
                        </form>




                    </div><!--col-->
                  </div>
                </div><!--card-header-->
                <div class="card-body">

                <div class="row">
                  <ul id="tabs">
                    <li><a href="#" name="tab1"><h6 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> General Info</h6></a></li>
                    <li><a href="#" name="tab3"><h6 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> Enginner Solution</h6></a></li>
                    <li><a href="#" name="tab6"><h6 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> Shipping</h6></a></li>
                    <li><a href="#" name="tab4"><h6 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> Transfer</h6></a></li>
                    <li><a href="#" name="tab5"><h6 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> History</h6></a></li>
                </ul>

                <div id="content" style="width:100%">
                    <div id="tab1">

                <div class="row border">

                    <div class='col'>
                      <br>
                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="receiptno" class="col-md-2 form-control-label">Receipt No <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->label($service->receiptno)
                                     ->for('receiptno')
                                     ->class('form-control')
                                 }}

                          </div>
                          <label for="assignto" class="col-md-2 form-control-label">Assign To </label>
                          <div class="col-md-4">
                            {{ html()->label($service->enginner_name($service->assignto))
                                     ->for('assignto')
                                     ->class('form-control')
                                 }}


                          </div>
                      </div>
                      <!-- Row 2 -->
                      <div class="form-group row{{ $errors->has('date') ? ' has-error' : '' }}">
                          <label for="date" class="col-md-2 form-control-label">Date in <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->label($service->date)
                                     ->for('date')
                                     ->class('form-control')
                                 }}
                          </div>
                          <label for="warrenty" class="col-md-2 form-control-label">Warrenty</label>
                          <div class="col-md-4">
                            {{ html()->label((($service->warrenty==1)?"In":"Out"))
                                ->for('warrenty')
                                ->class('form-control')
                               }}
                          </div>

                      </div>
                      <!-- Row 3 -->
                      <div class="form-group row{{ $errors->has('buyingdate') ? ' has-error' : '' }}">
                          <label for="buyingdate" class="col-md-2 form-control-label">Buying Date <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->label($service->buyingdate)
                                     ->for('buyingdate')
                                     ->class('form-control')
                                 }}
                          </div>
                          <label for="status" class="col-md-2 form-control-label">Status <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->label(ucfirst($service->status))
                              ->for('status')
                              ->class('form-control')
                            }}
                          </div>
                      </div>
                      <!-- Row 4 -->
                      <div class="form-group row{{ $errors->has('serialno') ? ' has-error' : '' }}">
                          <label for="serialno" class="col-md-2 form-control-label">Serial No <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->label(($service->serialno))
                              ->for('serialno')
                              ->class('form-control')
                            }}
                          </div>
                          <label for="model" class="col-md-2 form-control-label">Model <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->label(($service->model))
                              ->for('model')
                              ->class('form-control')
                            }}
                          </div>
                      </div>
                      <!-- Row 5 -->
                      <div class="form-group row{{ $errors->has('chargetype') ? ' has-error' : '' }}">
                          <label for="chargetype" class="col-md-2 form-control-label">Charge Type </label>
                          <div class="col-md-1">
                            {{ html()->label(ucfirst($service->chargetype))
                              ->for('chragetype')
                              ->class('form-control')
                            }}
                          </div>
                          <label for="chargeamount" class="col-md-1 form-control-label">Amount <span class="required">*</span></label>
                          <div class="col-md-2">
                            {{ html()->label('' . ($service->chargeamount))
                              ->for('chargeamount')
                              ->class('form-control')

                            }}
                          </div>
                          <label for="brand" class="col-md-2 form-control-label">Brand <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->label($service->brand()->pluck('name')->implode(' '))
                              ->for('chargeamount')
                              ->class('form-control')

                            }}
                          </div>
                      </div>
                      <!-- Row 6 -->
                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="problem" class="col-md-2 form-control-label">Problem <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->label($service->problem()->pluck('problem')->implode(' '))
                              ->for('problem')
                              ->class('form-control')
                            }}
                          </div>
                          <label for="description" class="col-md-2 form-control-label">Description <span class="required">*</span></label>
                          <div class="col-md-4">
                           <div class="form-control" style="height:auto">{!! nl2br(e($service->description)) !!})) </div>

                          </div>
                      </div>

                      <div class="form-group row{{ $errors->has('customerid') ? ' has-error' : '' }}">
                        <label for="customerid" class="col-md-2 form-control-label">Customer <span class="required">*</span></label>
                        <div class="col-md-4">
                          {{ html()->label($service->customer_name($service->customerid))
                            ->for('customerid')
                            ->class('form-control')
                          }}
                        </div>
                        <label for="phone" class="col-md-2 form-control-label">Phone <span class="required">*</span></label>
                        <div class="col-md-4">
                          {{ html()->label($service->phone)
                            ->for('phone')
                            ->class('form-control')
                          }}
                        </div>
                      </div>

                      <!-- Row 8 -->
                      <div class="form-group row{{ $errors->has('payment') ? ' has-error' : '' }}">
                        <label for="payment" class="col-md-2 form-control-label">Payment </label>
                          <div class="col-md-1">
                        {{ html()->label((($service->payment==0)?"No":"Yes"))
                            ->for('payment')
                            ->class('form-control')
                          }}
                        </div>
                        <label for="transfer" class="col-md-2 form-control-label">Transfer HQ</label>
                        <div class="col-md-1">
                          {{ html()->label((($service->transfer_hq==0)?"No":"Yes"))
                              ->for('transfer_hq')
                              ->class('form-control')
                            }}
                        </div>
                        <label for="paydate" class="col-md-2 form-control-label">Paid Date</label>
                        <div class="col-md-4">
                          {{ html()->label($service->paydate)
                            ->for('paydate')
                            ->class('form-control')
                          }}
                        </div>
                      </div>

                      <div class="row border">
                        <div class="col">
                          <br>
                          @if($service->logo)
                              <a href="{{ $service->logo->getUrl() }}" title="Image Zoom" data-spzoom>
                                  <img src="{{ $service->logo->getUrl() }}" height="150px">
                              </a>
                          @endif
                          <p>&nbsp;</p>
                      </div>

                      </div>

                    </div>
                  </div>


            </div> <!-- end of tab1 -->
                      <!-- Row 7 -->

                <div id="tab3">
                      <div class="row border">
                        <div class="col">
                          <br>
                      <div class="form-group row{{ $errors->has('solution') ? ' has-error' : '' }}">

                        <label for="solution" class="col-md-2 form-control-label">Solution</label>
                        <div class="col-md-4">

                          {{ html()->label($service->solution)
                            ->for('solution')
                            ->class('form-control')
                          }}
                        </div>
                        <label for="ordered_comment" class="col-md-2 form-control-label">Comment</label>
                        <div class="col-md-4">
                          {{ html()->label($service->ordered_comment)
                            ->for('ordered_comment')
                            ->class('form-control')
                          }}
                        </div>

                      </div>
                      <div class="form-group row{{ $errors->has('ordered') ? ' has-error' : '' }}">
                        <label for="ordered" class="col-md-2 form-control-label">Order </label>
                        <div class="col-md-4">
                          {{ html()->label((($service->ordered==0)?"No order":"Ordered"))
                            ->for('ordered')
                            ->class('form-control')
                          }}
                        </div>
                        <label for="rmacode" class="col-md-2 form-control-label">RMA Code</label>
                        <div class="col-md-4">
                          {{ html()->label((empty($service->rmacodes)?'':$service->rmacodes->code))
                            ->for('rmacode')
                            ->class('form-control')
                          }}
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="rmano" class="col-md-2 form-control-label">RMA #</label>
                        <div class="col-md-4">
                          {{ html()->label($service->rmano)
                            ->for('rmano')
                            ->class('form-control')
                          }}
                        </div>
                        <label for="rmadate" class="col-md-2 form-control-label">RMA Date</label>
                        <div class="col-md-4">
                          {{ html()->label($service->rmadate)
                            ->for('rmadate')
                            ->class('form-control')
                          }}
                        </div>
                      </div>

                      <table class="table table-striped table-bordered" width="100%" id="purchase_detail">
                        <thead>
                          <tr>


                            <th>{{ html()->label('Item')
                                  ->for('item')
                                  ->class('form-control-label') }}</th>
                            <th>{{ html()->label('Quantity')
                                  ->for('quantity')
                                  ->class('form-control-label') }}</td>
                              <th>{{ html()->label('Purchase Price')
                                    ->for('purchaseprice')
                                    ->class('form-control-label') }}</th>
                              <th>{{ html()->label('Sub Total')
                                    ->for('subtotal')
                                    ->class('form-control-label') }}</th>
                              <th>{{ html()->label('Selling Price')
                                    ->for('sellingprice')
                                    ->class('form-control-label') }}</th>
                              <th>{{ html()->label('Paid')
                                    ->for('payament')
                                    ->class('form-control-label') }}</th>
                              <th>{{ html()->label('Pay Date')
                                    ->for('Pay Date')
                                    ->class('form-control-label') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($service->service_rma as $rma_detail)
                            <tr>


                                <td> {{ html()->label($rma_detail->item_name)
                                              ->for('item_name')
                                        }}
                                      </td>
                                <td> {{ html()->label('' . $rma_detail->quantity)
                                              ->for('quantity')
                                    }}
                                    </td>
                                <td> {{ html()->label('' . $rma_detail->purchaseprice)
                                              ->for('purchaseprice')
                                        }}
                                </td>
                                <td> {{ html()->label('' . ($rma_detail->purchaseprice*$rma_detail->quantity))
                                              ->for('subtotal')
                                        }}
                                </td>
                                <td> {{ html()->label('' . $rma_detail->sellingprice)
                                              ->for('sellingprice')
                                        }}
                                </td>
                                <td> {{ html()->label('' . (($rma_detail->payment==1)?'Yes':'No'))
                                              ->for('payment')
                                        }}
                                </td>
                                <td> {{ html()->label('' . ($rma_detail->paydate))
                                              ->for('paydate')
                                        }}
                                </td>
                            </tr>
                          @endforeach

                        </tbody>
                      </table>
                      <!-- Row 10 -->

                      <!-- Row 11 -->

                    </div>
                  </div>
                </div> <!-- end of tab3 -->
                <div id="tab4">
                  <div class="row border">
                    <div class="col">
                      <br>
                  <div class="form-group row{{ $errors->has('transferno') ? ' has-error' : '' }}">
                  <label for="transferno" class="col-md-2 form-control-label">Transfer No</label>
                  <div class="col-md-2">
                    {{ html()->label(count($service->transfer)==0?'':'' . $service->transfer->first()->id)
                      ->for('transferno')
                      ->class('form-control')
                    }}
                  </div>
                  <label for="transferdate" class="col-md-2 form-control-label">Transfer Type</label>
                  <div class="col-md-2">
                    {{ html()->label(count($service->transfer)==0?'':$service->transfer->first()->type)
                      ->for('transferdate')
                      ->class('form-control')
                    }}
                  </div>

                  <label for="transferdate" class="col-md-2 form-control-label">Vendor</label>
                  <div class="col-md-2">
                    {{ html()->label(count($service->transfer)==0?'':$service->vendor_name($service->transfer->first()->vendor_id))
                      ->for('transferdate')
                      ->class('form-control')
                    }}
                  </div>

                </div>
                  <div class="form-group row{{ $errors->has('detination') ? ' has-error' : '' }}">
                    <label for="transferdate" class="col-md-2 form-control-label">Transfer Date</label>
                    <div class="col-md-2">
                      {{ html()->label(count($service->transfer)==0?'':$service->transfer->first()->transferdate)
                        ->for('transferdate')
                        ->class('form-control')
                      }}
                    </div>
                  <label for="transferdate" class="col-md-2 form-control-label">Return Date</label>
                  <div class="col-md-2">
                    {{ html()->label(count($service->transfer)==0?'':$service->transfer->first()->returndate)
                      ->for('transferdate')
                      ->class('form-control')
                    }}
                  </div>
                  <label for="returndate" class="col-md-2 form-control-label">Clear Date</label>
                  <div class="col-md-2">
                    {{ html()->label(count($service->transfer)==0?'':$service->transfer->first()->cleardate)
                      ->for('returndate')
                      ->class('form-control')
                    }}
                  </div>
                </div>


              <div class="form-group row{{ $errors->has('credit_note_reason') ? ' has-error' : '' }}">
                <label for="credit_note_reason" class="col-md-2 form-control-label">Reason</label>
                <div class="col-md-10">
                  {{ html()->label(count($service->transfer)==0?'':$service->transfer->first()->reason)
                    ->for('credit_note_reason')
                    ->class('form-control')
                  }}
                </div>


              </div>

                </div>
              </div>

                  </div>
                  <div id="tab5">
                    <div class="row border">
                      <div class="col">
                        <br>
                        <table class="table" width="100%">
                            <thead>
                              <tr>
                                  <th>Date</th>
                                  <th>User </th>
                                  <th>Status</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($service_history as $sh)
                                <tr>
                                  <td>{{ $sh->created_at->format('d/m/Y H:i') }}</td>
                                  <td>{{ $sh->user->first_name }} {{ $sh->user->last_name }}</td>
                                  <td>{{ ucfirst($sh->status) }}</td>

                                </tr>
                              @endforeach

                            </tbody>
                        </table>
                      </div>
                  </div>
                </div>

                <div id="tab6">


                  <div class="row border">
                    <div class="col">
                      <br>

                  <div class="form-group row{{ $errors->has('shipreference') ? ' has-error' : '' }}">
                  <label for="shippingno" class="col-md-2 form-control-label">Shipping No </label>
                  <div class="col-md-4">
                    {{ html()->label('' . (count($service->shipping)==0?'':$service->shipping->first()->id))
                        ->class('form-control')
                        ->for('shippingno') }}
                  </div>
                  <label for="shipreference" class="col-md-2 form-control-label">Ship Reference </label>
                  <div class="col-md-4">
                    {{ html()->label((count($service->shipping)==0?'':$service->shipping->first()->shipreference))
                        ->class('form-control')
                        ->for('shipreference') }}
                  </div>
                </div>
                <div class="form-group row{{ $errors->has('shipdate') ? ' has-error' : '' }}">
                  <label for="shipdate" class="col-md-2 form-control-label">Ship Date *</label>
                  <div class="col-md-4">

                    {{ html()->label((count($service->shipping)==0?'':$service->shipping->first()->shipdate))
                        ->class('form-control')
                        ->for('shipdate') }}

                </div>

                <label for="shipcost" class="col-md-2 form-control-label">Ship Costing *</label>
                <div class="col-md-4">
                  {{ html()->label((count($service->shipping)==0?'':'' . $service->shipping->first()->shipcost))
                      ->class('form-control')
                      ->for('shipcost') }}

              </div>
              </div>

              <br>
              </div>
            </div>
            </div>
                    </div><!--card-body-->


                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection
@section('custom_script')
<script>
$("#content").find("[id^='tab']").hide(); // Hide all content

$("#tabs li:first").attr("id","current"); // Activate the first tab
$("#content #tab1").fadeIn(); // Show first tab's content

$('#tabs a').click(function(e) {
    e.preventDefault();

    if ($(this).closest("li").attr("id") == "current"){ //detection for current tab
     return;
    }
    else{
      $("#content").find("[id^='tab']").hide(); // Hide all content
      $("#tabs li").attr("id",""); //Reset id's
      $(this).parent().attr("id","current"); // Activate this

      $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
    }
});

  $('[data-spzoom]').spzoom();
</script>
@endsection
