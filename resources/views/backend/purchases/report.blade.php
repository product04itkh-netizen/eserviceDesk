@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.reportpurchase.title'))

@section('content')
<style>
tr.group,
tr.group:hover {
    background-color: #ddd !important;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">@lang('strings.backend.reportpurchase.title')</h4>
            </div><!--card-header-->
            <div class="card-body">

              <div class="row border">
                    <div class="col">
                      <br>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="receiptno" class="col-md-2 form-control-label">Date From <span class="required">*</span></label>
                          <div class="col-md-2">
                            {{ html()->input('text','date_fr')
                                ->class('form-control')
                                ->value(old('date_fr'))
                                ->required()

                              }}
                          </div>
                          <label for="assignto" class="col-md-2 form-control-label">Date To <span class="required">*</span></label>
                          <div class="col-md-2">
                            {{ html()->input('text','date_to')
                                ->class('form-control')
                                ->value(old('date_to'))
                                ->required()
                              }}
                          </div>

                      </div>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="receiptno" class="col-md-2 form-control-label">Payment <span class="required">*</span></label>
                          <div class="col-md-2">
                            {{ html()->select('payment')
                                     ->class('form-control datepicker')
                                     ->options(array('No Pay','Paid'))
                                     ->value('')
                                     ->placeholder('ALL')

                                  }}
                          </div>
                          <label for="vendor" class="col-md-2 form-control-label">Vendor <span class="required">*</span></label>
                          <div class="col-md-2">
                            {{ html()->select('vendor')
                                     ->class('form-control datepicker')
                                     ->options($vendor)
                                     ->placeholder('ALL')

                                  }}
                          </div>

                          <div class="col-md-4 text-center">
                            <input type="button" id='btn_view' class="btn btn-primary" value="View Report">
                              <a href="#" class="btn btn-info btn_print">Print</a>
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
                    <th>PO#</th>
                    <th>RMA</th>
                    <th>Supplyer</th>
                    <th>Payment</th>
                    <th>Paydate</th>
                    <th>Item</th>
                    <th>Quanity</th>
                    <th>Price</th>
                    <th>Amount</th>

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
//  /* data: {'ajax':'ajax','date_fr':$('#date_fr').val(),'date_to':$('#date_to').val()}, */

      var groupColumn = 1;
      var oTable=$('#tbl_report').DataTable({

        'fixedHeader': true,
          processing: true,
          serverSide: true,
          ajax: {
            url: '{{ route('admin.stock.report_purchase') }}',
            data: function(data) {
              data.ajax = 'ajax';
              data.date_fr = $('#date_fr').val();
              data.date_to = $('#date_to').val();
              data.payment = $('#payment').val();
              data.vendor = $('#vendor').val();
            //  data.status= $('#status').val();
            }

          },
          columnDefs: [
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
                   //{ data: 'date.display', name: 'date'},
                  { data: 'date',
                     type: 'num',
                     render: {
                        _: 'display',
                        sort: 'timestamp',

                    }},
                   { data: 'id', name: 'id' },
                   { data: 'rma', name: 'rma' },
                   { data: 'supplyer_name', name: 'supplyer_name' },
                   { data: 'payment', name: 'payment' },
                   { data: 'paydate', name: 'paydate' },
                   { data: 'item_name', name: 'item_name'},
                   { data: 'quantity', name: 'quantity'},
                   { data: 'purchaseprice', name: 'purchaseprice'},
                   { data: 'amount', name: 'amount'}

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
                        .column( 10 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );


                    // Update footer by showing the total with the reference of the column index
        	     $( api.column( 0 ).footer() ).html('Total');
                    $( api.column( 10 ).footer() ).html('$' + amountTotal);

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
                          //console.log(api.column(8).data()[i]);

                          group_assoc=group.timestamp.replace(' ',"_");
                        //  total[group_assoc]=total[group_assoc]+intVal(api.column(9).data()[i]);
                          if(typeof total[group_assoc] != 'undefined'){
                              total[group_assoc]=total[group_assoc]+intVal(api.column(10).data()[i]);
                          }else{
                              total[group_assoc]=intVal(api.column(10).data()[i]);
                          }

                          if ( last !== group.display ) {
                              $(rows).eq( i ).before(
                                  '<tr class="group"><td colspan="9">'+ group.display +'</td><td class="text-right '+ group_assoc +'"></td></tr>'
                              );

                              last = group.display;
                          }
                      } );

                      for(var key in total) {
                            $("."+key).html("$"+total[key]);
                        }

                  }

       });




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
