@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.vendor.title'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <div class="row">
                 <div class="col-sm-5">
                  <h4 class="card-title">@lang('strings.backend.vendor.title')</h4>
                 </div>
                  <div class="col-sm-7">
                      <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                        <a href="{{route('admin.vendor.create')}}" class="btn btn-success ml-1" data-toggle="tooltip" title="" data-original-title="Create New"><i class="fas fa-plus-circle"></i></a>
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
                            <th>Vendor Name</th>
                            <th>Contact Name</th>
                            <th>Contact Title</th>
                            <th>E-mail</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($vendors as $vendor)
                            <tr>
                              <td>{{ $vendor->companyname }}</td>
                              <td>{{ $vendor->contactname }}</td>
                              <td>{{ $vendor->contacttitle }}</td>
                              <td>{{ $vendor->emails }}</td>
                              <td>{{ $vendor->phone }}</td>
                              <td>@if($vendor->status==1)
                                   {{ 'Active' }}
                                  @else
                                    {{ 'Inactive' }}
                                  @endif
                              </td>
                              <!-- $role->action_buttons -->
                              <td>
                                {!! $vendor->action_buttons !!}
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
              <div class="col-7">
              <div class="float-left">
              {{count($vendors)}} vendors total
              </div>
              <div class="float-right">
                {{ $vendors->links() }}
              </div>
              </div><!--col-->
              </div>

          </div><!--card-body-->



                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection
