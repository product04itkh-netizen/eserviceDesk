@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.transfer.title'))

@section('content')
@php
$mytime = Carbon\Carbon::now()->format('d/m/Y');
@endphp
@if(empty($transfer))
  <form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.service.transfer',['id'=>(empty($transfer)?'create':$transfer->id)]) }}" enctype="multipart/form-data">
@else
  <form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.service.transfer.edit',['id'=>(empty($transfer)?'create':$transfer->id)]) }}" enctype="multipart/form-data">
@endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <div class="row">
                  <div class="col text-left">
                    <h4 class="card-title">@lang('strings.backend.transfer.title')</h4>
                  </div>
                    <div class="col text-right">


                        @if(empty($transfer))
                          <button class="btn btn-primary btn-bg pull-right" name="btn_save" type="submit">Create</button>
                        @else
                          <a class="btn btn-danger btn-big" href="{{ route('admin.service.transfer',['id'=>'create']) }}">Cancel</a>
                          <button class="btn btn-primary btn-bg pull-right" name="btn_save" type="submit">Save</button>
                        @endif
                    </div><!--col-->
                  </div>
                </div><!--card-header-->
                <div class="card-body">

                      {{ csrf_field() }}
                      @if(!empty($transfer))
                          <input type="hidden" name="_method" value="PATCH">
                          <input type="hidden" name="model" value="update">
                      @else
                            <input type="hidden" name="model" value="create">
                      @endif


                      <div class="form-group row{{ $errors->has('transferno') ? ' has-error' : '' }}">
                          <label for="transferno" class="col-md-2 form-control-label">Transfer No <span class="required">*</span></label>
                          <div class="col-md-2">
                            {{ html()->input('text','transferno','')
                                ->class('form-control')
                                ->value(empty($transfer)?'auto generate':$transfer->id)
                                ->disabled()
                                 }}

                          </div>


                          @php
                            $type=['Credit Note'=>'Credit Note','Cancel Credit Note'=>'Cancel Credit Note','Clear Credit Note'=>'Clear Credit Note'];
                          @endphp
                          {{ html()->label('Transfer Type <span class="required">*</span>','type')
                            ->class('col-md-2 form-control-label')

                          }}
                          <div class="col-md-2">
                            {{ html()->select('type')
                                ->class('form-control')
                                ->options($type)
                                ->value(empty($transfer)?'Credit Note':$transfer->type)
                                ->required()
                                 }}

                          </div>
                          {{ html()->label('Vendor <span class="required">*</span>','vendor_id')
                            ->class('col-md-2 form-control-label')

                          }}
                          <div class="col-md-2">
                            {{ html()->select('vendor_id')
                                ->class('form-control')
                                ->options($vendors)
                                ->value(empty($transfer)?'':$transfer->vendor_id)
                                ->placeholder('Select Vendor')
                                ->required()
                                 }}

                          </div>
                      </div>

                      <div class="form-group row{{ $errors->has('transferdate') ? ' has-error' : '' }}">
                        {{ html()->label('Transfer Date <span class="required">*</span>','transferdate')
                          ->class('col-md-2 form-control-label')

                        }}
                        <div class="col-md-2">
                          {{ html()->input('text','transferdate')
                              ->class('form-control date-picker')
                              ->value(empty($transfer)?date('d/m/Y'):$transfer->transferdate)
                              ->required()
                               }}

                        </div>
                        {{ html()->label('Return Date','returndate')
                          ->class('col-md-2 form-control-label')

                        }}
                        <div class="col-md-2">
                          {{ html()->input('text','returndate')
                              ->class('form-control date-picker')
                              ->value(empty($transfer)?'':$transfer->returndate)

                               }}

                        </div>
                        {{ html()->label('Clear Date','cleardate')
                          ->class('col-md-2 form-control-label')

                        }}
                        <div class="col-md-2">
                          {{ html()->input('text','cleardate')
                              ->class('form-control date-picker')
                              ->value(empty($transfer)?'':$transfer->cleardate)

                               }}

                        </div>

                      </div>

                      <div class="form-group row{{ $errors->has('cnno') ? ' has-error' : '' }}">
                        <label for="cnno" class="col-md-2 form-control-label">CreditNote #<span class="required">*</span></label>
                        <div class="col-md-4">

                          {{ html()->input('text','cnno')
                              ->class('form-control')
                              ->value(empty($transfer)?'':$transfer->cnno)
                              ->required()
                               }}

                        </div>
                        <label for="cnprice" class="col-md-2 form-control-label">Price <span class="required">*</span></label>
                        <div class="col-md-4">

                          {{ html()->input('number','cnprice')
                              ->class('form-control')
                              ->value(empty($transfer)?'':$transfer->cnprice)
                              ->required()
                               }}

                        </div>
                      </div>
                          <div class="form-group row{{ $errors->has('reason') ? ' has-error' : '' }}">
                            <label for="serviceno" class="col-md-2 form-control-label">Service No <span class="required">*</span></label>
                            <div class="col-md-4">
                              @php
                                $services=null;$services_selected=null;
                                if(!empty($transfer)){
                                $services=$transfer->service()->pluck('receiptno','services.id');
                                $services_selected=$transfer->service()->pluck('services.id');
                              //print_r($services_selected);
                                }
                              @endphp



                              {{ html()->select('serviceno[]')                                ->class('form-control select2')
                                  ->options($services)
                                  ->value($services_selected)
                                  ->required()

                                   }}

                            </div>
                          <label for="reason" class="col-md-2 form-control-label">Reason</label>
                          <div class="col-md-4">
                            {{ html()->textarea('reason','')
                                ->class('form-control')
                                ->value(empty($transfer)?'':$transfer->reason)

                                 }}

                          </div>
                        </div>

                  </div>

                </div> <!-- end of card -->
                <div class="card-body" style="background:#fff">
                  <div class="row">
                  <div class="col-md-12 table-responsive">
                      <table class="table table-bordered" id='tbl_transfer' width="100%">
                          <thead class="thead-light">
                          <tr>
                              <th class="no-sort">Actions</th>
                              <th class="text-nowrap">CN Number</th>
                              <th>CN Price</th>
                              <th>Transfer Date</th>
                              <th>Transfer Type</th>
                              <th>Return Date</th>
                              <th>Clear Date</th>
                              <th>Reason</th>
                              <th>Service #</th>
                              <th>Vendor</th>
                          </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                    </div>
                </div>
              </div>
              </div> <!-- end of cols12 -->
            </div> <!-- end of main row -->
        </form>
@endsection

@section('custom_script')
<script>
var CSRF_TOKEN=$('meta[name="csrf-token"]').attr('content');
$(document).ready(function(){

  //$.ajaxSetup({
    //  headers: {

      //}
  //});

  function addDeleteForms() {
    $('#tbl_transfer tbody').find('[data-method="delete"]').append(function () {
      if (!$(this).find('form').length > 0) {
        return "\n<form action='" + $(this).attr('href') + "' method='POST' name='delete_item' style='display:none'>\n" + "<input type='hidden' name='_method' value='" + $(this).attr('data-method') + "'>\n" + "<input type='hidden' name='_token' value='" + $('meta[name="csrf-token"]').attr('content') + "'>\n" + '</form>\n';
      } else {
        return '';
      }
    }).attr('href', '#').attr('style', 'cursor:pointer;').attr('onclick', '$(this).find("form").submit();');

    $('#tbl_transfer tbody').find('[data-method="assign"]').append(function () {
      if (!$(this).find('form').length > 0) {
        return "\n<form action='" + $(this).attr('href') + "' method='POST' name='assign_item' style='display:none'>\n" + "<input type='hidden' name='_token' value='" + $('meta[name="csrf-token"]').attr('content') + "'>\n" + '</form>\n';
      } else {
        return '';
      }
    }).attr('href', '#').attr('style', 'cursor:pointer;').attr('onclick', '$(this).find("form").submit();');

    }

  $('select[name="serviceno[]"]').select2({
        ajax: {
          url: "{{route('admin.service.getServices')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              _token: CSRF_TOKEN,
              model:'transfer',
              search: params.term // search term
            };
          },
          processResults: function (response) {
          //  console.log(response)
            return {
              results: response
            };
          },

          cache: true
        },
  });

  $('#transferdate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

  $('#returndate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

  $('#cleardate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

  var oTable=$('#tbl_transfer').DataTable({

    'fixedHeader': true,
      processing: true,
      serverSide: false,
      ajax: {
        url: '{{ route('admin.service.transfer',['id'=>0]) }}',
        data: function(data) {
          data.ajax = 'ajax';

        }

      },
/*
      columnDefs: [
        { targets: -1, className: 'dt-body-right' },
        { "visible": false, "targets": groupColumn },
      // { "orderable": false, "targets": [0,2,3,4,5,6,7,8,9,10]}
      ],
*/
      paging:   false,
      ordering: false,
/*
      order: [
            [1, 'asc']
        ],
*/
      info:     false,
      searching: false,
      columns: [

            { data: 'action', name: 'action' },
            { data: 'cnno', name: 'cnno'},
            { data: 'cnprice', name: 'cnprice'},
            { data: 'transferdate', name: 'transferdate'},
            { data: 'type', name: 'type' },
            { data: 'returndate', name: 'returndate' },
            { data: 'cleardate', name: 'cleardate' },
            { data: 'reason', name: 'reason' },
            { data: 'services', name: 'services' },
            { data: 'vendor', name: 'vendor' },

            ],
            "initComplete":function( settings, json){
              addDeleteForms();
              //  console.log(json.data[0].service_rma);
         // call your function here
     }

   });
});
</script>
@endsection
