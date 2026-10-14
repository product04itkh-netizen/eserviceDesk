@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.item.title'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <div class="row">
                 <div class="col-sm-5">
                  <h4 class="card-title">@lang('strings.backend.stock.title')</h4>
                 </div>
                  <div class="col-sm-7">
                      <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                        @if(!$logged_in_user->isStockSaleorder())
                            <a href="{{route('admin.purchase.create')}}" class="btn btn-success ml-1" data-toggle="tooltip" title="" data-original-title="Create New"><i class="fas fa-plus-circle"></i> Add Purchase</a>
                        @endif
                        @if(!$logged_in_user->isStockPurchase())
                            <a href="{{route('admin.saleorder.create')}}" class="btn btn-success ml-1" data-toggle="tooltip" title="" data-original-title="Create New"><i class="fas fa-plus-circle"></i> Add Sale Order</a>
                        @endif

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
                            <th>Category</th>
                            <th>Sale Price</th>
                            <th>Description</th>
                            <th>Stock Available</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach($items as $item)
                            <tr>
                              <td>{{ $item->name }}</td>
                              <td>{{ $item->category->name }}</td>
                              <td>{{ $item->saleprice }}</td>
                              <td>{{ $item->description }}</td>
                              <td>{{ $item->stockable }}</td>
                              <!-- $role->action_buttons -->
                              <td>
                                {!! $item->action_buttons !!}
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
                {{count($items)}} items total
              </div>
              <div class="float-right">
                {{ $items->links() }}
              </div>
              </div><!--col-->
              </div>

          </div><!--card-body-->



                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection
