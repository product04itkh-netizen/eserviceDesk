@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.customer.title'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <div class="row">
                 <div class="col-sm-5">
                  <h4 class="card-title">@lang('strings.backend.customer.title')</h4>
                 </div>
                  <div class="col-sm-7">
                      <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                        <a href="{{route('admin.customer.create')}}" class="btn btn-success ml-1" data-toggle="tooltip" title="" data-original-title="Create New"><i class="fas fa-plus-circle"></i></a>
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
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>E-mail</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($customers as $customer)
                            <tr>
                              <td>{{ $customer->first_name }}</td>
                              <td>{{ $customer->last_name }}</td>
                              <td>{{ $customer->emails }}</td>
                              <td>{{ $customer->phone }}</td>
                              <td>@if($customer->status==1)
                                   {{ 'Active' }}
                                  @else
                                    {{ 'Inactive' }}
                                  @endif
                              </td>
                              <!-- $role->action_buttons -->
                              <td>
                                {!! $customer->action_buttons !!}
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
                {{count($customers)}} customers total
              </div>
              <div class="float-right">
                {{ $customers->links() }}
              </div>
              </div><!--col-->
              </div>

          </div><!--card-body-->



                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection
