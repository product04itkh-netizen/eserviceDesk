@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.enginner_busy.title'))

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
                <h4 class="card-title">@lang('strings.backend.enginner_busy.title')</h4>
            </div><!--card-header-->
            <div class="card-body">

              <div class="row border">
                    <div class="col">
                      <br>

                      <div class="form-group row{{ $errors->has('problem') ? ' has-error' : '' }}">

                          <!--   <input type="button" class="btn btn-info btn_print" value="Print"> -->
                          <div class="col-md-12 text-right" id="div_export">
                            <input type="button" class="btn btn-primary" id="btn_pay" value="Save">
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

                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="text-nowrap">Limit on Busy</th>

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


//  /* data: {'ajax':'ajax','date_fr':$('#date_fr').val(),'date_to':$('#date_to').val()}, */
      var chk=[];


      var oTable=$('#tbl_report').DataTable({

        'fixedHeader': true,
          processing: true,
          serverSide: false,
          ajax: {
            url: '{{ route('admin.enginner_busy.index') }}',
            data: function(data) {
              data.ajax = 'ajax';
              //data.status1= $('#status1').val();
            //  data.status2= $('#status2').val();
              data.limit=chk;
            }

          },

          columnDefs: [
          //  { targets: [3,7,19], className: 'dt-nowrap' },
          //  { "visible": false, "targets": groupColumn },
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
                   { data: 'full_name', name: 'full_name'},
                   { data: 'email', name: 'email' },
                   { data: 'status', name: 'status' },
                   { data: 'action', name: 'action'},


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
    var chks=$("input[name='chk[]']");

    if(chks.length==0) {
      alert('You must select any transaction to update.');
      return ;
    }
    var a=confirm('Are you sure to process ?');
    if(!a)
        return ;


    chk={};
    //chk_ref={};
    if(chks.length>0){

      chks.each(function(index){
        var sThisVal = $(this).val();
        var key=$(this).attr('uid');
        //chk_ref[key]=$(this).closest('tr').find("input[name='chk_ref[]']").val();
        chk[key]=sThisVal;
    });

    //console.log(chk);
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
