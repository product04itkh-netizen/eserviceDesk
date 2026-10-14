@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.saleinvoice.title'))

@section('content')
@push('after-styles')
<link href="{{ asset('css/paper-css.css') }}" rel="stylesheet">
<style>@page { size: A5 }</style>
<style>

/*
h4.title {
  overflow: hidden;
  text-align: center;
}

h4:before,
h4.title:after {
  background-color: #000;
  content: "";
  display: inline-block;
  height: 2px;
  position: relative;
  vertical-align: middle;
  width: 50%;
}

h4.title:before {
  right: 0.5em;
  margin-left: -50%;
}

h4.title:after {
  left: 0.5em;
  margin-right: -50%;
}
*/

h4.title {
  overflow: hidden;
  text-align: center;
}

h4.title:before, h4.title:after {
  content:'\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0';
  text-decoration: line-through;
  margin: auto 0.5em;
}

table > thead tr th,table > tbody tr td {
  padding-left:0px;
  padding-top: 2px !important;
  padding-bottom: 2px !important;
  margin:0px;
  border-color: #000 !important;
}

</style>
@endpush
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.saleinvoice.title')</h4>
                </div><!--card-header-->
                <div class="card-body ppp">
                      <div class="A5" id="print">
                        <section class="sheet padding-10mm">

                          <!-- Write HTML just like a web page -->
                        <article>
                          <address style="font-size:12px;padding-left:50px;margin-top:2px">
                          #281, St 128, Kampuchea Krom, Phnom Penh.<br>
Tel: 023 996 199/016 808 777/092 577 092/012 222 420<br>
Email: itshowroomkh@gmail.com, Website:www.itcomputerkh.com</article>
                        </address>
                        <h4 class="title"><span>INVOICE</span></h4>
                        <div class="row">
                          <table border="0" class="table table-borderless tbl_invoice">
                            <tbody>
                            <tr>
                              <td width="20%" nowrap>Customer Name:</td>
                              <td width="40%">{{ $saleorder->customer->fullname }}</td>
                              <td width="20%">Invoice No:</td>
                              <td width="20%" class="text-right">SO-{{ printf("%05d",$saleorder->id) }} </td>
                            </tr>
                            <tr>
                              <td width="20%" nowrap>Customer Phone:</td>
                              <td width="40%">{{ $saleorder->customer->phone }}</td>
                              <td width="20%">Date:</td>
                              <td width="20%" class="text-right">{{ $saleorder->date }} </td>
                            </tr>
                          </tbody>
                          </table>
                        </div>

                        <div class="row">

                            <table width=100% class="table table-bordered">
                                <thead>
                                    <th>No</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </thead>
                                <tbody>
                                  @php
                                    $total=0;
                                  @endphp
                                  @foreach ($saleorder->saleorder_detail as $k=>$saleorder_detail)
                                    @php
                                      $total +=($saleorder_detail->saleprice * $saleorder_detail->quantity);
                                    @endphp
                                  <tr>
                                    <td>{{ ($k+1) }}</td>
                                    <td>{{ $saleorder_detail->items->name }}</td>
                                    <td>{{ $saleorder_detail->quantity }}</td>
                                    <td class="text-right">{{ number_format($saleorder_detail->saleprice,2,".",",") }}</td>
                                    <td class="text-right">{{ number_format(($saleorder_detail->saleprice * $saleorder_detail->quantity),2,".",",") }}</td>

                                  </tr>
                                  @endforeach
                                  <tr height="{{ (238 - (40 * count($saleorder->saleorder_detail) )) }}px">
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                  </tr>

                                  <tr>
                                    <td colspan="4" class="text-right" style="border-bottom-style:hidden !important;border-left-style:hidden !important">
                                      <strong>Total</strong>
                                    </td>
                                    <td nowrap class="text-right">
                                      <strong>
                                        {{ number_format($total,2,".",",") }}
                                      </strong>
                                    </td>

                                  </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="row">
                          <p>
                          <strong>បញ្ចាក់:</strong><br>
                          <span>មិនធានាខុសត្រូវចំពោះបញ្ហាមិនប្រក្រតីណាមួយ ដែលកើតឡើងពេលកំពុងជួសជុល</span><br>
                          <span>មិនធានាខុសត្រូវនូវការបាត់បង់ ម៉ាសុីនណាដែលយកមកជួសជុល ហើយទុកហួសរយះពេល មួយខែ</span><br>
                          <span>មិនធានាលើកម្មវិធី កុំព្យូទ័រ​ និង ឯកសារផ្សេងៗ</span>
                          </p>
                        </div>
                        <div class="row">
                          <div class="col" style="margin-left:0px;padding-left:0px">
                              <p>ឈ្មោះអតិថិជន ............................</p>
                          </div>
                          <div class="col text-right">
                              <p>អ្នកទទួលទំនិញ ............................</p>
                          </div>

                        </div>
                        </section>
                      </div>


                    </div><!--card-body-->
                    <div class="card-footer clearfix">
                                    <div class="row">
                                      <div class="col">
                                          <a class="btn btn-danger btn-sm"  href="{{ route('admin.saleorder.index') }}">Back to List</a>
                                      </div><!--col-->
                                        <div class="col text-right">
                                            <a class="btn btn-danger btn-sm" id="btn_print" href="#">Print</a>
                                        </div><!--col-->
                    </div><!--row-->
                                </div><!--card-footer-->



                </div>
            </div><!--card-->
        </div><!--col-->
    </div><!--row-->

@endsection

@section('custom_script')
<script>
       $(document).ready(function(){
          $('#btn_print').on("click",function(e){
            $('#print').print();
        });
      });
</script>
@endsection
