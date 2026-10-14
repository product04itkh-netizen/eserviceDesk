@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.saleorder.title'))

@section('content')
<style>
td.details-control {
    background: url('{{ asset("img/backend/details_open.png") }}') no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url('{{ asset("img/backend/details_close.png") }}') no-repeat center center;
}

table.dataTable.dtr-inline.collapsed>tbody>tr>td.child,table.dataTable.dtr-inline.collapsed>tbody>tr>th.child,table.dataTable.dtr-inline.collapsed>tbody>tr>td.dataTables_empty{cursor:default !important}table.dataTable.dtr-inline.collapsed>tbody>tr>td.child:before,table.dataTable.dtr-inline.collapsed>tbody>tr>th.child:before,table.dataTable.dtr-inline.collapsed>tbody>tr>td.dataTables_empty:before{display:none !important}table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child,table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child{position:relative;padding-left:30px;cursor:pointer}table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before,table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{top:9px;left:4px;height:14px;width:14px;display:block;position:absolute;color:white;border:2px solid white;border-radius:14px;box-shadow:0 0 3px #444;box-sizing:content-box;text-align:center;text-indent:0 !important;font-family:'Courier New', Courier, monospace;line-height:14px;content:'+';background-color:#31b131}table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td:first-child:before,table.dataTable.dtr-inline.collapsed>tbody>tr.parent>th:first-child:before{content:'-';background-color:#d33333}table.dataTable.dtr-inline.collapsed.compact>tbody>tr>td:first-child,table.dataTable.dtr-inline.collapsed.compact>tbody>tr>th:first-child{padding-left:27px}table.dataTable.dtr-inline.collapsed.compact>tbody>tr>td:first-child:before,table.dataTable.dtr-inline.collapsed.compact>tbody>tr>th:first-child:before{top:5px;left:4px;height:14px;width:14px;border-radius:14px;line-height:14px;text-indent:3px}table.dataTable.dtr-column>tbody>tr>td.control,table.dataTable.dtr-column>tbody>tr>th.control{position:relative;cursor:pointer}table.dataTable.dtr-column>tbody>tr>td.control:before,table.dataTable.dtr-column>tbody>tr>th.control:before{top:50%;left:50%;height:16px;width:16px;margin-top:-10px;margin-left:-10px;display:block;position:absolute;color:white;border:2px solid white;border-radius:14px;box-shadow:0 0 3px #444;box-sizing:content-box;text-align:center;text-indent:0 !important;font-family:'Courier New', Courier, monospace;line-height:14px;content:'+';background-color:#31b131}table.dataTable.dtr-column>tbody>tr.parent td.control:before,table.dataTable.dtr-column>tbody>tr.parent th.control:before{content:'-';background-color:#d33333}table.dataTable>tbody>tr.child{padding:0.5em 1em}table.dataTable>tbody>tr.child:hover{background:transparent !important}table.dataTable>tbody>tr.child ul.dtr-details{display:inline-block;list-style-type:none;margin:0;padding:0}table.dataTable>tbody>tr.child ul.dtr-details>li{border-bottom:1px solid #efefef;padding:0.5em 0}table.dataTable>tbody>tr.child ul.dtr-details>li:first-child{padding-top:0}table.dataTable>tbody>tr.child ul.dtr-details>li:last-child{border-bottom:none}table.dataTable>tbody>tr.child span.dtr-title{display:inline-block;min-width:75px;font-weight:bold}div.dtr-modal{position:fixed;box-sizing:border-box;top:0;left:0;height:100%;width:100%;z-index:100;padding:10em 1em}div.dtr-modal div.dtr-modal-display{position:absolute;top:0;left:0;bottom:0;right:0;width:50%;height:50%;overflow:auto;margin:auto;z-index:102;overflow:auto;background-color:#f5f5f7;border:1px solid black;border-radius:0.5em;box-shadow:0 12px 30px rgba(0,0,0,0.6)}div.dtr-modal div.dtr-modal-content{position:relative;padding:1em}div.dtr-modal div.dtr-modal-close{position:absolute;top:6px;right:6px;width:22px;height:22px;border:1px solid #eaeaea;background-color:#f9f9f9;text-align:center;border-radius:3px;cursor:pointer;z-index:12}div.dtr-modal div.dtr-modal-close:hover{background-color:#eaeaea}div.dtr-modal div.dtr-modal-background{position:fixed;top:0;left:0;right:0;bottom:0;z-index:101;background:rgba(0,0,0,0.6)}@media screen and (max-width: 767px){div.dtr-modal div.dtr-modal-display{width:95%}}
</style>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <div class="row">
                 <div class="col-sm-5">
                  <h4 class="card-title">@lang('strings.backend.saleorder.title')</h4>
                 </div>
                  <div class="col-sm-7">
                      <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                        <a href="{{ route('admin.saleorder.create') }}" class="btn btn-success ml-1" data-toggle="tooltip" title="" data-original-title="Create New"><i class="fas fa-plus-circle"></i> New Sale Order</a>
                      </div><!--btn-toolbar-->
                  </div>
                </div>
                </div><!--card-header-->

                <div class="card-body">


                      <div class="row mt-4">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-borderless" id="tbl_sales" width="100%">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Sale Order#</th>
                            <th>Date</th>
                            <th>Ref</th>
                            <th>Description</th>
                            <th>Paid</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
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
/*
          $('#tbl_service thead tr').clone(true).appendTo('#tbl_service thead' );
          $('#tbl_service thead tr:eq(1) th').each( function (i) {
              var title = $(this).text();
              if(title!='Actions')
                $(this).html( '<input type="text" style="width:70px;padding:0px !Important;margin:0px" placeholder="Search '+title+'" />' );
              else
                $(this).html('');
              $( 'input', this ).on( 'keyup change', function () {
                  if ( oTable.column(i).search() !== this.value ) {
                      oTable.column(i)
                          .search( this.value )
                          .draw();
                  }
              } );
          } );
*/
          function addDeleteForms() {
            $('#tbl_sales tbody').find('[data-method="delete"]').append(function () {
              if (!$(this).find('form').length > 0) {
                return "\n<form action='" + $(this).attr('href') + "' method='POST' name='delete_item' style='display:none'>\n" + "<input type='hidden' name='_method' value='" + $(this).attr('data-method') + "'>\n" + "<input type='hidden' name='_token' value='" + $('meta[name="csrf-token"]').attr('content') + "'>\n" + '</form>\n';
              } else {
                return '';
              }
            }).attr('href', '#').attr('style', 'cursor:pointer;').attr('onclick', '$(this).find("form").submit();');

            $('#tbl_sales tbody').find('[data-method="assign"]').append(function () {
              if (!$(this).find('form').length > 0) {
                return "\n<form action='" + $(this).attr('href') + "' method='POST' name='assign_item' style='display:none'>\n" + "<input type='hidden' name='_token' value='" + $('meta[name="csrf-token"]').attr('content') + "'>\n" + '</form>\n';
              } else {
                return '';
              }
            }).attr('href', '#').attr('style', 'cursor:pointer;').attr('onclick', '$(this).find("form").submit();');

            }

           var oTable=$('#tbl_sales').DataTable({
              // "dom": '<"toolbar">frtip',
              'dom': 'Bfrtip',
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
                                        columns: [1, 2, 3, 4]
                                     }
                                  },

                                ],
                                fade: true
                    },
                    {
                        extend: 'collection',
                        text: 'Paid Status',
                        autoClose: true,
                        buttons: [
                            {
                                text: 'ALL',
                                action: function ( e, dt, node, config ) {
                                    dt.columns(5).search("" ).draw();

                                }
                            },
                            {
                                text: 'Paid',
                                action: function ( e, dt, node, config ) {
                                    dt.columns(5).search('yes').draw();
                                }
                            },
                            {
                                text: 'Not Pay',
                                action: function ( e, dt, node, config ) {
                                    dt.columns(5).search("no" ).draw();
                                }
                            },

                      ]
                    }
                  ],
              "fnInitComplete": function() {
                //addDeleteForms();
              },
              "fnDrawCallback": function(settings){
                addDeleteForms();
              },
               processing: true,
               serverSide: true,
               ajax: '{{ route('admin.saleorder.index',['ajax'=>'ajax']) }}',
               columns: [
                        {
                          "className":      'details-control',
                           "orderable":      false,
                           "searchable":     false,
                           "data":           null,
                           "defaultContent": ''
                        },
                        { data: 'id', name: 'id' },
                        { data: 'date', name: 'date' },
                        { data: 'ref', name: 'ref' },
                        { data: 'note', name: 'note' },
                        { data: 'payment', name: 'payment' },
                        { data: 'action', name: 'action' },


                     ],
                     order: [[1, 'desc']]
            });


            function format ( data ) {
                var $markup = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:150px;" width="80%" class="table table-bordered table-striped">';
                $markup += '<thead><tr><th>Item</th><th>Quantity</th><th>Price</th><th>Amount</th></tr></thead><tbody>';
                var subtasks = JSON.parse(data.detail);

                subtasks.forEach(subtask => {
                    $markup += '<tr><td>' + subtask.items.name + '</td>';
                    $markup += '<td>' + subtask.quantity + '</td>';
                    $markup += '<td>' + subtask.saleprice + '</td>';
                    $markup += '<td>' + (subtask.saleprice * subtask.quantity) + '</td>';
                    $markup +='</tr>';
                })

                $markup += '</tbody></table>';

                return $markup;
            }

            $('#tbl_sales tbody').on('click', 'td.details-control', function () {
               var tr = $(this).closest('tr');
               var row = oTable.row( tr );

               if ( row.child.isShown() ) {
                   // This row is already open - close it
                   row.child.hide();
                   tr.removeClass('shown');
               }
               else {
                   // Open this row
                   row.child( format(row.data()) ).show();
                   tr.addClass('shown');
               }
           });

         });

</script>

@endsection
