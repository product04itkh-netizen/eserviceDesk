@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.report_transfer.title'))

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
                <h4 class="card-title">@lang('strings.backend.report_transfer.title')</h4>
            </div><!--card-header-->
            <div class="card-body">

              <div class="row border">
                    <div class="col">
                      <br>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                        <label for="receiptno" class="col-md-2 form-control-label">Date Tracking </label>
                        <div class="col-md-2">
                          {{ html()->input('text','date_fr')
                              ->class('form-control')
                              ->value(old('date_fr'))
                              ->required()

                            }}
                        </div>
                        <label for="assignto" class="col-md-1 form-control-label">To</label>
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
                        <label for="receiptno" class="col-md-2 form-control-label">Date Cleared</label>
                        <div class="col-md-2">
                          {{ html()->input('text','date_fr1')
                              ->class('form-control')
                              ->value(old('date_fr1'))
                              ->disabled()
                            }}
                        </div>
                        <label for="assignto" class="col-md-1 form-control-label">To</label>
                        <div class="col-md-2">
                          {{ html()->input('text','date_to1')
                              ->class('form-control')
                              ->value(old('date_to1'))
                              ->disabled()
                            }}
                        </div>

                      </div>
                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                        {{ html()->label('Vendor <span class="required">*</span>','vendor_id')
                          ->class('col-md-2 form-control-label')

                        }}
                        <div class="col-md-2">
                          {{ html()->select('vendor_id')
                              ->class('form-control')
                              ->options($vendors)
                              ->value('')
                              ->placeholder('Select Vendor')
                              ->required()
                               }}
                        </div>
                        <label for="rma_paid" class="col-md-1 form-control-label">CN Type</label>
                        <div class="col-md-2">
                          {{ html()->select('credit_note_type')
                                   ->class('form-control')
                                   ->options($credit_note_type)
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
                    <th>Date Tracking</th>
                    <th>Credit Note Type</th>
                    <th>Reason</th>
                    <th>Date Cleared</th>
                    <th>Service No</th>
                    <th>Serial No</th>
                    <th>Model</th>
                    <th>Problem</th>
                    <th>CN Number</th>
                    <th>CN Price</th>
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
  $('#credit_note_type').on('change',function(){

    if($(this).val()=='Clear Credit Note'){
      $('input[name="date_fr1"]').prop( "disabled", false );
      $('input[name="date_to1"]').prop( "disabled", false );
    }else {
      $('input[name="date_fr1"]').prop( "disabled", true );
      $('input[name="date_to1"]').prop( "disabled", true );
    }

  });

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

  $('#date_fr1').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
  });

  $('#date_to1').datepicker({
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
            url: '{{ route('admin.transfer.report') }}',
            data: function(data) {
              data.ajax = 'ajax';
              data.date_fr = $('#date_fr').val();
              data.date_to = $('#date_to').val();
              data.vendor_id=$('#vendor_id').val();

              if($('#credit_note_type').val()=='Clear Credit Note'){
                data.date_fr1 = $('#date_fr1').val();
                data.date_to1 = $('#date_to1').val();
              }else{
                data.date_fr1 = '';
                data.date_to1 = '';
              }
              data.credit_note_type = $('#credit_note_type').val();
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
                  { data: 'transferdate', name: 'transferdate'},
                  /*{ data: 'date',
                     type: 'num',
                     render: {
                        _: 'display',
                        sort: 'timestamp',

                    }},
                    */
                   { data: 'type', name: 'type' },
                   { data: 'reason', name: 'reason' },
                   { data: 'cleardate', name: 'cleardate' },
                   { data: 'service_no', name: 'service_no' },
                   { data: 'serialno', name: 'serialno' },
                   { data: 'model', name: 'model' },
                   { data: 'problem', name: 'problem' },
                   { data: 'cnno', name: 'cno' },
                   { data: 'cnprice', name: 'cnprice' },

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
    oTable.ajax.reload();

  });

  $('.btn_print').on("click",function(e){
    e.preventDefault();
    $('div.report_view').print();
  });

})
</script>
  @endsection
