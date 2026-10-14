@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.editservice.title'))


@section('content')
@push('after-styles')
<style>
  .dz-image img{width: 100%;}

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
    height: 820px;
    position: relative;
    z-index: 2;
    -moz-border-radius: 0 5px 5px 5px;
    -webkit-border-radius: 0 5px 5px 5px;
    border-radius: 0 5px 5px 5px;
    -moz-box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
    -webkit-box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
    box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
}

/* ------------------------------------------------- */
</style>
@endpush

@php ($disabled=false)

@if($logged_in_user->isServiceReceptionist() || $logged_in_user->isServiceEnginner())
    @php ($disabled=true)
@endif

<form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.service.update',['id'=>$service->id]) }}" enctype="multipart/form-data" novalidate>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col text-left">
                      <h4 class="card-title"><strong>Service No:</strong> {{ $service->receiptno }}</h4>
                    </div>
                    <div class="col text-right">
                        <a class="btn btn-danger btn-big" href="{{ route('admin.service.index') }}">Cancel</a>
                        <button class="btn btn-primary btn-bg pull-right" type="submit">Update</button>
                    </div><!--col-->
                  </div>
                </div><!--card-header-->

                <div class="card-body">

                      {{ csrf_field() }}
                      <input type="hidden" name="_method" value="PATCH">
<div class="row">
  <ul id="tabs">
    <li><a href="#" name="tab1"><h6 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> General Information</h6></a></li>

    <li><a href="#" name="tab3"><h6 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> Enginner Solution</h6></a></li>


</ul>

<div id="content" style="width:100%">
    <div id="tab1">

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
                  ->readonly()
                 }}
            </div>
        @php ($disabled=false)

            @if($logged_in_user->isServiceEnginner())
                @php ($disabled=false)
            @endif
            <label for="warrenty" class="col-md-2 form-control-label">Warrenty</label>
            <div class="col-md-1">
              {{ html()->label(
                      html()->checkbox('warrenty[]', $service->warrenty)
                            ->disabled($disabled)
                            ->class('switch-input')
                            ->id('warrenty-'. '01')
                          . '<span class="switch-slider" data-checked="in" data-unchecked="out"></span>')
                      ->class('switch switch-label switch-pill switch-primary mr-2')

                  ->for('warrenty-'. '01') }}

            </div>
            @if($logged_in_user->isServiceEnginner())
                @php ($disabled=true)
            @else
                @php ($disabled=false)
            @endif

            <label for="delivered" class="col-md-2 form-control-label"><strong>Delivered</strong></label>
            <div class="col-md-1">
              {{ html()->label(
                      html()->checkbox('delivered[]', $service->delivered)
                            ->disabled($disabled)
                            ->class('switch-input')
                            ->id('delivered-'. '01')
                          . '<span class="switch-slider" data-checked="yes" data-unchecked="no"></span>')
                      ->class('switch switch-label switch-pill switch-primary mr-2')

                  ->for('delivered-'. '01') }}

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

              <?php

            //  print_r($status);

              if($logged_in_user->isServiceEnginner()){
                if(strtolower($service->status)=='not start')
                    unset($status['pending'],$status['progress'],$status['complete'],$status['incomplete'],$status['cancel'],$status['delivered']);
                elseif(strtolower($service->status)=='start')
                    unset($status['not start'],$status['progress'],$status['delivered']);
                elseif(strtolower($service->status)=='pending')
                    unset($status['not start'],$status['start'],$status['delivered']);
                elseif(strtolower($service->status)=='progress')
                    unset($status['not start'],$status['pending'],$status['start'],$status['delivered']);
                elseif(in_array(strtolower($service->status),array('complete','incomplete','cancel')))
                    unset($status['not start'],$status['pending'],$status['start'],$status['delivered'],$status['progress']);
              }

                if(empty($service->rmacodes))
                        unset($status['complete']);
              /*
              elseif($logged_in_user->isServiceReceptionist()){
                  if(in_array(strtolower($service->status),array('complete','incomplete','cancel'))){
                    $status=array(strtolower($service->status)=>ucfirst($service->status));
                    $status['delivered']='Delivered';
                  }else
                    $status=array(strtolower($service->status)=>ucfirst($service->status));

                    //$status=array(array_key($service->status)=>ucfirst($service->status));
              }
              */

               ?>

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
            @if($logged_in_user->isServiceReceptionist())
                @php ($disabled=false)
            @endif
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
            <div class="col-md-1">
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
            <label for="chargeamount" class="col-md-1 form-control-label">Amount <span class="required">*</span></label>
            <div class="col-md-2">
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

            @if($logged_in_user->isServiceAdmin() || $logged_in_user->isAdmin())
            <label for="labor_cost" class="col-md-1 form-control-label">Labor Cost</label>
            <div class="col-md-2">
              {{ html()->input('number','labor_cost',0)
                  ->class('form-control')
                  ->value($service->labor_cost)
                  ->attribute('step','0.01')
                }}
            </div>
            @endif

            <label for="brand" class="col-md-2 form-control-label">Brand <span class="required">*</span></label>
            <div class="col-md-4">
              {{ html()->select('brand')
                       ->class('form-control')
                       ->options($brands)
                       ->value(old('brand',$service->brand()->pluck('brands.id')->implode(' ')))
                       ->placeholder('Select brand')
                       ->required()
                    }}
            </div>
        </div>

        <!-- Row 6 -->
        @if($logged_in_user->isServiceEnginner())
            @php ($disabled=false)
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
        @if($logged_in_user->isServiceEnginner())
          @php ($disabled=true)
        @endif
        <div class="form-group row{{ $errors->has('payment') ? ' has-error' : '' }}">
          <label for="payment" class="col-md-2 form-control-label">Payment </label>
          <div class="col-md-1">
            {{ html()->label(
                    html()->checkbox('payment[]', $service->payment)
                          ->class('switch-input')
                          ->disabled($disabled)
                          ->id('payment-'. '01')
                        . '<span class="switch-slider" data-checked="yes"  data-unchecked="no"></span>')
                    ->class('switch switch-label switch-pill switch-primary mr-2')
                ->for('payment-'. '01') }}
          </div>
          <label for="transfer" class="col-md-2 form-control-label">Transfer HQ</label>
          <div class="col-md-1">
          @if($logged_in_user->isServiceEnginner())
            @php ($disabled=true)
          @endif
          {{ html()->label(
                    html()->checkbox('transfer_hq[]', $service->transfer_hq)
                          ->class('switch-input')
                          ->disabled($disabled)
                          ->id('transfer-'. '01')
                        . '<span class="switch-slider" data-checked="yes"  data-unchecked="no"></span>')
                    ->class('switch switch-label switch-pill switch-primary mr-2')
                ->for('transfer-'. '01') }}
          </div>

          @if($disabled)
            <input type="hidden" name="payment" value="{{ $service->payment }}">
            <input type="hidden" name="transfer_hq" value="{{ $service->transfer_hq }}">
            <input type="hidden" name="delivered" value="{{ $service->delivered }}">
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
        <div class="form-group row">
        <div class="col-md-12">
        <div class="form-group {{ $errors->has('logo') ? 'has-error' : '' }}">
            <label for="logo">Picture</label>
            <div class="needsclick dropzone" id="logo-dropzone">
            </div>
        </div>
        </div>
        <p>&nbsp;</p>

        </div>


      </div> <!-- div of row -->
      </div>




    </div> <!-- end of tab1 -->
    <div id="tab3">
      @if($logged_in_user->isServiceReceptionist())
          @php ($disabled=true)
      @endif

      @if($logged_in_user->isServiceEnginner())
          @php ($disabled=false)
      @endif


      <div class="row border">
        <div class="col">
          <br>
        <div class="form-group row{{ $errors->has('solution') ? ' has-error' : '' }}">

          <label for="solution" class="col-md-2 form-control-label">Solution</label>
          <div class="col-md-4">

            {{ html()->textarea('solution',old('solution',$service->solution))
                ->attribute((($disabled)?"disabled":""))
                ->class('form-control')

              }}
          </div>

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

        <label for="rmacode" class="col-md-2 form-control-label">RMACode </label>
        <div class="col-md-4">
          {{ html()->select('rmacode')
                   ->class('form-control')
                   ->options($rmacodes)
                   ->value(old('brand',$service->rmacodes()->pluck('rmacodes.id')->implode(' ')))
                   ->placeholder('Select RMACode')

                }}
        </div>

        @if($disabled)
          <input type="hidden" name="ordered" value="{{ $service->ordered }}">
          <input type="hidden" name="rmacode" value="{{ $service->rmacode }}">
        @endif
      </div>





      <div class="form-group row{{ $errors->has('solution') ? ' has-error' : '' }}">

        <label for="rmano" class="col-md-2 form-control-label">RMA #</label>
        <div class="col-md-4">

          {{ html()->text('rmano',old('rmano',$service->rmano))
              ->attribute((($disabled)?"disabled":""))
              ->class('form-control')
              ->disabled($status_order || $disabled)
            }}
        </div>
        <label for="rmadate" class="col-md-2 form-control-label">RMA Date <span class="required">*</span></label>
        <div class="col-md-4">
          {{ html()->input('text','rmadate','')
              ->class('form-control datepicker')
              ->value(old('rmadate',(empty($service->rmadate))?date("d/m/Y"):$service->rmadate))
              ->required()
              ->autofocus()
          }}

        </div>

    </div>
    <div class="row">
      <table class="table table-striped table-bordered" width="100%" id="purchase_detail">
        <thead>
          <tr>
            <td><div class="btn-group btn-group-sm" role="group" aria-label="User Actions">
              @if(!$logged_in_user->isServiceReceptionist())
                    <a href="#" class="btn btn-primary" id="btn_add"><i class="fas fa-plus-circle" data-toggle="tooltip" data-placement="top" title="Add"></i></a>
              @endif
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
              <th>{{ html()->label('Sub Total')
                    ->for('subtotal')
                    ->class('form-control-label') }}</th>
              <th>{{ html()->label('Selling Price')
                    ->for('selprice')
                    ->class('form-control-label')

                  }} </th>
              <th>{{ html()->label('Paid')
                    ->for('payment')
                    ->class('form-control-label')

                  }} </th>

          </tr>
        </thead>
        <tbody>
          @foreach ($service->service_rma as $rma_detail)
            <tr>
              <td>
              @if($rma_detail->payment!=1 && !$logged_in_user->isServiceReceptionist())
                <div class="btn-group btn-group-sm" role="group" aria-label="User Actions">
                        <a href="#" class="btn btn-danger btn_remove"><i class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="Remove"></i></a>
                </div>
              @endif
              </td>
              <td>
                 <input type="hidden" name='rmaids[]' value="{{ $rma_detail->id }}">
                 {{ html()->select('category[]')
                         ->class('form-control category')
                         ->options($categorys)
                         ->value(old('category[]',$rma_detail->category_id)) //$purchase_detail->category_id
                         ->placeholder('Select Category')
                         ->required()
                      }}</td>
                <td>  {{ html()->input('text','item_name[]')
                      ->class('form-control')
                      ->value(old('item_name[]',$rma_detail->item_name))
                      ->required()
                        }}</td>
                <td> {{ html()->input('number','quantity[]')
                    ->class('form-control')
                    ->value(old('quantity[]',$rma_detail->quantity))
                     ->required()
                  }} </td>
                <td> {{ html()->input('number','purchaseprice[]')
                    ->class('form-control')
                    ->value(old('purchaseprice[]',$rma_detail->purchaseprice))
                     ->required()
                  }} </td>
                <td> {{ html()->input('number','subtotal[]')
                    ->class('form-control')
                    ->value(old('subtotal[]',($rma_detail->quantity * $rma_detail->purchaseprice)))
                    ->readonly()
                  }} </td>
                <td> {{ html()->input('number','sellingprice[]')
                    ->class('form-control')
                    ->value(old('sellingprice[]',$rma_detail->sellingprice))
                  }}
                </td>
                <td>
                  @if($rma_detail->payment==1)
                    <input type="hidden" name="paymentrma[]" value="{{ $rma_detail->id }}">
                    {{ html()->checkbox('paymentrma1[]','0')
                      ->class('form-control')
                      ->value($rma_detail->id)
                      ->checked(($rma_detail->payment==1)?true:false)
                      ->disabled(true)
                    }}
                  @else
                  {{ html()->checkbox('paymentrma[]','0')
                    ->class('form-control')
                    ->value($rma_detail->id)
                    ->checked(($rma_detail->payment==1)?true:false)

                  }}
                 @endif
                </td>
            </tr>
          @endforeach
          <tr>
            <td>
              <div class="btn-group btn-group-sm" role="group" aria-label="User Actions">
                      <a href="#" class="btn btn-danger btn_remove"><i class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="Remove"></i></a>
                </div>
            </td>
            <td>
              <input type="hidden" name='rmaids[]' value="0">
               {{ html()->select('category[]')
                       ->class('form-control category')
                       ->options($categorys)
                       ->value(old('category'))
                       ->placeholder('Select Category')
                       ->required()
                    }}</td>
              <td>  {{ html()->input('text','item_name[]')
                         ->class('form-control')
                         ->value(old('item_name[]'))
                         ->required()
                      }}</td>

              <td> {{ html()->input('number','quantity[]')
                  ->class('form-control')
                  ->value(old('quantity[]',1))
                  ->required()
                }} </td>
              <td> {{ html()->input('number','purchaseprice[]')
                  ->class('form-control')
                  ->value(old('purchaseprice[]',0))
                  ->disabled($disabled)
                }} </td>
              <td> {{ html()->input('number','subtotal[]')
                  ->class('form-control')
                  ->value(old('subtotal[]'))
                  ->readonly()
                }} </td>
              <td> {{ html()->input('number','sellingprice[]')
                  ->class('form-control')
                  ->value(old('sellingprice[]',1))

                }}
              </td>
              <td> {{ html()->checkbox('paymentrma[]','0')
                  ->class('form-control')
                  ->value(0)
                  ->checked(false)
                  ->disabled()
                }}
              </td>


          </tr>
        </tbody>
      </table>
    </div>
<p>&nbsp;</p>
    </div>
  </div>
    </div>
</div>
</div>
                    </div><!--card-body-->
                </div>
            </div><!--card-->
        </div><!--col-->

      </form>
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

  $('#rmadate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons",

  });

  $('#credit_note_type').on('change',function(){
    if($(this).val()==3){
      $('#credit_note_clear').prop( "disabled", false );
    }else {
        $('#credit_note_clear').prop( "disabled", true );
    }
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

  $("#shipping-01").on("change", function(){
      if($(this).is(':checked')){
        $('input[name="shipname"]').prop( "disabled", false );
        $('input[name="shipdate"]').prop( "disabled", false );
        $('input[name="shipcost"]').prop( "disabled", false );
        $('input[name="shipreference"]').prop( "disabled", false );
      }else{
        $('input[name="shipname"]').prop( "disabled", true );
        $('input[name="shipdate"]').prop( "disabled", true );
        $('input[name="shipcost"]').prop( "disabled", true );
        $('input[name="shipreference"]').prop( "disabled", true );
      }

  });

  $("#ordered-01").on("change", function(){
      if($(this).is(':checked')){
        $('#purchase_detail').show();
        $('input[name="ordered_comment"]').prop( "disabled", false );
    //    $('select[name="rmacode"]').prop( "disabled", false );
        $('input[name="rmano"]').prop( "disabled", false );
        //$('textarea[name="solution"]').prop( "disabled", false );
        $('select[name="ordered_supplyer"]').prop( "disabled", false );
        $('.rma_detail').prop( "disabled", false );
      //  $('input[name="ordered_comment"]').focus();

      }else {
        $('#purchase_detail').hide();
        $('input[name="ordered_comment"]').prop( "disabled", true );
        $('input[name="ordered_comment"]').val('');
//        $('select[name="rmacode"]').prop( "disabled", true );
        $('input[name="rmano"]').prop( "disabled", true );
        //$('textarea[name="solution"]').prop( "disabled", true );
        $('.rma_detail').prop( "disabled", true );

      }

  });

  if($('#ordered-01').is(':checked'))
      $('#purchase_detail').show();
  else
      $('#purchase_detail').hide();

  $('#purchase_detail').parent().show();

/*
  $('#date').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });
*/

  $('#buyingdate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

  $('#shipdate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

  var old_datereturn='{{ $service->returndate }}';
  var old_datetransfer='{{ $service->transferdate }}';

  $("#destination").on("change", function(){
      if($(this).val()!=''){

        $('input[name="transferdate"]').prop( "disabled", false );
        $('input[name="returndate"]').prop( "disabled", false );
        $('#credit_note_type').prop( "disabled", false );
        $('input[name="credit_note_reason"]').prop( "disabled", false );
        if($('#credit_note_type').val()==3)
          $('input[name="credit_note_clear"]').prop( "disabled", false );
        else
          $('input[name="credit_note_clear"]').prop( "disabled", true );

      //  $('input[name="transferdate"]').focus();

        $('input[name="transferdate"]').val(old_datetransfer);
        $('input[name="returndate"]').val(old_datereturn);


      }else {

        $('input[name="transferdate"]').prop( "disabled", true );
        $('#credit_note_type').prop( "disabled", true );
        $('input[name="credit_note_reason"]').prop( "disabled", true );
        $('input[name="credit_note_clear"]').prop( "disabled", true );

        old_datetransfer=$('input[name="transferdate"]').val();

        $('input[name="transferdate"]').val('');
        $('input[name="returndate"]').prop( "disabled", true );
        old_datereturn=$('input[name="returndate"]').val();
        $('input[name="returndate"]').val('');
      }

  });

  var RowNew;


  $(document).on("click",".btn_remove",function(e){
      e.preventDefault();

      var row=tbl_purchase.rows( $(this).parents('tr') );
      if(tbl_purchase.rows().count()>1)
        row.remove().draw();
      else
          alert('Can not remove, It still has one row.');
  });

  $(document).on("click","#btn_add",function(e){
    e.preventDefault();
    tbl_purchase.row.add(RowNew).draw();
//  newrow.appendTo("#purchase_detail tbody");


  });

  $(document).on("change",'input[name="quantity[]"],input[name="purchaseprice[]"]',function(){
      var price= $(this).closest('tr').find('input[name="purchaseprice[]"]').val();
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

  @if($logged_in_user->isServiceReceptionist())
    //$('#purchase_detail').find('input').attr('readonly','readonly');
    $('#purchase_detail').find('select').prop('disabled','true');
    $('#purchase_detail').find('input').prop('disabled','true');
  @endif
  //tbl_purchase.row(0).node().find('input[name="quantity[]"]').attr('required','')
  RowNew=tbl_purchase.row({{count($service->service_rma)}}).data();

  if({{count($service->service_rma)}}>0)
    tbl_purchase.rows({{count($service->service_rma)}}).remove().draw();

  if({{count($service->service_rma)}}==0)
    $('#purchase_detail tbody tr:first').find('.btn_remove').hide();

});
</script>
@endsection
