<?php

namespace App\Http\Controllers\Backend;

use App\Models\Service\Service;
use App\Models\Service\Service_rma;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Service\Enginner_busy;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Service::get()->count() hydrates every row as a full Eloquent
        // model just to count them — invisible with a handful of test
        // rows, but fatal (PHP memory exhaustion) at real production scale
        // (58k+ services). count() alone runs a plain SELECT COUNT(*).
        $service_total=Service::count();
        $service_pending=Service::where('status','pending')->count();
        $service_processing=Service::where('status','processing')->count();
        $service_start=Service::where('status','start')->count();
        $service_not_start=Service::where('status','not start')->count();


        $service_pending_today=Service::where('status','pending')->whereDate('created_at',date('Y-m-d'))->count();
        $service_processing_today=Service::where('status','processing')->whereDate('created_at',date('Y-m-d'))->count();

        DB::enableQueryLog();
        //dd($service);

        $busylist = User::with('enginner_busy')->whereHas('roles', function($q){
                        $q->where('name', 'service-engineer')->where('active',1); })
                    ->select(['users.id','users.first_name','users.last_name',
                    DB::raw("(select count(services.id) from services where users.id=services.assignto and services.status in ('start','progress')) as total"),
                    DB::raw("(select count(services.id) from services where users.id=services.assignto and services.status='start') as s_start"),
                    DB::raw("(select count(services.id) from services where users.id=services.assignto and services.status='progress') as s_processing"),
                    DB::raw("(select count(services.id) from services where users.id=services.assignto and services.status='pending') as s_pending")])
                    ->leftjoin('services','users.id','=','services.assignto')
                    //->whereIn('services.status',['start','progress'])
                    ->groupBy('users.id','users.first_name','users.last_name')
                    //->havingRaw('count(users.id) >=0 ')
                    ->get();


                  //dd(DB::getQueryLog());
                  //die();
        //dd($busylist);
        $func=request('func');

        if(!empty(request('date_fr')))
           $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');
        else
           $date_fr=date('Y-m-d');

        if(!empty(request('date_to')))
           $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');
        else
           $date_to=date('Y-m-d');

        //   $date_fr='2019-11-03';
        //   $date_to='2019-11-03';
      $services_status=Service::select(DB::raw('count(*) as status_count, status'))
                           ->whereDate('date','>=',$date_fr)
                           ->whereDate('date','<=',$date_to)
                           ->groupBy('status')->get();

    //  dd($date_fr);

     $services_totalcash=Service::selectRaw('sum(services.chargeamount + (select coalesce(sum(service_rmas.quantity*service_rmas.sellingprice),0) from  service_rmas where service_id=services.id)) as total')
                          //->leftjoin('service_rmas','service_rmas.service_id','=','services.id')
                          //->groupBy('services.id')
                          //->selectRaw('sum(service_rmas.quantity*service_rmas.sellingprice) as rma')
                          ->where('services.payment',1)->get()->first();


    //dd($services_totalcash);

     $services_totalcash=($services_totalcash->total);

     $services_takeout=Service::select(DB::raw('count(*) as payment_count'))
                        //  ->where('date','=',date('Y-m-d'))
                        //  ->where('payment',1)
                          ->where('delivered','=',1)
                        //  ->orWhere('status','=','cancel')
                          ->count();

    $services_instock=Service::select(DB::raw('count(*) as payment_count'))
                         ->where('delivered','=','0')->orWhereNull('delivered')
                         ->count();

   $service=(object) ['total'=>$service_total,
                       'processing'=>$service_processing,
                       'start'=>$service_start,
                       'not_start'=>$service_not_start,
                       'services_takeout'=>$services_takeout,
                       'services_instock'=>$services_instock,
                       'services_totalcash'=>$services_totalcash
                     ];

     $services_total=Service::select(DB::raw('count(*) as status_count, status'))
                          ->groupBy('status')->get();
    //  dd($services_instock);

      if(request()->ajax()){


          $arr=['data','labels'];

          foreach($services_status as $service){
            $arr['data'][]=$service->status_count;
            $arr['labels'][]=ucfirst($service->status);
          }

          $data=[
               "result"=>true,
               "message"=>'Total Service by Status',
               "data"=>[
                  "labels"=>
                    $arr['labels']
                ,
                  "chartData"=>$arr['data']
                ]
              ];

        return ($data);


      }
        return view('backend.dashboard',compact('service','busylist','services_status','services_total'));
    }
}
