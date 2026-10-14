@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.cash_transfer.title'))

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
                <h4 class="card-title">@lang('strings.backend.cash_transfer.title') to HQ</h4>
            </div><!--card-header-->
            <div class="card-body">

              <div class="row border">
                    <div class="col">
                      <br>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                        <label for="datetype" class="col-md-1 form-control-label">Date Type</label>
                        <div class="col-md-2">
                          @php
                            $datetype=['Payment Date','Receipt Date'];
                          @endphp
                          {{ html()->select('datetype')
                                   ->class('form-control')
                                   ->options($datetype)


                                }}

                        </div>
                        <label for="serviceno" class="col-md-1 form-control-label">Service #</label>
                        <div class="col-md-2">
                          {{ html()->input('text','serviceno')
                              ->class('form-control')
                              ->value(old('serviceno'))


                            }}
                        </div>


                      </div>

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

                          <div class="col-md-1 text-left nowrap">
                            <input type="button" id='btn_view' class="btn btn-primary" value="View">

                          <!--   <input type="button" class="btn btn-info btn_print" value="Print"> -->

                          </div>

                          <div class="col-md-2" id="div_export">
                            <input type="button" class="btn btn-primary" id="btn_pay" value="Save">
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
                    <th>Service #</th>
                    <th>Receipt Date</th>
                    <th>Model</th>
                    <th>Problem</th>
                    <th>Solution</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Pay Date</th>
                    <th>Transfer to HQ</th>
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
      var chk=[];
      var groupColumn = 1;
      var oTable=$('#tbl_report').DataTable({

        'fixedHeader': true,
          processing: true,
          serverSide: false,
          ajax: {
            url: '{{ route('admin.service.cash_transfer') }}',
            data: function(data) {
              data.ajax = 'ajax';
              data.datetype = $('#datetype').val();
              data.date_fr = $('#date_fr').val();
              data.date_to = $('#date_to').val();

              data.serviceno = $('#serviceno').val();


              data.chk=chk;
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

                  /*{ data: 'date',
                     type: 'num',
                     render: {
                        _: 'display',
                        sort: 'timestamp',

                    }},
                    */

                   { data: 'receiptno', name: 'receiptno' },
                   { data: 'date', name: 'date'},
                   { data: 'model', name: 'model' },
                   { data: 'problem', name: 'problem' },
                   { data: 'solution', name: 'solution' },
                   { data: 'amount', name: 'amount' },
                   { data: 'payment', name: 'payment' },
                   { data: 'paydate', name: 'paydate' },
                   { data: 'paymentchk', name: 'paymentchk' },

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

  });
  //.container().appendTo($('#div_export'));


  $('#btn_view').click(function(){
    oTable.ajax.reload();

  });

  $('#btn_pay').click(function(){
    var rma_chk=$("input[name='chk_pay[]']");

    if(rma_chk.length==0) {
      alert('You dont have any "RMA #" to update.');
      return ;
    }
    var a=confirm('Are you sure to process ?');
    if(!a)
        return ;


    chk={};
    if(rma_chk.length>0){

      rma_chk.each(function(index){
        var sThisVal = (this.checked ? "1" : "0");
        var key=$(this).val();

        chk[key]=sThisVal;
    });
  }
  //  console.log(chk);
//  return ;
    //  chk=['1','2'];
    oTable.ajax.reload();
  })

  $('.btn_print').on("click",function(e){
    e.preventDefault();
    $('div.report_view').print();
  });

})
</script>
  @endsection
