@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
<style>

.border-top-primary {
    border-top-color: #5969ff !important;
}

.border-3 {
    border-width: 3px !important;
}

.border-secondary {
    border-color: #ff407b !important;
}

.border-brand {
    border-color: #ffc750 !important;
}

.border-info {
    border-color: #25d5f2 !important;
}

.text-muted {
    color: #7171a6 !important;
}
h1,h5,h6 {
  color: #3d405c;
  margin: 0px 0px 15px 0px;
  font-family: 'Circular Std Medium';
}
h1 {
     font-size: 34px;
}
h5 {
    font-size: 15px;
    line-height: 26px;
  }
</style>

                    <div class="row">
                            <!-- ============================================================== -->
                            <!-- machine instock  -->
                            <!-- ============================================================== -->
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <h5 class="text-muted">MACHINE INSTOCK (YTD)</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $service->services_instock }}</h1>
                                        </div>
                                        <!--
                                        <div class="metric-label d-inline-block float-right text-success font-weight-bold">
                                            <span class="icon-circle-small icon-box-xs text-success bg-success-light"><i class="fa fa-fw fa-arrow-up"></i></span><span class="ml-1">5.86%</span>
                                        </div>
                                      -->
                                    </div>
                                </div>
                            </div>
                            <!-- ============================================================== -->
                            <!-- end instock  -->
                            <!-- ============================================================== -->
                            <!-- ============================================================== -->
                            <!-- machine takeout  -->
                            <!-- ============================================================== -->
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <h5 class="text-muted text-nowrap">MACHINE TAKEOUT(YTD)</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $service->services_takeout }}</h1>
                                        </div>
                                        <!--
                                        <div class="metric-label d-inline-block float-right text-success font-weight-bold">
                                            <span class="icon-circle-small icon-box-xs text-success bg-success-light"><i class="fa fa-fw fa-arrow-up"></i></span><span class="ml-1">10%</span>
                                        </div>
                                      -->
                                    </div>
                                </div>
                            </div>
                            <!-- ============================================================== -->
                            <!-- end new machine takeout  -->
                            <!-- ============================================================== -->
                            <!-- ============================================================== -->
                            <!-- machine not start  -->
                            <!-- ============================================================== -->
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <h5 class="text-muted">MACHINE NOT START</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">{{ $service->not_start }}</h1>
                                        </div>
                                        <!--
                                        <div class="metric-label d-inline-block float-right text-success font-weight-bold">
                                            <span class="icon-circle-small icon-box-xs text-success bg-success-light"><i class="fa fa-fw fa-arrow-up"></i></span><span class="ml-1">5%</span>
                                        </div>
                                      -->
                                    </div>
                                </div>
                            </div>
                            <!-- ============================================================== -->
                            <!-- end machine not start  -->
                            <!-- ============================================================== -->
                            <!-- ============================================================== -->
                            <!-- total cash  -->
                            <!-- ============================================================== -->
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <h5 class="text-muted">TOTAL CUSTOMER PAID($)</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">${{ ($service->services_totalcash) }}</h1>
                                        </div>
                                        <!--
                                        <div class="metric-label d-inline-block float-right text-danger font-weight-bold">
                                            <span class="icon-circle-small icon-box-xs text-danger bg-danger-light bg-danger-light "><i class="fa fa-fw fa-arrow-down"></i></span><span class="ml-1">4%</span>
                                        </div>
                                      -->
                                    </div>
                                </div>
                            </div>
                            <!-- ============================================================== -->
                            <!-- end total cash  -->
                            <!-- ============================================================== -->
                        </div>

        <div class="row">
          <div class="col-md-6">
             <div class="row">
                <div class="col-md-12">
                  <div class="card">
                    <h5 class="card-header">Total Service</h5>
                      <div class="card-body">

                          <div class="chart-widget-list">
                            @php ($color=["#090909", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#FF69B4","black","#0e05ff"] )
                            @php ($total=0)
                            @foreach($services_total as $i=>$service)
                              @php ($total=$total+$service->status_count)
                              <p style="border-bottom:solid 1px #f1f1f1"><span class="fa-xs mr-1 legend-title" style="color: {{ $color[$i] }}"><i class="fa fa-fw fa-square-full"></i></span><span class="legend-text"> {{ ucfirst($service->status) }}</span><span class="float-right">{{ $service->status_count }}</span></p>
                            @endforeach
                              {{-- Fixed color index: this row summarizes across all
                                   statuses, so it never had a real $i of its own — it
                                   was previously relying on $i leaking out of the
                                   @foreach above, which only exists if that loop ran
                                   at least once (broke on an empty $services_total,
                                   i.e. a fresh database with zero tickets). --}}
                              <p><span class="fa-xs mr-1 legend-title" style="color: {{ $color[0] }}"><i class="fa fa-fw fa-square-full"></i></span><span class="legend-text"> {{ 'Total' }}</span><span class="float-right">{{ $total }}</span></p>
                          </div>
                      </div>
                  </div>
                </div>
             </div>

              <div class="row">
                <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">Engineer Busy List</h5>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="bg-light">
                                    <tr class="border-0">
                                        <th class="border-0">#</th>
                                        <th class="border-0">Full Name</th>
                                        <th class="border-0">Limit Busy</th>
                                        <th class="border-0">Currently Busy</th>
                                        <th class="border-0">Status</th>

                                    </tr>
                                </thead>
                                <tbody>

                                  @foreach($busylist as $no=>$list)
                                  @php ($limit=(empty($list->enginner_busy)?"0":$list->enginner_busy->busy_limit))
                                  <tr>
                                    <td>{{ '' . ($no+1) }}</td>
                                    <td>{{ ucfirst($list->first_name) . ' ' . ucfirst($list->last_name) }}</td>
                                    <td>{{ $limit }}</td>
                                    <td>{{ $list->total }}</td>
                                    <td>{{ ($list->total>=$limit)?'Busy':'Free' }}</td>
                                  </tr>
                                  @endforeach
                                </tbody>
                                </table>
                           </div>
                      </div>
                  </div>
                </div> <!-- end of class 12  row -->
                </div> <!-- end of row -->
              </div>
              <div class="col-md-6">

                    <div class="card">
                      <h5 class="card-header">Total Service by status</h5>
                        <div class="col-md-12 card-header">
                      <div class="input-group input-daterange">
                          <label class="form-control-label col-md-1 text-left text-nowrap">From</label>
                      <div class="col-md-4">
                          <input type="text" name="date_fr" id="date_fr" class="form-control datepicker" value="{{ old('date_fr',date('d/m/Y')) }}">
                      </div>
                          <label class="form-control-label col-md-1 text-center text-middle text-nowrap">To</label>
                        <div class="col-md-4">
                          <input type="text" name="date_to" id="date_to" class="form-control datepicker" value="{{ old('date_fr',date('d/m/Y')) }}">
                        </div>
                        <div class="col-md-1 text-middle">
                          <button class="btn btn-primary text-nowrap" id='btn_refresh1'>Refresh</button>
                        </div>
                      </div>
                    </div>


                        <div class="card-body">
                            <canvas id="total-sale" width="220" height="155"></canvas>
                            <div class="chart-widget-list" id='chart_table1'>
                            </div>
                        </div>
                    </div>

              </div>
          </div>
        </div>


        </div><!--col-->
    </div><!--row-->
@endsection

@section('custom_script')
<script>
$(function() {
    "use strict";

    $('#btn_refresh1').click(function(){

        total_sale();

    });

    $('.input-daterange input').each(function() {
      $(this).datepicker({
        format:"dd/mm/yyyy",
        uiLibrary:"bootstrap4",
        iconsLibrary:"materialicons"
      });
    });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var ctx = document.getElementById("total-sale").getContext('2d');


        var total_sale=function(){
            $.ajax({
            url: "{{ route('admin.dashboard') }}",
            method: 'post',
            data: {
               ajax:'ajax',
               func:'total_sale',
               date_fr:$('#date_fr').val(),
               date_to:$('#date_to').val(),
            },
            success: function(result){
            //  console.log(result);
            var color=["#090909", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#FF69B4","#3e95cd","#FFFF00"];
            var total=0;
            $('#chart_table1').text('');
            $.each(result.data.labels, function(i, item) {
              total=total + result.data.chartData[i];
              $('#chart_table1').append('<p style="border-bottom:solid 1px #f1f1f1"><span class="fa-xs mr-1 legend-title" style="color:' + color[i] + '"><i class="fa fa-fw fa-square-full"></i></span><span class="legend-text"> ' + item + '</span><span class="float-right">'+ result.data.chartData[i] +'</span></p>');
            });
            if(result.data.labels.length>0){
              $('#chart_table1').append('<p><span class="fa-xs mr-1 legend-title" style="color:#F23912"><i class="fa fa-fw fa-square-full"></i></span><span class="legend-text"> Total</span><span class="float-right">'+ total +'</span></p>');
            }
              var myChart = new Chart(ctx, {
                  type: 'doughnut',

                  data: {
                      labels: result.data.labels,
                      datasets: [{
                          backgroundColor: [
                            "#090909", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#FF69B4","#3e95cd","#FFFF00"
                          ],
                          data: result.data.chartData
                      }] //end of dataset
                  }, //end of data
                  options: {
                      showLabel: true,
                      legend: {
                          display: false

                      }
                  }, //end of options

            });
            }
        }); //end of ajax
      }

      total_sale();

});
</script>
@endsection
