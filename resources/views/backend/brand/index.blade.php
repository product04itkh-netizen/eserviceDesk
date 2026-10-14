@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.barnd.title'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <div class="row">
                 <div class="col-sm-5">
                  <h4 class="card-title">@lang('strings.backend.brand.title')</h4>
                 </div>
                  <div class="col-sm-7">
                      <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                        <a href="{{route('admin.brand.create')}}" class="btn btn-success ml-1" data-toggle="tooltip" title="" data-original-title="Create New"><i class="fas fa-plus-circle"></i> Add Brand</a>
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
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($brands as $brand)
                            <tr>
                              <td>{{ $brand->name }}</td>
                              <td>{{ $brand->description }}</td>
                              <!-- $role->action_buttons -->
                              <td>
                                {!! $brand->action_buttons !!}
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
                {{count($brands)}} brands total
              </div>
              <div class="float-right">
                {{ $brands->links() }}
              </div>
              </div><!--col-->
              </div>

          </div><!--card-body-->



                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection
