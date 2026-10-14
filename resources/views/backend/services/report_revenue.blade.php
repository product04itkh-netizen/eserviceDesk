@extends('backend.layouts.app')

@section('title', app_name() . ' | Revenue & Profit Report')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Revenue & Profit Report</h4>
            </div><!--card-header-->
            <div class="card-body">

              <p class="text-muted">
                Labor charge + parts markup, minus parts cost and labor cost, for tickets marked
                Paid within the selected date range (filtered by payment date). Labor Cost is set
                per-ticket by an admin on the Edit screen — profit is only as accurate as that figure
                being filled in; a ticket with no Labor Cost entered is treated as $0 cost.
              </p>

              <div class="row border">
                    <div class="col">
                      <br>

                      <div class="form-group row">
                        <label for="date_fr" class="col-md-1 form-control-label">Date Fr </label>
                        <div class="col-md-2">
                          {{ html()->input('text','date_fr')
                              ->class('form-control')
                              ->value(old('date_fr'))
                              ->required()
                            }}
                        </div>
                        <label for="date_to" class="col-md-1 form-control-label">Date To</label>
                        <div class="col-md-2">
                          {{ html()->input('text','date_to')
                              ->class('form-control')
                              ->value(old('date_to'))
                              ->required()
                            }}
                        </div>
                        <label for="enginner" class="col-md-1 form-control-label">Engineer </label>
                        <div class="col-md-2">
                          {{ html()->select('enginner')
                                   ->class('form-control')
                                   ->options($enginner)
                                   ->placeholder('ALL')
                                }}
                        </div>

                        <div class="col-md-2 text-left nowrap">
                          <input type="button" id='btn_view' class="btn btn-primary" value="View">
                          <input type="button" class="btn btn-info btn_print" value="Print">
                        </div>

                        <div class="col-md-1" id="div_export">
                        </div>
                      </div>

                    </div>
              </div>

              <br>
              <div class="row border report_view">
                    <div class="col">
              <div class="table-responsive">
              <table border="0" class="table compact" id="tbl_report" width="100%">
                <thead>
                    <th>No</th>
                    <th>Paid Date</th>
                    <th>Receipt#</th>
                    <th>Customer</th>
                    <th>Engineer</th>
                    <th class="text-right">Labor Fee</th>
                    <th class="text-right">Parts Revenue</th>
                    <th class="text-right">Parts Cost</th>
                    <th class="text-right">Labor Cost</th>
                    <th class="text-right">Total Revenue</th>
                    <th class="text-right">Profit</th>
                    <th class="text-right">Margin %</th>
                </thead>
                <tfoot align="right">
                  <tr>
                    <th class="text-left"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
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

      var oTable=$('#tbl_report').DataTable({

        'fixedHeader': true,
          processing: true,
          // false meant the client never sent draw/start/length, so the
          // server tried to return every matching row in one response —
          // fine for a handful of test rows, but at real volume (500+ paid
          // tickets in a typical month) this just hangs until PHP's
          // execution limit kills it. The controller already returns a
          // proper yajra server-side response; the client just needs to
          // ask for it that way.
          serverSide: true,
          ajax: {
            url: '{{ route('admin.revenue.report') }}',
            data: function(data) {
              data.ajax = 'ajax';
              data.date_fr = $('#date_fr').val();
              data.date_to = $('#date_to').val();
              data.enginner = $('#enginner').val();
            }

          },
          columnDefs: [
            { targets: [5,6,7,8,9,10,11], className: 'dt-body-right' },
          ],
          paging:   false,
          ordering: false,
          info:     false,
          searching: false,
          columns: [
                   { data: 'no', render: function (data, type, row, meta) {
                          return meta.row + meta.settings._iDisplayStart + 1;
                        },
                   } ,
                   { data: 'paydate', name: 'paydate' },
                   { data: 'receiptno', name: 'receiptno' },
                   { data: 'customername', name: 'customername' },
                   { data: 'enginnername', name: 'enginnername' },
                   { data: 'chargeamount', name: 'chargeamount' },
                   { data: 'parts_revenue', name: 'parts_revenue' },
                   { data: 'parts_cost', name: 'parts_cost' },
                   { data: 'labor_cost', name: 'labor_cost' },
                   { data: 'revenue', name: 'revenue' },
                   { data: 'profit', name: 'profit' },
                   { data: 'margin', name: 'margin',
                     render: function(data, type, row){
                       return data + '%';
                     }
                   },
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api();

                    // Under serverSide:true, api.column(x).data() only holds
                    // the CURRENT PAGE's rows — summing it here would show a
                    // 10-row total on a report that may match 500+ tickets.
                    // The server computes true totals over the full filtered
                    // set (see the 'totals' key on the raw ajax response).
                    var json = api.ajax.json();
                    var t = (json && json.totals) ? json.totals : {
                      labor_fee:0, parts_revenue:0, parts_cost:0, labor_cost:0, revenue:0, profit:0, margin:0
                    };

                    $( api.column(0).footer() ).html('Total');
                    $( api.column(5).footer() ).html('$' + Number(t.labor_fee).toFixed(2));
                    $( api.column(6).footer() ).html('$' + Number(t.parts_revenue).toFixed(2));
                    $( api.column(7).footer() ).html('$' + Number(t.parts_cost).toFixed(2));
                    $( api.column(8).footer() ).html('$' + Number(t.labor_cost).toFixed(2));
                    $( api.column(9).footer() ).html('$' + Number(t.revenue).toFixed(2));
                    $( api.column(10).footer() ).html('$' + Number(t.profit).toFixed(2));
                    $( api.column(11).footer() ).html(t.margin + '%');
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
                                    order : 'index',
                                    page : 'all',
                                    search : 'none'
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
