@extends('backend.layouts.pdf')

@section('title', app_name() . ' | ' . __('strings.backend.reportservice.title'))

@section('content')
<style>
tr.group,
tr.group:hover {
    background-color: #ddd !important;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">@lang('strings.backend.reportservice.title')</h4>
            </div><!--card-header-->
            <div class="card-body">
              <br>
              <div class="row border">
                    <div class="col">
              <table border="0" class="table compact" id="tbl_report" width="100%">
                <thead>
                    <th>No</th>
                    <th>Date</th>
                    <th>Receipt#</th>
                    <th>Customer</th>

                    <th>Model</th>
                    <th>Serial#</th>
                    <th>Problem</th>
                    <th>Status</th>
                    <th>Warrenty</th>
                    <th>Paid</th>
                    <th class="text-right">Amount</th>

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
