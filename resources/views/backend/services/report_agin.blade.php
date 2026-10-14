@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.report_agin.title'))

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
                <h4 class="card-title">@lang('strings.backend.report_agin.title')</h4>
            </div><!--card-header-->
            <div class="card-body">

              <div class="row border">
                    <div class="col">
                      <br>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                          <label for="date_option" class="col-md-1 form-control-label">Date Option</label>
                          <div class="col-md-2">


                            {{ html()->select('date_option')
                                     ->class('form-control')
                                     ->options($date_option)
                                     ->value('')
                                     ->placeholder('Please select one option')

                                  }}

                          </div>
                          <label for="category" class="col-md-1 form-control-label">Category</label>
                          <div class="col-md-2">
                            {{ html()->select('category')
                                     ->class('form-control')
                                     ->options($category)
                                     ->value('0')

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
              <table border="0" class="table compact" id="tbl_report" width="100%">
                <thead>
                    <th>No</th>
                    <th>Date</th>
                    <th>Receipt#</th>
                    <th>Machine Name/Model</th>
                    <th>Customer</th>
                    <th>Category</th>
                    <th>Problem</th>
                    <th>Status</th>
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
          serverSide: false,
          ajax: {
            url: '{{ route('admin.agin.report') }}',
            data: function(data) {
              data.ajax = 'ajax';
              data.date_option = $('#date_option').val();
              data.status= $('#status').val();
              data.category = $('#category').val();
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
                   { data: 'receiptno', name: 'receiptno' },
                   { data: 'model', name: 'model' },
                   { data: 'customername', name: 'customername' },
                   { data: 'category', name: 'category' },
                   { data: 'problem', name: 'problem' },
                   { data: 'status', name: 'status' },
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
                        .column( 8 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );


                    // Update footer by showing the total with the reference of the column index


               $( api.column( 0 ).footer() ).html('Total');
                    $( api.column( 8 ).footer() ).html('$' + amountTotal);
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
                              total[group_assoc]=total[group_assoc]+intVal(api.column(8).data()[i]);
                          }else{
                              total[group_assoc]=intVal(api.column(8).data()[i]);
                          }

                          if ( last !== group.display ) {
                              $(rows).eq( i ).before(
                                  '<tr class="group"><td colspan="7">'+ group.display +'</td><td class="text-right '+ group_assoc +'"></td></tr>'
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
