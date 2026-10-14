@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.viewservice.title'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.viewservice.title')</h4>
                </div><!--card-header-->
                <div class="card-body">

                <div class="row">

                  <div class="col">

                    <h5 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> General Information</h5>
                  </div>

                </div>
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
                            {{ html()->label((($service->warrenty==0)?"In":"Out"))
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
                          <div class="col-md-4">
                            {{ html()->label(ucfirst($service->chargetype))
                              ->for('chragetype')
                              ->class('form-control')
                            }}
                          </div>
                          <label for="chargeamount" class="col-md-2 form-control-label">Amount <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->label('' . ($service->chargeamount))
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
                    </div>
                  </div>
                      <!-- Row 7 -->
                      <div class="row">

                        <div class="col">
                          <br>
                          <h5 class="text-left" style="white-space:nowrap"><i class="fas fa-external-link-alt"></i> Customer Payment</h5>
                        </div>

                      </div>

                      <div class="row border">
                        <div class="col">
                          <br>
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
                          <div class="col-md-4">
                        {{ html()->label((($service->payment==0)?"Not Pay":"Paid"))
                            ->for('payment')
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

                    </div>
                  </div>
                      <!-- Row 9 -->
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
                          {{ html()->label((($service->ordered==0)?"No order":"Ordered"))
                            ->for('ordered')
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
                      <!-- Row 10 -->
                      <div class="form-group row{{ $errors->has('solution') ? ' has-error' : '' }}">
                        <label for="solution" class="col-md-2 form-control-label">Solution</label>
                        <div class="col-md-4">

                          {{ html()->label($service->solution)
                            ->for('solution')
                            ->class('form-control')
                          }}
                        </div>


                        <label for="destination" class="col-md-2 form-control-label">Vendor</label>
                        <div class="col-md-4">
                          {{ html()->label($service->vendor_name($service->destination))
                            ->for('destination')
                            ->class('form-control')
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
                        <br>
                        @if($service->logo)
                            <a href="{{ $service->logo->getUrl() }}" title="Image Zoom" data-spzoom>
                                <img src="{{ $service->logo->getUrl() }}" height="150px">
                            </a>
                        @endif
                    </div>
                    </div>

                  </div>

                    </div><!--card-body-->
                    <div class="card-footer clearfix">
                                    <div class="row">
                                        <div class="col">
                                            <a class="btn btn-danger btn-sm" href="{{ route('admin.service.index') }}">Back to List</a>
                                        </div><!--col-->
                                        @if(Auth()->user()->isServiceAdmin() || Auth()->user()->isServiceReceptionist())
                                        <div class="col text-center">
                                          <a class="btn btn-info btn-sm" href="{{ route('admin.service.receipt',['id'=>$service->id]) }}">Print Receipt</a>
                                            <a class="btn btn-danger btn-sm" href="{{ route('admin.service.create') }}">Add New</a>
                                            <a class="btn btn-info btn-sm" href="{{ route('admin.service.invoice',['id'=>$service->id]) }}">Print Invoice</a>
                                        </div><!--col-->
                                        @endif
                                        <div class="col text-right">
                                          @php
                                          if(Auth()->user()->isServiceAdmin()){
                                            $disabled='';

                                          }else {
                                            if(($service->payment=='0') || empty($service->payment)){
                                                if(Auth()->user()->isServiceEnginner() && Auth()->user()->id==$service->assignto)
                                                  $disabled='';
                                                elseif(Auth()->user()->isServiceAdmin())
                                                  $disabled='';
                                                elseif(Auth()->user()->isServiceReceptionist())
                                                    $disabled='';
                                                else
                                                  $disabled='disabled';
                                            }else
                                              $disabled='disabled';

                                          }@endphp

                                              <a class="btn btn-danger btn-sm {{ $disabled }}"  href="{{ route('admin.service.edit',['id'=>$service->id]) }}">Edit</a>

                                        </div><!--col-->
                    </div><!--row-->
                                </div><!--card-footer-->



                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection
@section('custom_script')
<script>
  $('[data-spzoom]').spzoom();
</script>
@endsection
