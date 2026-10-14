@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.service.title'))


@section('content')
  @push('after-styles')
  <style>

   .toolbar {
    float: left;
    }
    .text-nowrap {color:blue;white-space: nowrap !important}

  </style>
  @endpush

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <div class="row">
                 <div class="col-sm-5">
                  <h4 class="card-title">@lang('strings.backend.service.title')</h4>
                 </div>
                 @if(Auth()->user()->isServiceAdmin() || Auth()->user()->isServiceReceptionist() || Auth()->user()->isAdmin())
                  <div class="col-sm-7">
                      <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                        <a href="{{route('admin.service.create')}}" class="btn btn-success ml-1" data-toggle="tooltip" title="" data-original-title="Create New"><i class="fas fa-plus-circle"></i> Service</a>
                      </div><!--btn-toolbar-->
                  </div>
                  @endif
                </div>
                </div><!--card-header-->

                <div class="card-body">


          <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <table class="table display table-bordered" id='tbl_service' width="100%">
                        <thead class="thead-light">
                        <tr>
                            <th class="no-sort">Actions</th>
                            <th class="text-nowrap">Receipt#</th>
                            <th>Customer</th>
                            <th class="no-sort">Phone</th>
                            <th>Date</th>
                            <th>TAT</th>
                            <th>Serial#</th>
                            <th>Model</th>
                            <th>Problem</th>
                            <th>Solution</th>
                            <th>Receiver</th>
                            <th>Status</th>
                            <th>Charge</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Warranty</th>
                            <th>Transfer</th>
                            <th>Delivered</th>
                            <th>Assign</th>

                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                  </div>
            </div>
          </div>

          </div><!--card-body-->



                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->
@endsection

@section('custom_script')
<script>


       $(document).ready(function(){

         $.fn.dataTable.ext.buttons.alert = {
            className: 'buttons-alert',

            action: function ( e, dt, node, config ) {
                par_status= node.text();
                $("div.toolbar").html('<strong>Status:</strong> ' + node.text() + '.');
                oTable.clear().draw();
                oTable.ajax.reload();
            }
        };

         function addDeleteForms() {
           $('#tbl_service tbody').find('[data-method="delete"]').append(function () {
             if (!$(this).find('form').length > 0) {
               return "\n<form action='" + $(this).attr('href') + "' method='POST' name='delete_item' style='display:none'>\n" + "<input type='hidden' name='_method' value='" + $(this).attr('data-method') + "'>\n" + "<input type='hidden' name='_token' value='" + $('meta[name="csrf-token"]').attr('content') + "'>\n" + '</form>\n';
             } else {
               return '';
             }
           }).attr('href', '#').attr('style', 'cursor:pointer;').attr('onclick', '$(this).find("form").submit();');

           $('#tbl_service tbody').find('[data-method="assign"]').append(function () {
             if (!$(this).find('form').length > 0) {
               return "\n<form action='" + $(this).attr('href') + "' method='POST' name='assign_item' style='display:none'>\n" + "<input type='hidden' name='_token' value='" + $('meta[name="csrf-token"]').attr('content') + "'>\n" + '</form>\n';
             } else {
               return '';
             }
           }).attr('href', '#').attr('style', 'cursor:pointer;').attr('onclick', '$(this).find("form").submit();');

           }


          $('#tbl_service thead tr').clone(true).appendTo('#tbl_service thead' );
          $('#tbl_service thead tr:eq(1) th').each( function (i) {
              var title = $(this).text();
              if(title!='Actions')
                $(this).html( '<input type="text" style="width:70px;padding:0px !Important;margin:0px" placeholder="Search '+title+'" />' );
              else
                $(this).html('');

              $( 'input', this ).on( 'keyup change', function () {
                  if ( oTable.column(i).search() !== this.value ) {
                    var regex = '\\b' + this.value + '\\b';
                      oTable.column(i)
                          .search(regex,true,false)
                          .draw();
                  }
              } );
          } );

          var par_status='';

           var oTable=$('#tbl_service').DataTable({
              "dom": '<"row"<"col-md-4"B><"col-md-4"<"toolbar">><"col-md-4"f>>rtlp',
              //'dom': '<"toolbar">Bfrtip',
              'buttons': [
                    //'copyHtml5',
                    //'excelHtml5',
                    //'csvHtml5',
                    //'columnsToggle',
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
                                          columns: [1, 2, 3, 4, 5, 6, 7, 8, 9,10,11],
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
                    },
                    {
                        extend: 'collection',
                        text: 'Status',
                        autoClose: true,

                        buttons: [
                            {
                                extend: 'alert',
                                text: 'ALL'
                            },
                            {
                                extend: 'alert',
                                text: 'Not Start'
                            },
                            {
                                extend: 'alert',
                                text: 'Start'
                            },
                            {
                                extend: 'alert',
                                text: 'Pending'
                            },
                            {
                                extend: 'alert',
                                text: 'Progress'
                            },
                            {
                                extend: 'alert',
                                text: 'Complete'
                            },
                            {
                                extend: 'alert',
                                text: 'Incomplete'
                            },
                            {
                                extend: 'alert',
                                text: 'Cancel'
                            },




                        ],
                        /*
                        buttons: [
                            {
                                text: 'ALL',

                                action: function ( e, dt, node, config ) {
                                    dt.columns(10).search("" ).draw();
                                }
                            },
                            {
                                text: 'Not Start',
                                action: function ( e, dt, node, config ) {
                                    dt.columns(10).search("not start" ).draw();
                                  //par_status = 'pending';
                                  //console.log(node.text());
                                  //oTable.ajax.reload();
                                }
                            },
                            {
                                text: 'Start',
                                action: function ( e, dt, node, config ) {
                                  var regex = '\\b' + 'start' + '\\b';
                                      dt.columns(10).search(regex, true, false).draw();
                                  //  dt.columns(10).search("start" ).draw();
                                  //par_status = 'pending';
                                  //console.log(node.text());
                                  //oTable.ajax.reload();
                                }
                            },
                            {
                                text: 'Pending',
                                action: function ( e, dt, node, config ) {
                                    dt.columns(10).search("pending" ).draw();
                                  //par_status = 'pending';
                                  //console.log(node.text());
                                  //oTable.ajax.reload();
                                }
                            },
                            {
                                text: 'Progress',
                                action: function ( e, dt, node, config ) {
                                    dt.columns(10).search("progress" ).draw();
                                }
                            },
                            {
                                text: 'Complete',
                                action: function ( e, dt, node, config ) {
                                var regex = '\\b' + 'complete' + '\\b';
                                    dt.columns(10).search(regex, true, false).draw();
                                }
                            },
                            {
                                text: 'Incomplete',
                                action: function ( e, dt, node, config ) {
                                    dt.columns(10).search("incomplete" ).draw();
                                }
                            },
                            {
                                text: 'Cancel',
                                action: function ( e, dt, node, config ) {
                                    dt.columns(10).search("cancel" ).draw();
                                }
                            },
                            {
                                text: 'Delivered',
                                action: function ( e, dt, node, config ) {
                                    dt.columns(10).search("delivered" ).draw();
                                }
                            },

                        ]
                          */


                    }


                ],
              'order': [],
              'orderCellsTop': true,
              'fixedHeader': true,
               "fnInitComplete": function() {
                   $('#tbl_service tbody tr').each(function(){
                   $(this).find('td:eq(6)').attr('nowrap', 'nowrap');
                 });

                 //addDeleteForms();

               },

               "fnDrawCallback": function(settings){
                 addDeleteForms();
               },

               "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                  //console.log(aData['status']);
                if ( aData['status'] == "Not start" )
                    {

                        $('td', nRow).css('background-color', '#f39c12');
                        $('td', nRow).css('color', '#fff');

                        $('td:nth-child(1)', nRow).css('background-color', '#fff');
                    }
                },
               "language": {
            			"decimal": ",",
            			"thousands": "."
            		},
               columnDefs: [
                 { type: 'currency', targets: 8  },
                 {targets: 1, className: 'text-nowrap'},
                 {
                        "targets": 'no-sort',
                        "orderable": false,
                  }
               ],
               processing: true,
               serverSide: false,
              // ajax: "route('admin.service.index',['ajax'=>'ajax','status'=> '" + '1'  + "']) !!}",
                ajax: {
                url: "{{ route('admin.service.index') }}",
                data: function (d) {
                d.ajax = 'ajax';
                d.status = par_status ;
                }
              },
               columns: [
                        { data: 'action', name: 'action' },
                        { data: 'receiptno', name: 'receiptno' },
                        { data: 'customername', name: 'customername' },
                        { data: 'phone', name: 'phone' },
                        { data: 'date', name: 'date' },
                        { data: 'duration', name: 'duration' },
                        { data: 'serialno', name: 'serialno' },
                        { data: 'model', name: 'model' },
                        { data: 'problem', name: 'problem' },
                        { data: 'solution', name: 'solution' },
                        { data: 'receiver', name: 'receiver' },
                        { data: 'status', name: 'status' },
                        { data: 'chargetype', name: 'chargetype' },
                        { data: 'chargeamount', name: 'chargeamount' },
                        { data: 'payment', name: 'payment' },
                        { data: 'warrenty', name: 'warrenty' },
                        { data: 'transfer', name: 'transfer' },
                        { data: 'delivered', name: 'delivered' },
                        { data: 'enginnername', name: 'enginnername' }

                     ],
                 //scrollY: 200,
                 scroller: {
                     loadingIndicator: true
                 },
            });
              $("div.toolbar").html('<strong>Status:</strong> Not start, Start, Pending.');
         });


</script>

@endsection
