@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.reportservice.title'))

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
                <h4 class="card-title">@lang('strings.backend.reportservice.title')</h4>
            </div><!--card-header-->
            <div class="card-body">

              <div class="row border">
                    <div class="col">
                      <br>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="receiptno" class="col-md-1 form-control-label">Date From </label>
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
                          <label for="status" class="col-md-1 form-control-label">Status</label>
                          <div class="col-md-2">
                            {{ html()->select('status')
                                     ->class('form-control')
                                     ->options($status)
                                     ->value('')
                                     ->placeholder('ALL')

                                  }}

                          </div>
                      </div>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="receiptno" class="col-md-1 form-control-label">Payment </label>
                          <div class="col-md-2">
                            {{ html()->select('payment')
                                     ->class('form-control datepicker')
                                     ->options(array('No Pay','Paid'))
                                     ->value('')
                                     ->placeholder('ALL')

                                  }}
                          </div>
                          <label for="assignto" class="col-md-1 form-control-label">Engineer </label>
                          <div class="col-md-2">
                            {{ html()->select('enginner')
                                     ->class('form-control')
                                     ->options($enginner)
                                     ->placeholder('ALL')

                                  }}
                          </div>
                          @php ($reportby=array('Date In','Status Date','Payment Date'))
                          <label for="reportby" class="col-md-1 form-control-label">Report by </label>
                          <div class="col-md-2">
                            {{ html()->select('reportby')
                                     ->class('form-control')
                                     ->options($reportby)


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
                    <th>Brand</th>
                    <th>Category</th>
                    <th>RMACode</th>

                    <th>Date</th>
                    <th>Receipt#</th>
                    <th>Customer</th>

                    <th>Model</th>
                    <th>Serial#</th>
                    <th>Problem</th>
                    <th>Status</th>
                    <th>Solution</th>
                    <th>Engineer</th>
                    <th>Warranty</th>
                    <th>Paid</th>
                    <th class="text-right">Amount</th>

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

      var groupColumn = 4;
      var oTable=$('#tbl_report').DataTable({

        'fixedHeader': true,
          processing: true,
          serverSide: false,
          ajax: {
            url: '{{ route('admin.service.report') }}',
            data: function(data) {
              data.ajax = 'ajax';
              data.date_fr = $('#date_fr').val();
              data.date_to = $('#date_to').val();
              data.payment = $('#payment').val();
              data.enginner = $('#enginner').val();
              data.status= $('#status').val();
              data.reportby = $('#reportby').val();
            }

          },
          columnDefs: [
            { targets: [4], className: 'dt-nowrap' },
            { targets: -1, className: 'dt-body-right' },
            { "visible": false, "targets": groupColumn },
          /*  { "orderable": false, "targets": [0,2,3,4,5,6,7,8,9,10]} */
          ],
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
                  { data: 'brand', name: 'brand'},
                  { data: 'category', name: 'category'},
                   { data: 'rmacode', name: 'rmacode'},
                  { data: 'date',
                     type: 'num',
                     render: {
                        _: 'display',
                        sort: 'timestamp',

                    }},
                   { data: 'receiptno', name: 'receiptno' },
                   { data: 'customername', name: 'customername' },
                   { data: 'model', name: 'model' },
                   { data: 'serialno', name: 'serialno' },
                   { data: 'problem', name: 'problem' },
                   { data: 'status', name: 'status' },
                   { data: 'solution', name: 'solution' },
                    { data: 'enginnername', name: 'enginnername' },
                   { data: 'warrenty', name: 'warrenty' },
                    { data: 'payment', name: 'payment' },
                   { data: 'chargeamount', name: 'chargeamount' },

                ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // converting to interger to find total
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    // computing column Total of the complete result

        	     var amountTotal = api
                        .column( 15 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );


                    // Update footer by showing the total with the reference of the column index
        	     $( api.column( 0 ).footer() ).html('Total');
                    $( api.column( 15 ).footer() ).html('$' + amountTotal);
                },
                "drawCallback": function ( settings ) {
                      var api = this.api();
                      var rows = api.rows( {page:'all'} ).nodes();
                      var last=null;

                      var intVal = function ( i ) {
                      	          	return typeof i === 'string' ?
                      			          i.replace(/[\$,]/g, '')*1 :
                      			          typeof i === 'number' ?
                      				               i : 0;
                      	          };
                      total=[];

                      api.column(groupColumn, {page:'all'} ).data().each( function ( group, i ) {

                          group_assoc=group.timestamp.replace(' ',"_");
                        //  total[group_assoc]=total[group_assoc]+intVal(api.column(9).data()[i]);
                          if(typeof total[group_assoc] != 'undefined'){
                              total[group_assoc]=total[group_assoc]+intVal(api.column(15).data()[i]);
                          }else{
                              total[group_assoc]=intVal(api.column(15).data()[i]);
                          }

                          if ( last !== group.display ) {
                              $(rows).eq( i ).before(
                                  '<tr class="group"><td colspan="14">'+ group.display +'</td><td class="text-right '+ group_assoc +'"></td></tr>'
                              );

                              last = group.display;
                          }
                      } );
                      for(var key in total) {
                            $("."+key).html("$"+total[key]);
                        }
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
