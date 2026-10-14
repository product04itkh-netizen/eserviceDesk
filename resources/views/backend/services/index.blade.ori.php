@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.service.title'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <div class="row">
                 <div class="col-sm-5">
                  <h4 class="card-title">@lang('strings.backend.service.title')</h4>
                 </div>
                  <div class="col-sm-7">
                      <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                        <a href="{{route('admin.service.create')}}" class="btn btn-success ml-1" data-toggle="tooltip" title="" data-original-title="Create New"><i class="fas fa-plus-circle"></i> Create New Service</a>
                      </div><!--btn-toolbar-->
                  </div>
                </div>
                </div><!--card-header-->

                <div class="card-body">


                      <div class="row mt-4">
            <div class="col">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Receipt#</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Date</th>
                            <th>Model</th>
                            <th>Problem</th>
                            <th>Status</th>
                            <th>Charge</th>
                            <th>Amount</th>
                            <th>Warrenty</th>
                            <th>Assign</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                          @foreach($services as $service)
                            <tr>
                              <td>{{ $service->receiptno }}</td>
                              <td>{{ $service->customer_name($service->customerid) }}</td>
                              <td>{{ $service->customer_phone($service->customerid) }}</td>
                              <td>{{ $service->date }}</td>
                              <td>{{ $service->model }}</td>
                              <td>{{ $service->problem()->pluck('problem')->implode(' ') }}</td>
                              <td>{{ ucfirst($service->status) }}</td>
                              <td>{{ ucfirst($service->chargetype) }}</td>
                              <td>{{ $service->chargeamount }}</td>
                              <td>
                                @if($service->warrenty==0)
                                  No
                                @else
                                  Yes
                                @endif
                              </td>
                              <td>{{ $service->enginner_name($service->assignto) }}</td>
                              <!-- $role->action_buttons -->
                              <td>
                                {!! $service->action_buttons !!}
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </table>

                  </div>
                </div>
              </div>
              <div class="row">
              <div class="col-12">
              <div class="float-left">
                {{count($services)}} services total
              </div>
              <div class="float-right">
                {{ $services->links() }}
              </div>
              </div><!--col-->
              </div>

              <!--
              <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                  <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Home</a>
                  <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</a>
                  <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</a>
                </div>
              </nav>
              <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">..1.</div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">..2.</div>
                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">..3.</div>
              </div>
            -->
          </div><!--card-body-->



                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection
