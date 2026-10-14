@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.calling.title'))

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
                <h4 class="card-title">@lang('strings.backend.calling.title')</h4>
            </div><!--card-header-->
            <div class="card-body">

              <div class="row border">
                    <div class="col">
                      <br>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">
                        <label for="datetype" class="col-md-2 form-control-label">Update Status</label>
                        <div class="col-md-2">
                          @php
                            $status=[1=>'Successful',2=>'Unsuccesful'];
                          @endphp
                          {{ html()->select('status2')
                                   ->class('form-control')
                                   ->options($status)


                                }}

                        </div>

                          <!--   <input type="button" class="btn btn-info btn_print" value="Print"> -->
                          <div class="col-md-2" id="div_export">
                            <input type="button" class="btn btn-primary" id="btn_pay" value="Update Status">
                          </div>
                          <div class="col-md-4">
                            <input type="button" id='btn_view' class="btn btn-secondary" value="Refesh">
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

                    <th>Calling</th>
                    <th>Calling Date</th>
                    <th>Calling Status</th>
                    <th class="text-nowrap">Receipt #</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Date</th>
                    <th>TAT</th>
                    <th class="text-nowrap">Serial #</th>
                    <th>Model</th>
                    <th>Problem</th>
                    <th>Solution</th>
                    <th>Receiver</th>
                    <th>Receipt Status</th>

                    <th>Charge</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Warrenty</th>
                    <th>Transfer</th>
                    <th>Engineer</th>
                </thead>
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

  $('#cndate').datepicker({
            format:"dd/mm/yyyy",
            uiLibrary:"bootstrap4",
            iconsLibrary:"materialicons"
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
//  /* data: {'ajax':'ajax','date_fr':$('#date_fr').val(),'date_to':$('#date_to').val()}, */
      var chk=[];

      var groupColumn = 1;
      var oTable=$('#tbl_report').DataTable({

        'fixedHeader': true,
          processing: true,
          serverSide: false,
          ajax: {
            url: '{{ route('admin.service.calling') }}',
            data: function(data) {
              data.ajax = 'ajax';
              //data.status1= $('#status1').val();
              data.status2= $('#status2').val();
              data.chk=chk;
            }

          },

          columnDefs: [
            { targets: [3,7,19], className: 'dt-nowrap' },
            { "visible": false, "targets": groupColumn },
          // { "orderable": false, "targets": [0,2,3,4,5,6,7,8,9,10]}
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
                   { data: 'action', name: 'action'},
                   { data: 'calling_date', name: 'calling_date'},
                   { data: 'calling_status', name: 'calling_status' },
                   { data: 'receiptno', name: 'receiptno' },
                   { data: 'customername', name: 'customername' },
                   { data: 'phone', name: 'phone' },
                   { data: 'date', name: 'date' },
                   { data: 'tat', name: 'tat' },
                   { data: 'serialno', name: 'serialno'},
                   { data: 'model', name: 'model'},
                   { data: 'problem', name: 'problem'},
                   { data: 'solution', name: 'solution'},
                   { data: 'receiver', name: 'receiver'},
                   { data: 'status1', name: 'status1'},
                   { data: 'chargetype', name: 'chargeamount'},
                   { data: 'chargeamount', name: 'chargeamount'},
                   { data: 'payment', name: 'payment'},
                   { data: 'warrenty', name: 'warrenty'},
                   { data: 'transfer', name: 'transfer'},
                   { data: 'engineer', name: 'engineer'},

                ],
                "initComplete":function( settings, json){

                  //console.log(json.data);
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
    var rma_chk=$("input[name='chk_pay[]']:checked");

    if(rma_chk.length==0) {
      alert('You must select any transaction to update.');
      return ;
    }
    var a=confirm('Are you sure to process ?');
    if(!a)
        return ;


    chk={};
    //chk_ref={};
    if(rma_chk.length>0){

      rma_chk.each(function(index){
        var sThisVal = (this.checked ? "1" : "0");
        var key=$(this).val();
        //chk_ref[key]=$(this).closest('tr').find("input[name='chk_ref[]']").val();
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
