@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.serviceinvoice.title'))

@section('content')
@push('after-styles')
<link href="{{ asset('css/paper-css.css') }}" rel="stylesheet">
<style>@page { size: A5 }</style>

<style>

.table tr td{
  padding:6px !important;
}
/*
h4.title {
  overflow: hidden;
  text-align: center;
}

h4.title:before,
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
  content:'\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0';
  text-decoration: line-through;
  margin: auto 0.5em;
}

table.tbl_invoice tbody tr td {
  padding-left:0px;
  padding-top: 0px;
}

</style>
@endpush
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('strings.backend.serviceinvoice.title')</h4>
                </div><!--card-header-->
                <div class="card-body ppp">
                      <div class="A5" id="print">
                        <section class="sheet padding-10mm">

                          <!-- Write HTML just like a web page -->

                        <article>
                          <address style="font-size:12px;margin-top:2px">
                            <center>
                  Tel: 016 808 777/092 577 092
                        </center>
                        </address>
                        </article>
                        <h4 class="title"><span>SERVICE INVOICE</span></h4>
                        <div class="row">
                          <table border="0" class="table table-borderless tbl_invoice">
                            <tbody>
                            <tr>
                              <td width="20%" nowrap>Customer Name:</td>
                              <td width="40%">{{ $service->customer->fullname }}</td>
                              <td class="text-nowrap">Invoice No:</td>
                              <td class="text-right">{{ $service->receiptno }} </td>
                            </tr>
                            <tr>
                              <td width="20%" nowrap>Customer Phone:</td>
                              <td width="40%">{{ $service->phone }}</td>
                              <td>Date:</td>
                              <td class="text-right text-nowrap">{{ $service->date }} {{ $service->time }}</td>

                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                              <td>&nbsp;</td>
                              <td>RMA #:</td>
                              <td class="text-right text-nowrap">{{ $service->rmano }} </td>
                            </tr>
                          </tbody>
                          </table>
                        </div>

                        <div class="row">

                            <table border="1" width=100% class="table">
                                <thead>
                                    <tr style="padding:0px">
                                        <td style="padding:0px !important">No</td>
                                        <td style="padding:0px !important">Description</td>
                                        <td style="padding:0px !important">Qty</td>
                                        <td style="padding:0px !important">Price</td>
                                        <td style="padding:0px !important">Total</td>
                                    </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td class="text-center">
                                      &nbsp;<br>&nbsp;<br>
                                      {{ '1' }}
                                    </td>
                                    <td>
                                      Model: {{ $service->model }} <br>
                                      S/N: {{ $service->serialno }} <br>
                                      Problem: {{ $service->problem()->pluck('problem')->implode(' ') }}
                                    </td>
                                    <td>
                                      &nbsp;<br>&nbsp;<br>
                                      1
                                    </td>
                                    <td class="text-nowrap text-right">
                                      &nbsp;<br>&nbsp;<br>
                                      $ {{ number_format($service->chargeamount,2,".",",") }}
                                    </td>
                                    <td class="text-nowrap text-right">
                                      &nbsp;<br>&nbsp;<br>
                                      $ {{ number_format($service->chargeamount,2,".",",") }}
                                    </td>

                                  </tr>
                                  @php
                                  $total=$service->chargeamount;
                                  @endphp
                                  @foreach ($service->service_rma as $k=>$service_rma)
                                    @php
                                      $subtotal=($service_rma->sellingprice * $service_rma->quantity);
                                      $total=$total + $subtotal;
                                    @endphp
                                    <tr>
                                      <td class="text-center">{{ ($k+2) }}</td>
                                      <td>{{ $service_rma->item_name }}</td>
                                      <td>{{ $service_rma->quantity }}</td>
                                      <td class="text-nowrap text-right">$ {{ number_format($service_rma->sellingprice,2,".",",") }}</td>
                                      <td class="text-nowrap text-right">$ {{ number_format($subtotal,2,".",",") }}</td>

                                    </tr>
                                  @endforeach

                                  <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                    @if(!empty($service->description))
                                    <strong><u>Note:</u></strong><br>
                                    {!! str_replace("\n", "<br />", $service->description) !!}
                                    @endif
                                    </td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td colspan="4" class="text-right" style="border-bottom-style:hidden !important;border-left-style:hidden !important">
                                      <strong>Total</strong>
                                    </td>
                                    <td class="text-nowrap text-right">
                                      <strong>
                                      $ {{ number_format($total,2,".",",") }}
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
                        <label style="display: flex; align-items: center;">
    			<input type="checkbox" style="margin-right: 5px;"> បានអាន និងយល់ព្រម </label>
                          
                           </p>
                        </div>
                        <div class="row" style="font-size:95%">
                          <div class="col" style="margin-left:0px;padding-left:0px">
                              <p class="text-nowrap">ឈ្មោះអតិថិជន <p>..................................</p></p>
                          </div>
                          <div class="col text-center">
                             <p class="text-nowrap">អ្នកទទួលទំនិញ <p>..................................</p></p>
                         </div>
                          <div class="col text-right">
                              <p class="text-nowrap">ជាងជួសជុល <p>..................................</p></p>
                          </div>

                        </div>
                        <div class="row" style="font-size:95%">
                          <div class="col" style="margin-left:0px;padding-left:0px;">
                              <p>អតិថិជន​​ ​មកទទួលទំនិញ <p>..................................</p></p>
                          </div>
                          <div class="col text-center">
                             <p>ឈ្មោះអ្នក ប្រគល់ទំនិញ <p>..................................</p></p>
                         </div>
                          <div class="col text-right">
                              <p>ផ្នែកត្រួតពិនិត្យ <p>..................................</p></p>
                          </div>

                        </div>
                        </section>
                      </div>


                    </div><!--card-body-->
                    <div class="card-footer clearfix">
                                    <div class="row">
                                      <div class="col">
                                          <a class="btn btn-danger btn-sm"  href="{{ route('admin.service.index') }}">Back to List</a>
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
