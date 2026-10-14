@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.report_rma.title'))

@section('content')
<style>
tr.group,
tr.group:hover {
    background-color: #ddd !important;
}

#exTab3 .nav-pills > li > a {
  border-radius: 4px 4px 0 0 ;
}

#exTab3 .tab-content {
  color : white;
  background-color: #428bca;
  padding : 5px 15px;
}

</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">@lang('strings.backend.report_rma.title')</h4>
            </div><!--card-header-->
            <div class="card-body">

              <div class="row border">
                    <div class="col">
                      <br>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                        <label for="receiptno" class="col-md-1 form-control-label">Date Fr </label>
                        <div class="col-md-2">
                          {{ html()->input('text','date_fr')
                              ->class('form-control')
                              ->value(old('date_fr'))
                              ->required()

                            }}
                        </div>
                        <label for="assignto" class="col-md-1 form-control-label">Date To</label>
                        <div class="col-md-2">
                          {{ html()->input('text','date_to')
                              ->class('form-control')
                              ->value(old('date_to'))
                              ->required()
                            }}
                        </div>

                          <div class="col-md-2 text-left nowrap">
                            <input type="button" id='btn_view' class="btn btn-primary" value="View">

                            <input type="button" class="btn btn-info btn_print" value="Print">

                          </div>

                          <div class="col-md-1" id="div_export">
                          </div>
                      </div>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                        {{ html()->label('Vendor','vendor_id')
                          ->class('col-md-1 form-control-label')

                        }}
                        <div class="col-md-2">
                          {{ html()->select('category_id')
                              ->class('form-control')
                              ->options($categorys)
                              ->value('')
                              ->placeholder('Select Vendor')
                              ->required()
                               }}
                        </div>
                        <label for="rma_paid" class="col-md-1 form-control-label">RMA Paid</label>
                        <div class="col-md-2">
                          {{ html()->select('rma_paid')
                                   ->class('form-control')
                                   ->options($payment)
                                   ->value('')
                                   ->placeholder('ALL')

                                }}

                        </div>

                      </div>
                    </div>
              </div>

              <br>
              <div class="row border report_view">
                    <div class="col">
              <table border="0" class="table compact" id="tbl_report" width="100%">
                <thead>
                    <th>No</th>
                    <th>Date</th>
                    <th>RMA#</th>
                    <th>Service #</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>P. Price</th>
                    <th>Subtotal</th>
                    <th>S. Price</th>
                    <th>Vendor</th>
                    <th>Paid</th>
                    <th>Reference</th>
                </thead>
              </table>
                </div>
            </div>
            </div>

        </div>
  </div>
</div>
@endsection
@section('custom_script')
<script>
$(document).ready(function(){

  $('#date_fr').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

  $('#date_to').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });
//  /* data: {'ajax':'ajax','date_fr':$('#date_fr').val(),'date_to':$('#date_to').val()}, */

      var groupColumn = 1;
      var oTable=$('#tbl_report').DataTable({

        'fixedHeader': true,
          processing: true,
          serverSide: false,
          ajax: {
            url: '{{ route('admin.rma.report') }}',
            data: function(data) {
              data.ajax = 'ajax';
              data.date_fr = $('#date_fr').val();
              data.date_to = $('#date_to').val();
              data.payment = $('#rma_paid').val();
              data.category_id = $('#category_id').val();
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
                   { data: 'no', render: function (data, type, row, meta) {
                          return meta.row + meta.settings._iDisplayStart + 1;
                        },
                   } ,
                  { data: 'rmadate', name: 'rmadate'},
                  /*{ data: 'date',
                     type: 'num',
                     render: {
                        _: 'display',
                        sort: 'timestamp',

                    }},
                    */
                   { data: 'rmano', name: 'rmano' },
                   { data: 'receiptno', name: 'receiptno' },
                   { data: 'item_name', name: 'item_name' },
                   { data: 'quantity', name: 'quantity' },
                   { data: 'purchaseprice', name: 'purchaseprice' },
                   { data: 'subtotal', name: 'subtotal' },
                   { data: 'sellingprice', name: 'sellingprice' },
                   { data: 'vendor', name: 'vendor' },
                   { data: 'payment', name: 'payment' },
                   { data: 'reference', name: 'reference' },

                ],
                "initComplete":function( settings, json){
                  //  console.log(json.data[0].service_rma);
             // call your function here
         }

       });

  var buttons = new $.fn.dataTable.Buttons(oTable,{

    'buttons': [
          {
                      extend: 'collection',
                      text: 'Export',
                      autoClose: true,
                      buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5' ,
                        {
                          'extend': 'pdfHtml5',
                          'orientation': 'landscape',
                          'pageSize': 'A4',
                          'exportOptions': {
                                    modifier : {
                                    // DataTables core
                                    order : 'index',  // 'current', 'applied', 'index',  'original'
                                    page : 'all',      // 'all',     'current'
                                    search : 'none'     // 'none',    'applied', 'removed'
                                }
                             }
                        },

                      ],
                      fade: true
          }
        ]

  }).container().appendTo($('#div_export'));


  $('#btn_view').click(function(){
    oTable.ajax.reload();

  });

  $('.btn_print').on("click",function(e){
    e.preventDefault();
    $('div.report_view').print();
  });

})
</script>
  @endsection
