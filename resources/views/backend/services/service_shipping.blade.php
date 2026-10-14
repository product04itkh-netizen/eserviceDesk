@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.shipping.title'))

@section('content')
@php
$mytime = Carbon\Carbon::now()->format('d/m/Y');
@endphp
@if(empty($shipping))
  <form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.service.shipping',['id'=>(empty($shipping)?'create':$shipping->id)]) }}" enctype="multipart/form-data">
@else
  <form class="form-horizontal form-label-left" method="POST" action="{{ route('admin.service.shipping.edit',['id'=>(empty($shipping)?'create':$shipping->id)]) }}" enctype="multipart/form-data">
@endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <div class="row">
                  <div class="col text-left">
                    <h4 class="card-title">@lang('strings.backend.shipping.title')</h4>
                  </div>
                    <div class="col text-right">

                        @if(empty($shipping))
                          <button class="btn btn-primary btn-bg pull-right" name="btn_save" type="submit">Create</button>
                        @else
                          <a class="btn btn-danger btn-big" href="{{ route('admin.service.shipping',['id'=>'create']) }}">Cancel</a>
                          <button class="btn btn-primary btn-bg pull-right" name="btn_save" type="submit">Save</button>
                        @endif
                    </div><!--col-->
                  </div>
                </div><!--card-header-->
                <div class="card-body">

                      {{ csrf_field() }}
                      @if(!empty($shipping))
                          <input type="hidden" name="_method" value="PATCH">
                          <input type="hidden" name="model" value="update">
                      @else
                            <input type="hidden" name="model" value="create">
                      @endif


                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="shippingno" class="col-md-2 form-control-label">Shipping No <span class="required">*</span></label>
                          <div class="col-md-4">
                            {{ html()->input('text','shippingno','')
                                ->class('form-control')
                                ->value(empty($shipping)?'auto generate':$shipping->id)
                                ->disabled()
                                 }}

                          </div>
                          {{ html()->label('Date','date')
                            ->class('col-md-2 form-control-label')

                          }}
                          <div class="col-md-4">
                            {{ html()->input('text','date')
                                ->class('form-control datepicker')
                                ->value(empty($shipping)?date('d/m/Y'):$shipping->shipdate)
                                ->required()
                                 }}

                          </div>
                      </div>

                      <div class="form-group row{{ $errors->has('reference') ? ' has-error' : '' }}">
                          <label for="reference" class="col-md-2 form-control-label">Reference</label>
                          <div class="col-md-4">
                            {{ html()->input('text','reference','')
                                ->class('form-control')
                                ->value(empty($shipping)?'':$shipping->shipreference)

                                 }}

                          </div>
                          {{ html()->label('Costing','costing')
                            ->class('col-md-2 form-control-label')

                          }}
                          <div class="col-md-4">
                            {{ html()->input('number','costing')
                                ->class('form-control')
                                ->value(empty($shipping)?'0':$shipping->shipcost)
                                ->required()
                                 }}

                          </div>

                      </div>

                      <div class="form-group row{{ $errors->has('serviceno') ? ' has-error' : '' }}">
                          <label for="serviceno" class="col-md-2 form-control-label">Service No <span class="required">*</span></label>
                          <div class="col-md-4">
                            @php
                              $services=null;$services_selected=null;
                              if(!empty($shipping)){
                              $services=$shipping->service()->pluck('receiptno','services.id');
                              $services_selected=$shipping->service()->pluck('services.id');
                            //print_r($services_selected);
                              }
                            @endphp



                            {{ html()->multiselect('serviceno[]')                                ->class('form-control select2')
                                ->options($services)
                                ->value($services_selected)
                                ->required()

                                 }}

                          </div>
                      </div>


                  </div>

                </div> <!-- end of card -->
                <div class="card-body" style="background:#fff">
                  <div class="row">
                  <div class="col-md-12 table-responsive">
                      <table class="table table-bordered" id='tbl_shipping' width="100%">
                          <thead class="thead-light">
                          <tr>
                              <th class="no-sort">Actions</th>
                              <th class="text-nowrap">Shipping#</th>
                              <th>Shipping Date</th>
                              <th>Shipping Ref</th>
                              <th>Shipping Cost</th>
                              <th>Service No</th>
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
    $('#tbl_shipping tbody').find('[data-method="delete"]').append(function () {
      if (!$(this).find('form').length > 0) {
        return "\n<form action='" + $(this).attr('href') + "' method='POST' name='delete_item' style='display:none'>\n" + "<input type='hidden' name='_method' value='" + $(this).attr('data-method') + "'>\n" + "<input type='hidden' name='_token' value='" + $('meta[name="csrf-token"]').attr('content') + "'>\n" + '</form>\n';
      } else {
        return '';
      }
    }).attr('href', '#').attr('style', 'cursor:pointer;').attr('onclick', '$(this).find("form").submit();');

    $('#tbl_shipping tbody').find('[data-method="assign"]').append(function () {
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
              model:'shipping',
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

  $('#date').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

  var oTable=$('#tbl_shipping').DataTable({

    'fixedHeader': true,
      processing: true,
      serverSide: false,
      ajax: {
        url: '{{ route('admin.service.shipping',['id'=>0]) }}',
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
            { data: 'id', name: 'id'},
            { data: 'shipdate', name: 'date'},
            { data: 'shipreference', name: 'shipreference' },
            { data: 'shipcost', name: 'shipcost' },
            { data: 'services', name: 'services' },


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
