@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.report_enginner.title'))

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
                <h4 class="card-title">@lang('strings.backend.report_enginner.title')</h4>
            </div><!--card-header-->
            <div class="card-body">

              <div class="row border">
                    <div class="col">
                      <br>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">

                          <label for="status" class="col-md-2 form-control-label">Status</label>
                          <div class="col-md-2">
                            {{ html()->select('status')
                                     ->class('form-control')
                                     ->options($status)
                                     ->value('')
                                     ->placeholder('Select one option')

                                  }}

                          </div>
                          <label for="limit" class="form-control-label col-md-1">Busy Over</label>
                          <input type="number" class="form-control col-md-2" value="5" id="txt_limit" name="txt_limit" min="0">

                          <div class="text-right text-nowrap">

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
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
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

//  /* data: {'ajax':'ajax','date_fr':$('#date_fr').val(),'date_to':$('#date_to').val()}, */

      var groupColumn = 1;
      var oTable=$('#tbl_report').DataTable({

        'fixedHeader': true,
          processing: true,
          serverSide: false,
          ajax: {
            url: '{{ route('admin.enginner.report') }}',
            data: function(data) {
              data.ajax = 'ajax';
              data.status = $('#status').val();
              data.limit = $('#txt_limit').val();
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
                   { data: 'first_name', name: 'first_name' },
                   { data: 'last_name', name: 'last_name' },
                   { data: 'email', name: 'email' },
                   { data: 'roles_label', name: 'roles_label' },

                ],


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
    if($('#status').val()=='')
      alert('Please select one option first.');
    else
      oTable.ajax.reload();

  });

  $('.btn_print').on("click",function(e){
    e.preventDefault();
    $('div.report_view').print();
  });

})
</script>
  @endsection
