<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Service\Service;
use App\Models\Service\Enginner_busy;
use App\Models\Service\Service_Deleted;
use App\Models\Service\ServiceStatus;
use App\Models\Service\Service_rma;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use App\Models\Customer\Customer;
use App\Models\Vendor\Vendor;
use App\Models\Item\Item;
use App\Models\Problem\Problem;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\Purchases_detail;
use App\Models\Problem\Groupproblem;
use App\Models\Shipping\Shipping;
use App\Models\Transfer\Transfer;
use App\Models\Category\Category;
use App\Models\Brand\Brand;
use App\Models\Rmacode\Rmacode;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Backend\Auth\Service\ManageServiceRequest;
use App\Http\Requests\Backend\Auth\Service\ManageServiceRequestEdit;
use Carbon\Carbon;
use DataTables;
use PDF;
use App\Repositories\Backend\Auth\RoleRepository;
use App\Repositories\Backend\Auth\UserRepository;



class ServiceController extends Controller
{

    use MediaUploadingTrait;

    protected $status=array('not start'=>'Not start','start'=>'Start','pending'=>'Pending','progress'=>'Progress','incomplete'=>'Incomplete','complete'=>'Complete','cancel'=>'Cancel');

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function suggesstion(Request $request){
           if(empty($request->input('keyword')) || strlen($request->input('keyword'))<=3)
            $service=[];
            else
            $service = Service::select('serialno', DB::raw('max(id) as idd'),DB::raw('count(*) as total'))->where('serialno','like', $request->input('keyword') .  '%' )->groupBy('serialno')->get();

           echo '<ul id="serial-list">';

           foreach($service as $s) {
               echo '<li class="text-nowrap"><a href="'. route('admin.service.show',['id'=>$s->idd]) . '" title="Receipt#: "' .  $s->receiptno  . '">' . $s->serialno . ' &nbsp;<span class="badge badge-primary">' . $s->total .'</span><a/></li>';
           }
           echo '</ul>';

           // echo $service;
     }

     public function assignto($id){
       $service = Service::findOrFail($id);

       $input=[];

       $input['assignto']=auth()->user()->id;
       $service->update($input);

       return redirect()->route('admin.service.index')
                       ->withFlashSuccess(__('alerts.backend.service.assigned'));

     }

    public function receipt($id){
      $service = Service::find($id);

      return view('backend.services.receipt',compact('service'));

    }

    public function invoice($id){
      $service = Service::find($id);

      return view('backend.services.invoice',compact('service'));

    }

    public function report(Request $request){

      $services=null;
      $enginner=User::whereHas('roles', function($q){
        $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      $status= $this->status;


      if($request->ajax()){

          $date_fr='';$date_to='';
           $reportby=request('reportby');


           if(!empty(request('date_fr')))
              $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');
           if(!empty(request('date_to')))
              $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

           // Eager-load every relation the closures below touch per row
           // (customer, problem, problem.group_problem, enginner, brand,
           // rmacodes, service_rma) plus media (Service's 'logo' $appends
           // accessor fires per row regardless of whether any closure
           // references it). This method previously had NONE of these —
           // only the ->get()-vs-builder issue was fixed earlier, the N+1
           // itself was still live underneath.
           $services=Service::with(['customer','problem','problem.group_problem','enginner','brand','rmacodes','service_rma','media'])->orderBy('date','asc');


          // $date_fr='2020-05-07';$date_to='2020-05-07';
          // $reportby=0;

           if($reportby==0){
               $services->when(!empty($date_fr), function($q) use ($date_fr){
                 return $q->whereDate('date','>=',$date_fr);
               });

               $services->when(!empty($date_to), function($q) use ($date_to){
                 return $q->whereDate('date','<=',$date_to);
               });
          }elseif($reportby==1){ // by status date
              $services->when(!empty($date_fr), function($q) use ($date_fr){
                return $q->whereDate('updated_at','>=',$date_fr);
              });

              $services->when(!empty($date_to), function($q) use ($date_to){
                return $q->whereDate('updated_at','<=',$date_to);
              });

          }elseif($reportby==2){ // by payment date
            $services->when(!empty($date_fr), function($q) use ($date_fr){
              return $q->whereDate('paydate','>=',$date_fr);
            });

            $services->when(!empty($date_to), function($q) use ($date_to){
              return $q->whereDate('paydate','<=',$date_to);
            });
          }

           $services->when(in_array(request('payment'),array('0','1')), function($q) {
             return $q->where('payment','=',request('payment'));
           });

           $services->when(!empty(request('enginner')), function($q) {
             return $q->where('assignto','=',request('enginner'));
           });

          if($reportby==1){
                $services->whereIn('status',['complete','incomplete']);

          }else{
               $services->when(!empty(request('status')), function($q) {
                 return $q->where('status','=',request('status'));
               });
          }

         if(empty($date_fr) && empty($date_to))
              $services->whereNull('id');


        //  dd($services->get());


             //->where('date','<=',request('date_to',$date_to));
        //      if(!empty(request('payment')))
          //        $services=Service::where('date','>=',$date_fr)->where('date','<=',$date_to)->where('payment',request('payment'));

            //}else
              //$services=[];



           // Passing a materialized Collection (->get()) forces yajra into
           // its Collection engine — the ENTIRE filtered result set gets
           // hydrated into PHP memory up front, with pagination/searching
           // done in-memory afterward, instead of SQL-level LIMIT/OFFSET.
           // Harmless for a handful of test rows; a broad date range against
           // real production volume (58k+ services) would repeat the same
           // memory exhaustion fixed in index() above. Pass the builder
           // itself so Datatables::of() uses its query engine instead.
           $datatables=Datatables::of($services);

           $datatables->addColumn('problem',function(Service $s){
               if(count($s->problem)>0)
                 return $s->problem->pluck('problem')->implode(' ');
               else
                 return '';
           });


           $datatables->addColumn('brand',function(Service $s){
               if(count($s->brand)>0)
                 return $s->brand->pluck('name')->implode(' ');
               else
                 return '';
           });

           $datatables->editColumn('status',function(Service $s){
                 return ucfirst($s->status);
            });

            $datatables->editColumn('rmacode',function(Service $s){
                  return (empty($s->rmacodes)?'':$s->rmacodes->code);
             });

          $datatables->editColumn('warrenty',function(Service $s){
                return ucfirst((($s->warrenty==1)?'in':'out'));
           });

         $datatables->editColumn('payment',function(Service $s){
               return ucfirst((($s->payment==1)?'Yes':'No'));
          });

          $datatables->addColumn('enginnername',function(Service $s){
              if(!empty($s->enginner))
                return ucfirst($s->enginner->first_name) . ' ' . ucfirst($s->enginner->last_name);
              else
                return 'N/A';
          });

          $datatables->addColumn('category',function($services){
                $problem = $services->problem->first();

                if(empty($problem) || $problem->group_problem->count()<=0)
                   return 'N/A';

                return $problem->group_problem->first()->name;
           });

          $datatables->editColumn('chargeamount',function($services){
            $subamount=0;
            foreach ($services->service_rma as $service_rma) {
                $subamount=$subamount + ($service_rma->quantity * $service_rma->sellingprice);
            }
            return ($subamount+$services->chargeamount);

          });

          $datatables->editColumn('date',function(Service $s){
             if(request('reportby')==0){
                 return [
                      'display' => $s->date,
                      'timestamp' => $s->date1
                   ];
            }elseif(request('reportby')==1){

              $updated_at = $s->updated_at->format('d/m/Y');
              return [
                   'display' =>  $s->updated_at->format('d/m/Y'),
                   'timestamp' => strtotime($updated_at)
                ];

            }elseif(request('reportby')==2){
              return [
                   'display' => $s->paydate,
                   'timestamp' => $s->date3
                ];

            }
          });

           $datatables->addColumn('customername',function(Service $s){
                 return empty($s->customer) ? '' : ucfirst($s->customer->first_name) . ' ' . ucfirst($s->customer->last_name);
            });

           return $datatables->make(true);
      //}



    }
      return view('backend.services.report',compact('enginner','services','status'));
  }



    public function index(Request $input)
    {
      if(request()->ajax()){

        // Eager-load every relation the addColumn/editColumn closures below
        // touch per row (customer, problem, enginner, transfer) plus media
        // ('logo' is an always-computed $appends accessor on Service, so
        // Datatables serializing each row triggers a media query regardless
        // of whether any closure references it directly). Without this, each
        // row fires ~13 separate relation queries — invisible in local dev
        // against MySQL on the same LAN as the app, but each query pays a
        // real network round-trip against remote Postgres: N+1 that was
        // "slow" became a 60s+ page timeout at real data volume.
        $services=Service::with([
            'customer'=> function($q){
                $q->select([
                    'id','first_name','last_name',
                    DB::raw("CONCAT(customers.first_name,' ',customers.last_name)  AS fullname")
                ]);
            },
            'problem', 'enginner', 'transfer', 'media',
        ]);

        // Each branch used to end in ->get(), executing (and discarding) a
        // full, unfiltered-by-pagination query against all of `services`
        // before Datatables::of() below ever got to apply its own
        // server-side LIMIT. Invisible with a handful of test rows;
        // catastrophic (multiple full-table loads with the customer eager
        // load, per page view) at real production scale — this is the main
        // service ticket list, the single most-visited page in the app.
        // whereIn()/where()/orderBy() all mutate $services in place and
        // don't need ->get() to take effect.
        if(empty(request('status')))
            $services->whereIn('status', ['not start','start','pending']);
        elseif(request('status')=='ALL')
            ; // no additional status filter
        else
            $services->where('status',strtolower(request('status')));

        $services->orderBy(DB::raw("case when status= 'not start' then 1 when status= 'start' then 2 when status= 'pending' then 3 when status= 'progress' then 4 when status= 'processing' then 4  when status= 'complete' then 5 when status= 'incomplete' then 6 when status= 'cancel' then 7 when status= 'delivered' then 8 end"));



        $datatables=Datatables::of($services);


    //    dump($datatables);
    //    die();

        $datatables->addColumn('customername',function(Service $s){
            if(!empty($s->customer))
              return ucfirst($s->customer->first_name) . ' ' . ucfirst($s->customer->last_name);
            else
              return '';
        });

        $datatables->addColumn('problem',function(Service $s){
            if(count($s->problem)>0)
              return $s->problem->pluck('problem')->implode(' ');
            else
              return '';
        });
        $datatables->addColumn('delivered',function($services){

            return (($services->delivered==1)?'Yes':'No');


        });
        $datatables->addColumn('enginnername',function(Service $s){
            if(!empty($s->enginner))
              return ucfirst($s->enginner->first_name) . ' ' . ucfirst($s->enginner->last_name);
            else
              return 'N/A';
        });

        $datatables->addColumn('transfer',function(Service $s){

            //return $s->transfer->count();

            if($s->transfer->count()==0)
              return '-';
            else if(!empty($s->transfer->first()->returndate))
              return 'In';
            else
              return 'Out';

        });

        $datatables->addColumn('action',function(Service $s){
            //htmlentities
            return  ($s->action_buttons);
        });

        $datatables->editColumn('status',function(Service $s){
            return ucfirst($s->status);
        });

        // receiver(userid) does User::where('id',...)->first() fresh per
        // row — a small table (55 real rows), so prefetch it once instead.
        $usersById = User::all()->keyBy('id');
        $datatables->addColumn('receiver',function(Service $s) use ($usersById){
            $u = $usersById->get($s->userid);
            return $u ? ucfirst($u->first_name) . ' ' . ucfirst($u->last_name) : '';
        });

        $datatables->addColumn('duration',function(Service $s){
            //htmlentities
            //if(in_array($s->status,['complete','incomplete']))
              //return Carbon::parse($s->dtt)->diffForHumans(Carbon::parse($s->updated_at));
            //else
              return  Carbon::parse($s->dtt)->diffForHumans();
        });

        $datatables->editColumn('customer',function(Service $s){
            return ucfirst($s->customer['fullname']);
        });

        $datatables->editColumn('chargetype',function(Service $s){
            return ucfirst($s->chargetype);
        });

        $datatables->editColumn('chargeamount',function(Service $s){
            return number_format($s->chargeamount,0);
        });



        $datatables->editColumn('warrenty',function(Service $s){
            return ucfirst((($s->warrenty==0)?"out":"in"));
        });

        $datatables->editColumn('payment',function(Service $s){
            return ucfirst((($s->payment==0)?"No":"Yes"));
        });



        return $datatables->make(true);

      }


      return view('backend.services.index');
    }

    public function pdfreport()
    {
       // This  $data array will be passed to our PDF blade
       $data = [
          'date_fr' => '01/09/2019',
          'date_to' => '31/10/2019',
          'payment' => '',
          'enginner' => ''
            ];

        $pdf = PDF::loadView('backend/services/reportpdf', $data);

        return $pdf->stream();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ManageServiceRequest $request)
    {

        $problems=Problem::all()->pluck('problem', 'id');
        $customers=Customer::all()->pluck('full_name', 'id');
        $vendors=Vendor::all()->pluck('companyname', 'id');
        $brands=Brand::orderBy('name','asc')->pluck('name', 'id');

      //  $user_enginners= User::whereHas('roles', function($q){
    //$q->where('name', 'service-engineer'); })->with('services')->get();//->pluck('name', 'id');
    /*
    $user_enginners= User::select(['users.*',DB::raw('count(users.id) as total')])
                ->leftjoin('services','users.id','=','services.assignto')
                ->WhereIn('status',['start','progress'])
                ->groupBy('users.id')
                ->havingRaw('count(users.id) < (select busy_limit from enginner_busys where enginner_busys.user_id=users.id)')
                ->get()->pluck('name', 'id');
*/

        // Comparing two correlated-subquery aliases (services_count < busy_limit)
        // via havingRaw() is a MySQL-only extension — Postgres's HAVING clause
        // can't resolve SELECT-list aliases that aren't real GROUP BY aggregates.
        // Filtering in PHP after fetching sidesteps the portability issue entirely.
        $user_enginners= User::whereHas('roles', function($q){
              $q->where('name', 'service-engineer')->where('active',1); })
              ->withCount(["enginner_busy as busy_limit"=> function($q){
                  $q->select('busy_limit');
              },"services" => function($q){
                  $q->WhereIn('status',['start','progress']);
              }])
              ->get()
              ->filter(function($user){
                  return $user->services_count < $user->busy_limit;
              })
              ->pluck('name', 'id');

  //      $user_enginners= User::with('services')->whereHas('services')->get();
        //dd($user_enginners);
    //  $user_enginners=$enginners;
        $status = $this->status;
        unset($status['complete'],$status['incomplete']);

        $service_lists=Service::select('id','receiptno','serialno','customerid')->orderBy('receiptno','asc')->get();

        return view('backend.services.addnew',compact('user_enginners','customers','problems','status','vendors','service_lists','brands'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'date' => ['required', 'date_format:"' . config('app.date_format') . '"'],
            'buyingdate' => 'nullable',
            'serialno' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'problem' => ['required','numeric'],
            'chargeamount' => ['nullable','numeric'],
            // Real ticket descriptions run up to 616 chars (many technicians
            // write multi-line Khmer notes); 255 was rejecting ~15% of real
            // tickets on save. 1000 covers the real max with headroom.
            'description' => ['required','string','max:1000'],
            'phone'=>'nullable'

        ]);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $problem = Problem::where('id',request('problem'))->first();
      $brand = Brand::where('id',request('brand'))->first();

      $this->validator($request->all())->validate();

      // P0 fix: receiptno (YYYYMMDD-N) used to be generated by reading the
      // whole services table, computing the next number in PHP, then
      // checking for a duplicate before saving — three non-atomic steps with
      // no DB-level guard, so concurrent ticket creation across branches
      // could produce duplicate receipt numbers. Now backed by a per-day
      // counter row locked with SELECT ... FOR UPDATE inside a transaction,
      // with services.receiptno itself carrying a unique constraint as the
      // hard backstop (see database/migrations for both).
      $today = date('Y-m-d');
      $last_id = DB::transaction(function () use ($today) {
          DB::statement(
              'INSERT INTO receiptno_counters (date, last_seq, created_at, updated_at) VALUES (?, 0, ?, ?) ON CONFLICT (date) DO NOTHING',
              [$today, now(), now()]
          );

          $counter = DB::table('receiptno_counters')->where('date', $today)->lockForUpdate()->first();
          $next_seq = $counter->last_seq + 1;
          DB::table('receiptno_counters')->where('date', $today)->update(['last_seq' => $next_seq, 'updated_at' => now()]);

          return date('Ymd') . '-' . $next_seq;
      });

      $service = new Service();
      $service->userid=auth()->user()->id;
      $service->receiptno=$last_id;
      $service->date = request('date');
      if(!empty(request('buyingdate')))
        $service->buyingdate = request('buyingdate');
      $service->serialno = request('serialno');
      $service->model = request('model');
      $service->customerid = request('customerid');
      $service->description = request('description');
      // request('field.0') not request('field')[0] — same illegal-offset
      // warning risk as update() if the field is ever absent/non-array.
      $service->chargetype = ((request('chargetype.0')==0)?"free":"cash");
      $service->chargeamount = empty(request('chargeamount'))?0:request('chargeamount');
      $service->payment = 0;
      $service->assignto = request('assignto');
      $service->warrenty=empty(request('warrenty.0'))?0:request('warrenty.0');
      //$service->destination='';
      $service->solution='';
      //$service->credit_note_type=null;
      //$service->credit_note_reason=null;
      //$service->credit_note_clear=null;
      $service->ordered=0;
      $service->status = request('status');
      $service->phone = empty(request('phone'))?'':request('phone');
      $service->ordered_comment = '';
//      $service->status
      $service->save();
      $service->problem()->attach($problem);
      $service->brand()->attach($brand);
      // -- save status to logs status table

      $service_status[] = new ServiceStatus([
                'userid' => auth()->user()->id,
                'status' => request('status'),

              ]);
      $service->service_status()->delete();
      $service->service_status()->saveMany($service_status);

      if ($request->input('logo', false)) {
          $service->addMedia(storage_path('tmp/uploads/' . $request->input('logo')))->toMediaCollection('logo');
      }

      return redirect()->route('admin.service.show',['id'=>$service->id])->withFlashSuccess(__('alerts.backend.service.created'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

      $problems=Problem::all()->pluck('problem', 'id');
      $customers=Customer::all()->pluck('full_name', 'id');
      $vendors=Vendor::all()->pluck('companyname', 'id');
      $user_enginners= User::whereHas('roles', function($q){

  $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      // Purchase::where('service_id',...) was dead code here — the
      // 'purchases' table (stock bought from vendors) has no service_id
      // column at all and never has; $rma was computed but never referenced
      // in the view. RMA/parts tracking actually lives in service_rmas.
      $service = Service::find($id);
      $status = $this->status;
      $credit_note_type=array(1=>'Credit Note',2=>'Cancel Credit Note',3=>'Clear Credit Note');
      $service_history= ServiceStatus::where('service_id',$id)->get();


      return view('backend.services.show',compact('service','problems','customers','user_enginners','status','vendors','service_history','credit_note_type'));

    }


    public function report_agin(Request $request){
      $services=null;

      $enginner=User::whereHas('roles', function($q){
        $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      $status= $this->status;

      $date_option=[1=>"X <= 3 days",
                    2=>"3 > X <= 7 Days",
                    3=>"7 > X <= 14 Days",
                    4=>"14 > X <= 30 Days",
                    5=>"X > 30 days"];
      $category = [0=>"ALL",1=>"Software",2=>"Hardware"];

      $date_option_req=request('date_option');
      $category_req=request('category');
      $status_req=strtolower(request('status'));

      if($request->ajax()){

          // Eager-load customer/problem/media so the addColumn closures below
          // don't re-query per row (media matters even though no closure
          // touches it directly — Service's 'logo' $appends accessor fires
          // getMedia() on every row serialization regardless).
          $services=Service::with(['customer','problem','media'])
                           ->join('services_status', 'services_status.service_id', '=', 'services.id')
                           ->join('problem_service','problem_service.service_id','=','services.id')
                           ->leftjoin('groupproblem_problem','groupproblem_problem.problem_id','=','problem_service.problem_id')
                           ->select(['services.*','services_status.created_at as status_created_at','services_status.status as status1','groupproblem_problem.groupproblem_id']);

               $services->when(!empty($date_option_req) || !empty($category_req) || !empty($status_req), function($q) use ($date_option_req,$category_req,$status_req){

                 if($date_option_req==1)
                   $q->whereDate('services_status.created_at', '>=',Carbon::now()->subDay(3));
                 elseif($date_option_req==2)
                   $q->whereDate('services_status.created_at', '>=',Carbon::now()->subDay(7))->where('services_status.created_at', '<',Carbon::now()->subDay(3));
                 elseif($date_option_req==3)
                    $q->where('services_status.created_at', '>=',Carbon::now()->subDay(14))->where('services_status.created_at', '<',Carbon::now()->subDay(7));
                 elseif($date_option_req==4)
                    $q->where('services_status.created_at', '>=',Carbon::now()->subDay(30))->where('services_status.created_at', '<',Carbon::now()->subDay(14));
                 elseif($date_option_req==5)
                    $q->whereDate('services_status.created_at', '<',Carbon::now()->subDay(30));

                 if(!empty($category_req))
                     $q->where('groupproblem_problem.groupproblem_id',$category_req);

                 if(!empty($status_req))
                     $q->where('services_status.status','=',$status_req);
               });

           // No filter selected previously ran this 3-way join across the
           // full services (58k+) / services_status (205k+) / problem_service
           // (58k+) tables unbounded, on every page load. Matches the
           // established "no filter = no results" convention used across
           // every other report in this controller.
           if(empty($date_option_req) && empty($category_req) && empty($status_req))
              $services->whereNull('services.id');

           $datatables=Datatables::of($services);

           // Tiny table (2 real rows) — fetched once, not per row.
           $groupproblemNames = Groupproblem::pluck('name','id');

           $datatables->addColumn('category',function(Service $s) use ($groupproblemNames){
             return $groupproblemNames->get($s->groupproblem_id, '');
           });

           $datatables->addColumn('problem',function(Service $s) {
               if(count($s->problem)>0)
                 return $s->problem->pluck('problem')->implode(' ');
               else
                 return '';
           });

           $datatables->editColumn('status',function($services){
                 return ucfirst($services->status1);
            });

          $datatables->editColumn('warrenty',function(Service $s){
                return ucfirst((($s->warrenty==1)?'in':'out'));
           });

         $datatables->editColumn('payment',function(Service $s){
               return ucfirst((($s->payment==1)?'Yes':'No'));
          });

          $datatables->editColumn('date',function(Service $s){
/*
             if(request('reportby')==0){
                 return [
                      'display' => $s->date,
                      'timestamp' => $s->date1
                   ];
            }elseif(request('reportby')==1){

              $updated_at = $s->updated_at->format('d/m/Y');
              return [
                   'display' =>  $s->updated_at->format('d/m/Y'),
                   'timestamp' => strtotime($updated_at)
                ];

            }elseif(request('reportby')==2){
*/

              return [
                   'display' => $s->date,
                   'timestamp' => $s->date1
                ];

          });

           // customer_name($id) does Customer::where('id',...)->count() then
           // ->first() twice — 3 fresh queries per row even though customer
           // is already eager-loaded above. This was the actual bottleneck:
           // 438 rows x 3 queries x ~90ms real network latency = the 60s
           // timeout, despite the base query itself completing in ~1s.
           $datatables->addColumn('customername',function(Service $s){
                 return empty($s->customer) ? '' : ucfirst($s->customer->first_name) . ' ' . ucfirst($s->customer->last_name);
            });

           return $datatables->make(true);

    }

        return view('backend.services.report_agin', compact('enginner','status','date_option','category'));
    }

    public function report_shipping(Request $request){
      $services=null;

      if($request->ajax()){

         $date_fr='';$date_to='';


         if(!empty(request('date_fr')))
            $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

         if(!empty(request('date_to')))
            $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

        $shipping=Shipping::with('service')->whereHas('Service');
        //dd($shipping);
         $shipping->when(!empty(request('date_fr')), function($q) use ($date_fr){
           return $q->where('shipdate','>=',$date_fr);
         });

         $shipping->when(!empty(request('date_to')), function($q) use ($date_to){
           return $q->where('shipdate','<=',$date_to);
         });

         if(empty($date_fr) && empty($date_to))
            $shipping->whereNull('id');

           $datatables=Datatables::of($shipping);

        //dd($purchases);


          $datatables->addColumn('service_no',function($shipping) {
                $service = $shipping->service->pluck('receiptno')->toArray();

                return join('<br> ',$service);
          });

          $datatables->rawColumns(['service_no']);

           return $datatables->make(true);

    }



        return view('backend.services.report_shipping');
    }

    public function report_transfer(Request $request){
      $services=null;

      $credit_note_type=array('Credit Note'=>'Credit Note','Cancel Credit Note'=>'Cancel Credit Note','Clear Credit Note'=>'Clear Credit Note');
      $vendors=Vendor::all()->pluck('companyname', 'id');
      $credit_note_type_req=request('credit_note_type');
      $vendor_id=request('vendor_id');
      if($request->ajax()){

         $date_fr='';$date_to='';$date_fr1='';$date_to1='';


         if(!empty(request('date_fr')))
            $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

         if(!empty(request('date_to')))
            $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

          if(!empty(request('date_fr1')))
             $date_fr1=Carbon::createFromFormat(config('app.date_format'), request('date_fr1'))->format('Y-m-d');

          if(!empty(request('date_to1')))
             $date_to1=Carbon::createFromFormat(config('app.date_format'), request('date_to1'))->format('Y-m-d');


        $service=Transfer::with(['service','service.problem'])->has('Service')->orderBy('transferdate','asc');

         $service->when(!empty(request('date_fr')), function($q) use ($date_fr){
           return $q->where('transferdate','>=',$date_fr);
         });

         $service->when(!empty(request('date_to')), function($q) use ($date_to){
           return $q->where('transferdate','<=',$date_to);
         });

         $service->when(!empty(request('date_fr1')), function($q) use ($date_fr1){
           return $q->where('cleardate','>=',$date_fr1);
         });

         $service->when(!empty(request('date_to1')), function($q) use ($date_to1){
           return $q->where('cleardate','<=',$date_to1);
         });

         $service->when(!empty($credit_note_type_req), function($q) use ($credit_note_type_req){
           return $q->where('type',$credit_note_type_req);
         });

         $service->when(!empty(request('vendor_id')), function($q) use($vendor_id){
           return $q->where('vendor_id',$vendor_id);
         });


        if(empty($date_fr) && empty($date_to))
            $service->whereNull('id');

           $datatables=Datatables::of($service);

           $datatables->addColumn('service_no',function($service) {
                 return '- ' . join('<br>- ',$service->service->pluck('receiptno')->toArray());
           });

           $datatables->addColumn('serialno',function($service) {
                 return '- ' . join('<br>- ',$service->service->pluck('serialno')->toArray());
           });

           $datatables->addColumn('model',function($service) {
                 return '- ' . join('<br>- ',$service->service->pluck('model')->toArray());
           });

           $datatables->addColumn('problem',function($service) {
                 $first = $service->service->first();
                 if(empty($first) || empty($first->problem->first())) return '';
                 return $first->problem->first()->problem;
           });

           $datatables->rawColumns(['service_no']);
           return $datatables->make(true);

    }
    return  view('backend.services.report_transfer',compact('credit_note_type','vendors'));
  }

    public function report_enginner(Request $request){


    //  $enginner=User::whereHas('roles', function($q){
      //  $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

    //  $vendor=Vendor::all()->pluck('companyname', 'id');

      $status = [1=>'Available Enginner ',2=>'Busy Enginner'];
      $limit = empty(request('limit'))?0:request('limit');
      if($request->ajax()){


         $status=request('status');

        $free=array('not start','pending','complete','incomplete','cancel','delivered');
        $busy=array('start','processing','progress');
        /*
        $data = User::whereHas('roles',function($q) {
            $q->where('name','service-engineer');
        })->whereHas('services',function($q) use($free){
              $q->where('assignto',9);
          //  $q->where('status','not start');
        })->get();
        */

        if($status==2){
        /* --- For Busy --- */

        $data= User::with('roles')->select(['users.*',DB::raw('count(users.id) as total')])
                    ->join('services','users.id','=','services.assignto')
                    ->WhereIn('status',$busy)
                    ->groupBy('users.id')
                    ->havingRaw('count(users.id) >= ?', [$limit])
                    ->get();

        }elseif($status==1) {
        /* --- For Free --- */
        $data=User::with('roles')->whereNotIn('users.id', function($q) use($busy){
          $q->select('assignto')
            ->from('services')
            ->WhereIn('status',$busy);
        })->whereHas('roles', function($q){
            $q->where('name','service-engineer');
        })->get(['users.*']);
      }
              //User::join('services','users.id','=','services.assignto')

/*
        $data=Service::whereHas('enginner', function($q){
            $q->whereHas('roles', function($q){
                $q->where('name','service-engineer');
            });
        })->get();
*/


         //dump($purchases->toSql());

         if(empty($status))
            return Datatables::of([])->make(true);

           $datatables=Datatables::of($data);

          // $datatables->addColumn('total_busy',function($data){
            //  return $data->count();
           //});
           $datatables->addColumn('roles_label',function($data){
                  return $data->roles->pluck('name')->toArray();

            });

           return $datatables->make(true);

    }

        return view('backend.services.report_enginner', compact('status'));
    }

    /**
     * Gross margin report: labor charge + parts markup, minus parts cost,
     * for tickets that have actually been paid (paydate-filtered, matching
     * report_selling's convention) — realized revenue, not just billed.
     * Labor cost isn't tracked anywhere in this system (only what's charged
     * to the customer), so "profit" here is gross margin, not true net
     * profit after wages.
     */
    public function report_revenue(Request $request){
      $enginner=User::whereHas('roles', function($q){
        $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      if($request->ajax()){

         $date_fr='';$date_to='';

         if(!empty(request('date_fr')))
            $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

         if(!empty(request('date_to')))
            $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

         // Eager-load every relation the closures below touch per row
         // (customer, enginner, service_rma) — without this, each row fires
         // its own relation queries (plus customer_name() below re-queries
         // Customer from scratch, ignoring the already-loaded relation
         // entirely). Invisible with a handful of test rows; at real scale
         // this is the same N+1-over-network collapse fixed earlier in
         // index()/report() for the main service list.
         // 'media' must be eager-loaded even though no closure below touches
         // it directly — 'logo' is an always-computed $appends accessor on
         // Service (getMedia('logo')), so DataTables serializing each row
         // triggers a media query per row unless it's already loaded. Same
         // root cause already fixed on the main service list (index()).
         $applyFilters = function($q) use ($date_fr, $date_to) {
             $q->where('payment',1);
             $q->when(!empty($date_fr), function($q) use ($date_fr){
               return $q->where('paydate','>=',$date_fr);
             });
             $q->when(!empty($date_to), function($q) use ($date_to){
               return $q->where('paydate','<=',$date_to);
             });
             $q->when(!empty(request('enginner')), function($q){
               return $q->where('assignto',request('enginner'));
             });
             // Matches the sibling reports' convention (PurchaseController's
             // report_purchase(), ServiceController's own report() above): no
             // date filter selected means no query against real production
             // volume by default, not "every paid ticket ever."
             if(empty($date_fr) && empty($date_to))
                $q->whereNull('id');
             return $q;
         };

         $services = $applyFilters(Service::with(['customer','enginner','service_rma','media']));

         // Pass the query builder itself, not a materialized ->get() —
         // yajra's Collection engine would otherwise hydrate the entire
         // filtered result set into memory up front instead of using
         // SQL-level LIMIT/OFFSET, the same fix already applied to report().
         $datatables=Datatables::of($services);

         $datatables->addColumn('customername',function(Service $s){
               if(empty($s->customer)) return 'N/A';
               return ucfirst($s->customer->first_name) . ' ' . ucfirst($s->customer->last_name);
          });

         $datatables->addColumn('enginnername',function(Service $s){
             if(!empty($s->enginner))
               return ucfirst($s->enginner->first_name) . ' ' . ucfirst($s->enginner->last_name);
             else
               return 'N/A';
         });

         $datatables->addColumn('parts_revenue',function(Service $s){
             $sum=0;
             foreach ($s->service_rma as $rma) {
                 $sum += $rma->quantity * $rma->sellingprice;
             }
             return $sum;
         });

         $datatables->addColumn('parts_cost',function(Service $s){
             $sum=0;
             foreach ($s->service_rma as $rma) {
                 $sum += $rma->quantity * $rma->purchaseprice;
             }
             return $sum;
         });

         $datatables->addColumn('labor_cost',function(Service $s){
             return $s->labor_cost ?? 0;
         });

         $datatables->addColumn('revenue',function(Service $s){
             $parts_revenue=0;
             foreach ($s->service_rma as $rma) {
                 $parts_revenue += $rma->quantity * $rma->sellingprice;
             }
             return $s->chargeamount + $parts_revenue;
         });

         $datatables->addColumn('profit',function(Service $s){
             $parts_revenue=0;$parts_cost=0;
             foreach ($s->service_rma as $rma) {
                 $parts_revenue += $rma->quantity * $rma->sellingprice;
                 $parts_cost += $rma->quantity * $rma->purchaseprice;
             }
             return ($s->chargeamount + $parts_revenue) - $parts_cost - ($s->labor_cost ?? 0);
         });

         $datatables->addColumn('margin',function(Service $s){
             $parts_revenue=0;$parts_cost=0;
             foreach ($s->service_rma as $rma) {
                 $parts_revenue += $rma->quantity * $rma->sellingprice;
                 $parts_cost += $rma->quantity * $rma->purchaseprice;
             }
             $revenue = $s->chargeamount + $parts_revenue;
             if($revenue <= 0)
                 return 0;
             $profit = $revenue - $parts_cost - ($s->labor_cost ?? 0);
             return round(($profit / $revenue) * 100, 1);
         });

         // With serverSide:true the footer must NOT sum api.column().data()
         // client-side — that only reflects the current page (10 rows), not
         // the full filtered set, which would silently misreport revenue on
         // any range wider than one page. Compute the true totals here, over
         // the same filtered (unpaginated) query, using the same correlated-
         // subquery-per-row shape already proven fast against real volume
         // (service_rmas.service_id is indexed) rather than hydrating every
         // matching Service row just to sum four numbers.
         $totalsRow = $applyFilters(Service::query())->selectRaw('
             COALESCE(SUM(services.chargeamount),0) as labor_fee,
             COALESCE(SUM(services.labor_cost),0) as labor_cost,
             COALESCE(SUM((SELECT COALESCE(SUM(sr.quantity*sr.sellingprice),0) FROM service_rmas sr WHERE sr.service_id = services.id)),0) as parts_revenue,
             COALESCE(SUM((SELECT COALESCE(SUM(sr.quantity*sr.purchaseprice),0) FROM service_rmas sr WHERE sr.service_id = services.id)),0) as parts_cost
         ')->first();

         $revenueTotal = $totalsRow->labor_fee + $totalsRow->parts_revenue;
         $profitTotal = $revenueTotal - $totalsRow->parts_cost - $totalsRow->labor_cost;
         $marginTotal = $revenueTotal > 0 ? round(($profitTotal / $revenueTotal) * 100, 1) : 0;

         $datatables->with('totals', [
             'labor_fee' => (float) $totalsRow->labor_fee,
             'parts_revenue' => (float) $totalsRow->parts_revenue,
             'parts_cost' => (float) $totalsRow->parts_cost,
             'labor_cost' => (float) $totalsRow->labor_cost,
             'revenue' => (float) $revenueTotal,
             'profit' => (float) $profitTotal,
             'margin' => $marginTotal,
         ]);

         return $datatables->make(true);

      }

      return view('backend.services.report_revenue', compact('enginner'));
    }


    public function report_selling(Request $request){
      $services=null;

      $enginner=User::whereHas('roles', function($q){
        $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      if($request->ajax()){

         $date_fr='';$date_to='';


         if(!empty(request('date_fr')))
            $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

         if(!empty(request('date_to')))
            $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

        //$purchases=Purchase::join('purchases_details', 'purchases.id', '=', 'purchases_details.purchase_id')
          //->select(['purchases.id', 'purchases.date','purchases.service_id', 'purchases.rma', 'purchases.vendor_id', 'purchases.payment', 'purchases.paydate','purchases_details.item_id','purchases_details.quantity','purchases_details.purchaseprice']);
        // media: 'logo' is an always-computed $appends accessor on Service
        // (getMedia('logo')) — fires per row regardless of the narrow
        // column select above, unless eager-loaded here.
        $service=Service::with('media')
                   ->join('service_rmas','services.id','=','service_rmas.service_id')
                   ->select(['services.id','services.receiptno','services.status','services.rmano','services.chargeamount','services.paydate','services.rmadate' ,'service_rmas.item_name','service_rmas.quantity','service_rmas.purchaseprice','service_rmas.sellingprice','service_rmas.payment','service_rmas.reference','service_rmas.category_id']);

         //$service->where('payment','=',1);

         $service->when(!empty(request('date_fr')), function($q) use ($date_fr){
           return $q->where('services.paydate','>=',$date_fr);
         });

         $service->when(!empty(request('date_to')), function($q) use ($date_to){
           return $q->where('services.paydate','<=',$date_to);
         });

         // Was commented out — with no date filter this ran unbounded across
         // services join service_rmas (real volume: 58,860 x 13,174). Matches
         // the guard convention used by every other report in this controller.
         if(empty($date_fr) && empty($date_to))
             $service->whereNull('services.id');

            $datatables=Datatables::of($service);

           $datatables->addColumn('subtotal',function($service) {
                 //$rmar=Service::find($s->customerid);
                 return ($service->quantity * $service->sellingprice);
           });

         $datatables->editColumn('payment',function($service){
               return ucfirst((($service->payment==1)?'Yes':'No'));
          });

          // Category is a tiny table (3 real rows) — prefetch once.
          $categoriesById = Category::all()->keyBy('id');
          $datatables->addColumn('vendor',function($service) use ($categoriesById){
                $category=$categoriesById->get($service->category_id);
                return $category ? ucfirst($category->name) : '';
           });

           $datatables->editColumn('status',function($service){
                 return ucfirst($service->status);
            });

           $result=$datatables->make(true);

           return $result;
        //   dd($result);

    }

        return view('backend.services.report_selling');
    }


    public function report_rma(Request $request){
      $services=null;

      $enginner=User::whereHas('roles', function($q){
        $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      $categorys=Category::all()->pluck('name', 'id');

      $payment = [1=>"Yes",0=>"No"];

      if($request->ajax()){

         $date_fr='';$date_to='';
         $payment_req=request('payment');
          $category_id=request('category_id');

         if(!empty(request('date_fr')))
            $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

         if(!empty(request('date_to')))
            $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

        //$purchases=Purchase::join('purchases_details', 'purchases.id', '=', 'purchases_details.purchase_id')
          //->select(['purchases.id', 'purchases.date','purchases.service_id', 'purchases.rma', 'purchases.vendor_id', 'purchases.payment', 'purchases.paydate','purchases_details.item_id','purchases_details.quantity','purchases_details.purchaseprice']);
          $service=Service::with('media')
                   ->join('service_rmas','services.id','=','service_rmas.service_id')
                   ->select(['services.id','services.receiptno','services.rmano','services.rmadate' ,'service_rmas.item_name','service_rmas.quantity','service_rmas.purchaseprice','service_rmas.sellingprice','service_rmas.payment','service_rmas.reference','service_rmas.category_id']);


         $service->when(!empty(request('date_fr')), function($q) use ($date_fr){
           return $q->where('services.rmadate','>=',$date_fr);
         });

         $service->when(!empty(request('date_to')), function($q) use ($date_to){
           return $q->where('services.rmadate','<=',$date_to);
         });

         $service->when(in_array(request('payment'),array('0','1')), function($q) {
           return $q->where('service_rmas.payment',request('payment'));
         });
         //dump($purchases->toSql());
         $service->when(!empty(request('category_id')), function($q) use($category_id){
           return $q->where('service_rmas.category_id',$category_id);
         });

        if(empty($date_fr) && empty($date_to))
            $service->whereNull('services.id');




           $datatables=Datatables::of($service);

           $datatables->addColumn('subtotal',function($service) {
                 //$rmar=Service::find($s->customerid);
                 return ($service->quantity * $service->purchaseprice);
           });

         $datatables->editColumn('payment',function($service){
               return ucfirst((($service->payment==1)?'Yes':'No'));
          });

          // Category is a tiny table (3 real rows) — prefetch once, not per row.
          $categoriesById = Category::all()->keyBy('id');
          $datatables->addColumn('vendor',function($service) use ($categoriesById){
                $category=$categoriesById->get($service->category_id);
                return $category ? ucfirst($category->name) : '';
           });


           $result=$datatables->make(true);

           return $result;
        //   dd($result);

    }

        return view('backend.services.report_rma', compact('enginner','payment','categorys'));
    }

    public function report_cash(Request $request){
      $services=null;

      //$enginner=User::whereHas('roles', function($q){
        //$q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      $vendor=Vendor::all()->pluck('companyname', 'id');

      if($request->ajax()){

         $date_fr='';$date_to='';

         if(!empty(request('date_fr')))
            $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

         if(!empty(request('date_to')))
            $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

      //  $purchases=Purchase::join('purchases_details', 'purchases.id', '=', 'purchases_details.purchase_id')
        //  ->select(['purchases.id', 'purchases.date','purchases.service_id', 'purchases.rma', 'purchases.vendor_id', 'purchases.payment', 'purchases.paydate','purchases_details.item_id','purchases_details.quantity','purchases_details.purchaseprice']);
         // Eager loads chained on this first builder survive the union()
         // calls below (union() only combines the SQL, not the Eloquent
         // wrapper's eager-load config) — avoids per-row Customer::find()
         // and the always-fired media query from Service's 'logo' $appends.
         $service=Service::with(['customer','service_rma','media'])
                 ->select('id','date','receiptno','customerid','model','description','chargeamount')
                 ->selectRaw('? as transaction',['Receipt']);
         $service->when(!empty(request('date_fr')), function($q) use ($date_fr){
           return $q->where('date','>=',$date_fr);
         });

         $service->when(!empty(request('date_to')), function($q) use ($date_to){
           return $q->where('date','<=',$date_to);
         });

         if(empty($date_fr) && empty($date_to))
             $service->whereNull('id');

         $payment=Service::select('id','paydate','receiptno','customerid','model','description')
                 ->selectRaw('chargeamount * ? as chargeamount',[-1])
                 ->selectRaw('? as transaction',['Payment'])
                 ->where('payment','>',0);

         $payment->when(!empty(request('date_fr')), function($q) use ($date_fr){
           return $q->where('paydate','>=',$date_fr);
         });

         $payment->when(!empty(request('date_to')), function($q) use ($date_to){
           return $q->where('paydate','<=',$date_to);
         });

         if(empty($date_fr) && empty($date_to))
             $payment->whereNull('id');

         $transfer=Service::select('id','transfer_hq_date','receiptno','customerid','model','description')
                 ->selectRaw('chargeamount * ? as chargeamount',[-1])
                 ->selectRaw('? as transaction',['Transfer'])
                 ->where('transfer_hq','>',0);

         $transfer->when(!empty(request('date_fr')), function($q) use ($date_fr){
           return $q->where('transfer_hq_date','>=',$date_fr);
         });

         $transfer->when(!empty(request('date_to')), function($q) use ($date_to){
           return $q->where('transfer_hq_date','<=',$date_to);
         });

         if(empty($date_fr) && empty($date_to))
             $transfer->whereNull('id');

         $report = $service->union($payment)->union($transfer);

         //dump($report->toSql());

           $datatables=Datatables::of($report);
           $datatables->addColumn('customer',function(Service $s) {
                 return empty($s->customer) ? '' : $s->customer->first_name;
           });


         $datatables->editColumn('chargeamount',function(Service $service){
           $subamount=0;
           foreach ($service->service_rma as $service_rma) {
               $subamount=$subamount + ($service_rma->quantity * $service_rma->sellingprice);
           }
           return ($subamount+$service->chargeamount);

          });

           return $datatables->make(true);

    }

        return view('backend.services.report_cash');
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $rmacodes=Rmacode::all()->pluck('code', 'id');
      $problems=Problem::all()->pluck('problem', 'id');
      $brands=Brand::orderBy('name','asc')->pluck('name', 'id');
      $customers=Customer::all()->pluck('full_name', 'id');
      $vendors=Vendor::all()->pluck('companyname', 'id');
      $categorys=Category::orderBy('name','asc')->pluck('name','id');
    //  $user_enginners= User::whereHas('roles', function($q){

  //$q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');



      // Same dead code as show(): 'purchases' has no service_id column.
      $credit_note_type=array(1=>'Credit Note',2=>'Cancel Credit Note',3=>'Clear Credit Note');

      $service = Service::find($id);

      // Same portability issue as create(): havingRaw() referencing withCount's
      // correlated-subquery aliases is a MySQL-only extension. Filter in PHP
      // instead, keeping the currently-assigned engineer regardless of busy status.
      $user_enginners= User::select('id','first_name','last_name')->whereHas('roles', function($q){
            $q->where('name', 'service-engineer')->where('active',1); })
            ->withCount(["enginner_busy as busy_limit"=> function($q){
                $q->select('busy_limit');
            },"services" => function($q){
                $q->WhereIn('status',['start','progress']);
            }])
            ->get()
            ->filter(function($user) use ($service){
                return ($user->services_count < $user->busy_limit) || ($user->id == $service->assignto);
            })
            ->pluck('name', 'id');

    //  dd($user_enginners->toSql());
      if(auth()->user()->isServiceEnginner() && auth()->user()->id!=$service->assignto)
            return redirect()->route('admin.service.show',['id'=>$id])
                  ->withFlashDanger('You have no permission to edit this service no.');

      $status = $this->status;
      return view('backend.services.edit',compact('service','problems','customers','user_enginners','status','vendors','credit_note_type','categorys','brands','rmacodes'));

    }


    public function transfer_clearance(Request $request){
      $services=null;

      $enginner=User::whereHas('roles', function($q){
        $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      $vendor=Vendor::all()->pluck('companyname', 'id');

      $payment = [1=>"Yes",0=>"No"];

      if($request->ajax()){

         $date_fr='';$date_to='';
         //$payment_req=request('payment');
         $datetype=request('datetype');
         $serviceno=request('serviceno');
         //$rmano=request('rmano');

         $chks=request('chk');
         $cntype=request('cntype');
         $cndate=request('cndate');
         $reason=request('reason');

        // $reference=request('chk_ref');

         if(!empty($chks)){

         foreach($chks as $k=>$chk){
           $rma_tbl= Transfer::find($k);
           if($chk==1){

             $rma_tbl->type=$cntype;
             $rma_tbl->cleardate =$cndate;
             $rma_tbl->reason=$reason;

             $rma_tbl->save();
           }

         }
        }



         if(!empty(request('date_fr')))
            $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

         if(!empty(request('date_to')))
            $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

        //$purchases=Purchase::join('purchases_details', 'purchases.id', '=', 'purchases_details.purchase_id')
          //->select(['purchases.id', 'purchases.date','purchases.service_id', 'purchases.rma', 'purchases.vendor_id', 'purchases.payment', 'purchases.paydate','purchases_details.item_id','purchases_details.quantity','purchases_details.purchaseprice']);
        //  $service=Service::join('service_rmas','services.id','=','service_rmas.service_id')
          //         ->select(['services.id','service_rmas.id as rmaid','services.receiptno','services.rmano','services.rmadate as date','services.date as dateservice' ,'service_rmas.item_name','service_rmas.quantity','service_rmas.purchaseprice','service_rmas.sellingprice','service_rmas.payment','service_rmas.reference']);


        $service=Transfer::with(array('service'=>function($q){
              $q->select('receiptno','date');
          },'vendor'));

        $service->whereHas('service',function($q) use ($date_fr,$date_to,$datetype,$serviceno){
              if($datetype==1 && !empty($date_fr))
                 $q->where('services.date','>=',$date_fr);
              if($datetype==1 && !empty($date_to))
                 $q->where('services.date','<=',$date_to);
              if(!empty($serviceno))
                  $q->where('receiptno',$serviceno);
            });


         $service->when(!empty($date_fr), function($q) use ($date_fr,$datetype){
           if($datetype==0)
              return $q->where('transferdate','>=',$date_fr);
         });

         $service->when(!empty($date_to), function($q) use ($date_to,$datetype){
           if($datetype==0)
              return $q->where('transferdate','<=',$date_to);
         });




        // Was commented out — with no filters this ran unbounded across the
        // full Transfer table joined to services, hitting the vendor/service
        // N+1 below on every real row. Matches the guard convention used
        // elsewhere in this controller.
        if(empty($date_fr) && empty($date_to) && empty($serviceno))
            $service->whereNull('transfers.id');

           $datatables=Datatables::of($service);

           $datatables->addColumn('subtotal',function($service) {
                 //$rmar=Service::find($s->customerid);
                 return ($service->quantity * $service->purchaseprice);
           });

           $datatables->addColumn('date',function($service) {
              return $service->service->first()->date;
           });

           $datatables->addColumn('receiptno',function($service) {
              return $service->service->first()->receiptno;
           });

           $datatables->addColumn('vendor',function($service) {
              return $service->vendor->companyname;
           });

         $datatables->addColumn('action',function($service){
              $chk= ($service->payment==1)?'checked':'';
              return '<input type="checkbox" ' . $chk . ' class="form-control" name="chk_pay[]" value="' . $service->id . '">';
               //return ucfirst((($service->payment==1)?'Yes':'No'));
          });


          $datatables->rawColumns(['action']);

           $result=$datatables->make(true);

           return $result;
        //   dd($result);

    }
        return view('backend.services.transfer_clearance', compact('payment'));
    }

    public function rma_paid(Request $request){
      $services=null;

      $enginner=User::whereHas('roles', function($q){
        $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      $vendor=Vendor::all()->pluck('companyname', 'id');
      $categorys=Category::all()->pluck('name', 'id');
      $payment = [1=>"Yes",0=>"No"];

      if($request->ajax()){

         $date_fr='';$date_to='';
         $payment_req=request('payment');
         $datetype=request('datetype');
         $serviceno=request('serviceno');
         $rmano=request('rmano');
         $category_id=request('category_id');
         $chks=request('chk');
         $reference=request('chk_ref');

         if(!empty($chks)){
         foreach($chks as $k=>$chk){
           $rma_tbl= Service_rma::where('id',$k)->first();


           if($chk=='0'){
              $rma_tbl->paydate=null;
              $rma_tbl->reference=null;
           }elseif($rma_tbl->payment==0){
              $rma_tbl->paydate=date('Y-m-d');
              $rma_tbl->reference=null;
           }else
              $rma_tbl->reference=$reference[$k];

           $rma_tbl->payment=$chk;


           $rma_tbl->save();
         }
        }



         if(!empty(request('date_fr')))
            $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

         if(!empty(request('date_to')))
            $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

          //  $date_fr ='2020-05-01';$date_to ='2020-05-30';
        //$purchases=Purchase::join('purchases_details', 'purchases.id', '=', 'purchases_details.purchase_id')
          //->select(['purchases.id', 'purchases.date','purchases.service_id', 'purchases.rma', 'purchases.vendor_id', 'purchases.payment', 'purchases.paydate','purchases_details.item_id','purchases_details.quantity','purchases_details.purchaseprice']);
          $service=Service::with('media')
                   ->join('service_rmas','services.id','=','service_rmas.service_id')
                   ->select(['services.id','service_rmas.id as rmaid','services.receiptno','services.rmano','services.rmadate','services.date' ,'service_rmas.item_name','service_rmas.quantity','service_rmas.purchaseprice','service_rmas.sellingprice','service_rmas.payment','service_rmas.reference','service_rmas.category_id']);



         $service->when(!empty($date_fr), function($q) use ($date_fr,$datetype){
           if($datetype==0)
              return $q->whereDate('services.rmadate','>=',$date_fr);
           else
              return $q->whereDate('services.date','>=',$date_fr);
         });

         $service->when(!empty($date_to), function($q) use ($date_to,$datetype){
           if($datetype==0)
              return $q->whereDate('services.rmadate','<=',$date_to);
           else
              return $q->whereDate('services.date','<=',$date_to);
         });

          $service->when(!empty(request('serviceno')), function($q) use ($serviceno){
              return $q->where('services.receiptno',$serviceno);
          });

          $service->when(!empty(request('rmano')), function($q) use ($rmano){
              return $q->where('services.rmano',$rmano);
          });

         $service->when(in_array(request('payment'),array('0','1')), function($q) {
           return $q->where('service_rmas.payment',request('payment'));
         });

         $service->when(!empty(request('category_id')), function($q) use($category_id){
           return $q->where('service_rmas.category_id',$category_id);
         });
         //dump($purchases->toSql());

        if(empty($date_fr) && empty($date_to) && empty($serviceno) && empty($rmano) && empty($payment_req))
            $service->whereNull('services.id');



           $datatables=Datatables::of($service);

           $datatables->addColumn('subtotal',function($service) {
                 //$rmar=Service::find($s->customerid);
                 if(empty($service->quantity))
                    return 0;
                 return ($service->quantity * $service->purchaseprice);
           });

            //  dd($service->get());

         $datatables->editColumn('payment',function($service){
              $chk= ($service->payment==1)?'checked':'';
              return '<input type="checkbox" ' . $chk . ' class="form-control" name="chk_pay[]" value="' . $service->rmaid . '">';
               //return ucfirst((($service->payment==1)?'Yes':'No'));
          });

          $datatables->editColumn('reference',function($service){

               return '<input type="text" class="form-control" name="chk_ref[]" value="' . (empty($service->reference)?'':$service->reference) . '">';
                //return ucfirst((($service->payment==1)?'Yes':'No'));
           });

          $datatables->rawColumns(['reference','payment']);

           $result=$datatables->make(true);

           return $result;
        //   dd($result);

    }
        return view('backend.services.rma_paid', compact('payment','categorys'));
    }

    public function calling(Request $request){
      $services=null;

      $enginner=User::whereHas('roles', function($q){
        $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      if($request->ajax()){

         $date_fr='';$date_to='';
         $status1=request('status1');
         $status2=request('status2');
         $chks=request('chk');


         if(!empty($chks)){
         foreach($chks as $k=>$chk){
           $rma_tbl= ServiceStatus::where('id',$k)->first();


           //if($chk=='0'){
            //  $rma_tbl->calling_date=null;
           //}elseif($rma_tbl->calling_status==0){
           if($chk==1 && empty($rma_tbl->calling_status)){
              $rma_tbl->calling_date=date('Y-m-d');
              $rma_tbl->calling_status=$status2;
           }

           $rma_tbl->save();
         }
        }

        // Eager-load every relation the closures below touch per row
        // (customer, enginner, problem, transfer, service_rma, media) —
        // this worklist can legitimately match thousands of real rows
        // (15,479 against real production data with no filter applied),
        // so N+1 here is far more costly than on a small report.
        $service=Service::with(['customer','enginner','problem','transfer','service_rma','media'])
                   ->join('services_status','services.id','=','services_status.service_id')
                   ->select(['services.*' ,'services_status.id as sid','services_status.status as status1','services_status.calling_status','services_status.calling_date','services_status.created_at as status_created_at']);

      //  $service->where('services_status.status','<>','not start')->whereNull('services_status.calling_status')->orWhereIn('services_status.calling_status',[null,1,2])->where('services.delivered','=',0);
         // Grouped in one where() so the 90-day bound below ANDs onto BOTH
         // OR branches — chaining ->where()->orWhere()->where(date) instead
         // would only bind the date to the second branch (SQL operator
         // precedence: A OR (B AND date)), leaving the first branch
         // completely unbounded. Confirmed via a real count: the ungrouped
         // form still matched 15,461 of 15,479 rows; grouped correctly it's
         // 1,136 — bounding the worklist to tickets whose status actually
         // changed in the last 90 days, not everything back to 2019.
         $service->where(function ($outer) {
            $outer->where(function ($query) {
                $query->where('services_status.status','<>' , 'not start')
                ->whereNull('services_status.calling_status');
            })->orWhere(function($query) {
                $query->whereIn('services_status.calling_status',[1,2])
                ->where('services.delivered','=',0);
            });
         })->where('services_status.created_at', '>=', Carbon::now()->subDays(90));


        //if(empty($status1))
          //    $status1=0;
         //dump($purchases->toSql());

        //if($status1==0)
          //  $service->whereNull('services_status.calling_status');
        //elseif($status1>0)
          //  $service->where('services_status.calling_status','<>','done');


           $datatables=Datatables::of($service);

           $datatables->addColumn('action',function($service){
                $chk= in_array($service->calling_status,['1','2'])?'checked':'';
                $disabled=in_array($service->calling_status,['1','2'])?'disabled':'';
                return '<input type="checkbox" ' . $chk . ' ' . $disabled . ' class="form-control" name="chk_pay[]" value="' . $service->sid . '">';
                 //return ucfirst((($service->payment==1)?'Yes':'No'));
            });

        //   dump($service->toSql());
        //   dump($service->get());
          $datatables->addColumn('engineer',function($service){
            if(!empty($service->enginner))
              return ucfirst($service->enginner->first_name) . ' ' . ucfirst($service->enginner->last_name);
            else
              return 'N/A';

           });

           $datatables->editColumn('chargetype',function($service) {
              return ucfirst($service->chargetype);
           });

           $datatables->editColumn('status1',function($service) {
              return ucfirst($service->status1);
           });


           $datatables->editColumn('calling_status',function($service) {

              $status=[0=>'Pending',1=>'Successful',2=>'Unsuccesful'];
              if(empty($service->calling_status))
                return ucfirst($status[0]);
              else
                return ucfirst($status[$service->calling_status]);
           });

           $datatables->addColumn('customername',function($service) {
                 if(empty($service->customer)) return '';
                 return ucfirst($service->customer->first_name) . ' ' . ucfirst($service->customer->last_name);
           });

           // Was ServiceStatus::find($service->sid)->dtt3 — a fresh query
           // per row for an accessor ('dtt3') that doesn't exist anywhere in
           // this codebase, so Carbon::parse(null) silently parsed as "now"
           // every time (always showed "a few seconds ago"). status_created_at
           // is already selected on this same row — no query needed, and it's
           // the actual timestamp this column was presumably meant to show.
           $datatables->addColumn('tat',function($service) {
                 return  Carbon::parse($service->status_created_at)->diffForHumans();
           });



           $datatables->addColumn('problem',function($service) {
               if(count($service->problem)>0)
                 return $service->problem->pluck('problem')->implode(' ');
               else
                 return '';
           });

           $datatables->editColumn('chargeamount',function($service){
           $subamount=0;
           foreach ($service->service_rma as $service_rma) {
               $subamount=$subamount + ($service_rma->quantity * $service_rma->sellingprice);
           }
           return ($subamount+$service->chargeamount);
          });

           $datatables->addColumn('transfer',function($service){

               //return $s->transfer->count();

               if($service->transfer->count()==0)
                 return '-';
               else if(!empty($service->transfer->first()->returndate))
                 return 'In';
               else
                 return 'Out';

           });



           $datatables->editColumn('warrenty',function($service){
               return ucfirst((($service->warrenty==0)?"out":"in"));
           });

           $datatables->editColumn('payment',function($service){
               return ucfirst((($service->payment==0)?"No":"Yes"));
           });

           // receiver(userid) does User::where('id',...)->first() fresh per
           // row — a small table (55 real rows), so prefetch it once instead.
           $usersById = User::all()->keyBy('id');
           $datatables->addColumn('receiver',function($service) use ($usersById) {
             $u = $usersById->get($service->userid);
             return $u ? ucfirst($u->first_name) . ' ' . ucfirst($u->last_name) : '';
           });


          $datatables->rawColumns(['action']);

           $result=$datatables->make(true);


           return $result;
        //   dd($result);

    }
        return view('backend.services.service_calling');
    }

    public function cash_transfer(Request $request){
      $services=null;

      $enginner=User::whereHas('roles', function($q){
        $q->where('name', 'service-engineer'); })->get()->pluck('name', 'id');

      $vendor=Vendor::all()->pluck('companyname', 'id');

      //$payment = [1=>"Yes",0=>"No"];

      if($request->ajax()){

         $date_fr='';$date_to='';
         $datetype=request('datetype');
         $serviceno=request('serviceno');
         if(empty($datetype))
                $datetype=0;
         $chks=request('chk');

         if(!empty($chks)){
         foreach($chks as $k=>$chk){
           $rma_tbl= Service::find($k);


           if($chk=='0')
              $rma_tbl->transfer_hq_date=null;
           elseif($rma_tbl->transfer_hq==0)
              $rma_tbl->transfer_hq_date=date('Y-m-d');

           $rma_tbl->transfer_hq=$chk;

           $rma_tbl->save();
         }
        }

         if(!empty(request('date_fr')))
            $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

         if(!empty(request('date_to')))
            $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

        //$purchases=Purchase::join('purchases_details', 'purchases.id', '=', 'purchases_details.purchase_id')
          //->select(['purchases.id', 'purchases.date','purchases.service_id', 'purchases.rma', 'purchases.vendor_id', 'purchases.payment', 'purchases.paydate','purchases_details.item_id','purchases_details.quantity','purchases_details.purchaseprice']);
          $service=Service::with(['problem','service_rma','media'])->orderby('id','asc');

         $service->when(!empty(request('date_fr')), function($q) use ($date_fr,$datetype){
           if($datetype==0)
              return $q->whereDate('paydate','>=',$date_fr);
           else
              return $q->whereDate('date','>=',$date_fr);
         });

         $service->when(!empty(request('date_to')), function($q) use ($date_to,$datetype){
           if($datetype==0)
              return $q->whereDate('paydate','<=',$date_to);
           else
              return $q->whereDate('date','<=',$date_to);
         });

          $service->when(!empty(request('serviceno')), function($q) use ($serviceno){
              return $q->where('receiptno',$serviceno);
          });

         //dump($purchases->toSql());

        if(empty($date_fr) && empty($date_to) && empty($serviceno) && empty($rmano) && empty($payment_req))
            $service->whereNull('id');

          $datatables=Datatables::of($service);

          $datatables->addColumn('payment',function($service){
              return ($service->payment==1?'Yes':'No');
          });

         $datatables->addColumn('paymentchk',function($service){
              $chk= ($service->transfer_hq==1)?'checked':'';
              return '<input type="checkbox" ' . $chk . ' class="form-control" name="chk_pay[]" value="' . $service->id . '">';
               //return ucfirst((($service->payment==1)?'Yes':'No'));
          });

          $datatables->addColumn('problem',function($service){
              if(count($service->problem)>0)
                return $service->problem->pluck('problem')->implode(' ');
              else
                return '';
          });

          $datatables->addColumn('amount',function($service){
                $subamount=0;
                foreach ($service->service_rma as $service_rma) {
                    $subamount=$subamount + ($service_rma->quantity * $service_rma->sellingprice);
                }
                return ($subamount+$service->chargeamount);
          });

          $datatables->rawColumns(['paymentchk']);

           $result=$datatables->make(true);

           return $result;
        //   dd($result);

    }
        return view('backend.services.cash_transfer');
    }

    public function shipping_destroy($id){
        $shipping=Shipping::findOrFail($id);
        $shipping->service()->delete();
        $shipping->delete();

        return redirect()->route('admin.service.shipping',['id'=>'create'])
                        ->withFlashSuccess(__('The Shipping has been deleted.'));

    }

    public function transfer_destroy($id){
        $transfer=Transfer::findOrFail($id);
        $transfer->service()->delete();
        $transfer->delete();

        return redirect()->route('admin.service.transfer',['id'=>'create'])
                        ->withFlashSuccess(__('The Transfer has been deleted.'));

    }

    public function transfer($id){

      $vendors=Vendor::all()->pluck('companyname', 'id');

      if($id>0 && request('model')=='update'){
          $transfer=Transfer::find($id);
          $transfer->transferdate=request('transferdate');
          $transfer->cleardate=request('cleardate');
          $transfer->returndate=request('returndate');
          $transfer->reason=request('reason');
          $transfer->type=request('type');
          $transfer->vendor_id=request('vendor_id');
          $transfer->cnno=request('cnno');
          $transfer->cnprice=request('cnprice');

          $transfer->save();
          $transfer->service()->sync(request('serviceno'));

          return redirect()->route('admin.service.transfer',['id'=>'create'])
                          ->withFlashSuccess(__('The Transfer has been updated.'));
      }

      if($id!='create' && $id>0){
        $transfer=Transfer::find($id);
        //dd($shipping->service()->pluck('receiptno','services.id'));
        return view('backend.services.service_transfer',compact('transfer','vendors'));
      }

      if($id=='create' && request('model')=='create'){
          $transfer=new Transfer();
          $transfer->transferdate=request('transferdate');
          $transfer->cleardate=request('cleardate');
          $transfer->returndate=request('returndate');
          $transfer->reason=request('reason');
          $transfer->type=request('type');
          $transfer->vendor_id=request('vendor_id');
          $transfer->cnno=request('cnno');
          $transfer->cnprice=request('cnprice');

          $transfer->save();

          $transfer->service()->sync(request('serviceno'));

         return redirect()->route('admin.service.transfer',['id'=>'create'])
                         ->withFlashSuccess(__('The Transfer has been created.'));

      }


    if(request()->ajax()){

         $transfers=Transfer::with(['vendor','service'])->orderby('transferdate','asc');
          $datatables=Datatables::of($transfers);

          $datatables->addColumn('vendor',function($transfers){
              return (!empty($transfers->vendor)?$transfers->vendor->companyname:'');
          });
          $datatables->addColumn('action',function($transfers){
            return '
            <div class="btn-group btn-group-sm" role="group" aria-label="'.__('labels.backend.access.users.user_actions').'">
            <a href="' . route('admin.service.transfer.edit',['id'=>$transfers->id]) . '" class="btn btn-primary"><i class="fas fa-edit" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.edit').'"></i></a>
            <a href="' . route('admin.service.transfer.destroy',$transfers) . '" data-method="delete"
     			 data-trans-button-cancel="'.__('buttons.general.cancel').'"
     			 data-trans-button-confirm="'.__('buttons.general.crud.delete').'"
     			 data-trans-title="'.__('strings.backend.general.are_you_sure').'"
     			 class="btn btn-danger"><i class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.delete').'"></i></a>
           </div>
           ';
          });

          $datatables->addColumn('services',function($transfers){
            $arrs=[];
            foreach($transfers->service as $service)
              //$arrs[]='<span class="badge badge-info">'. $service->receiptno . '</span>';
              $arrs[]=$service->receiptno;
              return join(" ",$arrs);
          });

          $datatables->rawColumns(['action']);

         return $datatables->make(true);


      }

      $transfer='';
      return view('backend.services.service_transfer',compact('transfer','vendors'));
    }

    public function shipping($id){

      if($id>0 && request('model')=='update'){
          $shipping=Shipping::find($id);
          $shipping->shipreference=request('reference');
          $shipping->shipdate=request('date');
          $shipping->shipcost=request('costing');

          $shipping->save();
          $shipping->service()->sync(request('serviceno'));

          return redirect()->route('admin.service.shipping',['id'=>'create'])
                          ->withFlashSuccess(__('The Shipping has been updated.'));
      }

      if($id!='create' && $id>0){
        $shipping=Shipping::find($id);
        //dd($shipping->service()->pluck('receiptno','services.id'));
        return view('backend.services.service_shipping',compact('shipping'));
      }

      if($id=='create' && request('model')=='create'){
          $shipping=new Shipping();
          $shipping->shipreference=request('reference');
          $shipping->shipdate=request('date');
          $shipping->shipcost=request('costing');

          $shipping->save();

          $shipping->service()->sync(request('serviceno'));

         return redirect()->route('admin.service.shipping',['id'=>'create'])
                         ->withFlashSuccess(__('The Shipping has been created.'));

      }


    if(request()->ajax()){

         $shippings=Shipping::with('service')->orderby('shipdate','asc');
          $datatables=Datatables::of($shippings);

          $datatables->addColumn('action',function($shippings){
            return '
            <div class="btn-group btn-group-sm" role="group" aria-label="'.__('labels.backend.access.users.user_actions').'">
            <a href="' . route('admin.service.shipping.edit',['id'=>$shippings->id]) . '" class="btn btn-primary"><i class="fas fa-edit" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.edit').'"></i></a>
            <a href="' . route('admin.service.shipping.destroy',$shippings) . '" data-method="delete"
     			 data-trans-button-cancel="'.__('buttons.general.cancel').'"
     			 data-trans-button-confirm="'.__('buttons.general.crud.delete').'"
     			 data-trans-title="'.__('strings.backend.general.are_you_sure').'"
     			 class="btn btn-danger"><i class="fas fa-trash" data-toggle="tooltip" data-placement="top" title="'.__('buttons.general.crud.delete').'"></i></a>
           </div>
           ';
          });

          $datatables->addColumn('services',function($shippings){
            $arrs=[];
            foreach($shippings->service as $service)
              $arrs[]='<span class="badge badge-info">'. $service->receiptno . '</span>';

              return join(" ",$arrs);
          });

          $datatables->rawColumns(['services','action']);

         return $datatables->make(true);


      }

      $shipping='';
      return view('backend.services.service_shipping',compact('shipping'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    //  dump($request->input('logo'));
    //  die();
      $service = Service::find($id);

      // request('field.0') is the null-safe way to read a possibly-array,
      // possibly-absent form field — $request->field[0] triggers a PHP
      // "illegal offset" warning (which this app's exception handler turns
      // fatal) whenever the field is a scalar or missing entirely, even
      // inside empty(); confirmed live via a crafted request missing one
      // of these checkbox fields.
      $request->merge(['warrenty' => (empty(request('warrenty.0'))?0:request('warrenty.0'))]);
      //$request->merge(['chargetype' => $request->chargetype, 0]);
      $request->merge(['payment' => (empty(request('payment.0'))?0:request('payment.0'))]);

      $request->merge(['transfer_hq' => (empty(request('transfer_hq.0'))?0:request('transfer_hq.0'))]);

      $request->merge(['ordered' => (empty(request('ordered.0'))?0:request('ordered.0'))]);

      $request->merge(['delivered' => (empty(request('delivered.0'))?0:request('delivered.0'))]);

      $request->merge(['shipping' => (empty(request('shipping.0'))?0:request('shipping.0'))]);

      $request->merge(['rmacode' => (empty($request->rmacode)?0:$request->rmacode)]);

      // ------------- Protect on payment and delivered ---------------------
      if($request->payment==1 && in_array($request,['not start','start','progress','pending'])){
        return redirect()->back()
                ->withFlashDanger('You are not allow do payment when status in [not stat,start,progress,pending]');
      }

      if($request->delivered==1 && $request->payment==0){
        return redirect()->back()
                ->withFlashDanger('Please set payment to yes before tick on delivered.');
      }

      /*if((request('payment')==0) && request('status')=='delivered')
          return redirect()->back()
                  ->withFlashDanger('Please set payment to yes before update status to delivered.');
            */
      if($request->ordered==0)
        $request->merge(['ordered_comment' => '']);

      if($request->payment==0)
          $request->merge(['paydate' => null]);

      if($request->transfer_hq==0)
          $request->merge(['transfer_hq_date' => null]);
      else{
          if($service->transfer_hq==0)
             $request->merge(['transfer_hq_date' => date('Y-m-d')]);
        }

      if(request('chargetype.0')==0){
        $request->merge(['chargetype' => 'free']);
        $request->merge(['chargeamount' => '0']);
      }else
        $request->merge(['chargetype' => 'cash']);

    //$request->merge(['description' => nl2br($request->description)]);

      if($request->status=='incomplete'){
          $request->merge(['chargeamount' => '0']);
      }

      $input = $request->all();

      // isAdmin() (the top-level super-Administrator role) never also holds
      // service-admin/receptionist/engineer in real data — without this
      // fallback $validate_arr is never assigned for that role, and the
      // validate() call below throws when a super-admin submits this form.
      if(auth()->user()->isServiceAdmin() || auth()->user()->isAdmin()){
          $validate_arr=[
              'date' => ['required', 'date_format:"' . config('app.date_format') . '"'],
              'buyingdate' => 'nullable', //['required', 'date_format:"' . config('app.date_format') . '"'],
              'serialno' => 'required',
              'model' => ['required', 'string', 'max:255'],
              'problem' => ['required','numeric'],
              'chargeamount' => ['nullable','numeric'],
              'labor_cost' => ['nullable','numeric'],
              'description' => ['required','string','max:1000'],
              'order_comment'=>'nullable',
              'phone'=>'required',
              'solution'=>'nullable',
              'paydate'=>'nullable',

          ];



    }elseif(auth()->user()->isServiceReceptionist()){
        $validate_arr=[
              'description' => ['required','string','max:1000'],
              'phone'=>'required',
              'chargeamount' => ['nullable','numeric'],
              'order_comment'=>'nullable',
              'solution'=>'nullable',
              'paydate'=>'nullable',
          ];

    }elseif(auth()->user()->isServiceEnginner()){
          $validate_arr=[
              'solution'=>'required',
              'ordered_comment'=>'required_if:ordered,1',
              //'problem' => ['required','numeric'],
          ];

    }



    if($request->shipping>0){
      $validate_arr['shipname']='required';
      $validate_arr['shipdate']=['required', 'date_format:"' . config('app.date_format') . '"'];
      $validate_arr['shipcost']=['required','numeric'];
   }


   if($request->ordered==1 && !auth()->user()->isServiceReceptionist()){

     //$service_detail = [];
     $ids=request('rmaids');
     $cats = request('category');
     $items = (array) request('item_name');
     $quantitys = request('quantity');
     $purchaseprices = request('purchaseprice');
     $sellingprices = request('sellingprice');

    // $paymentrma = (array) request('paymentrma');

    // dd($paymentrma);

     $test=false;

     for($i=0;$i < count($items);$i++){

       $data=array('category_id'=>(empty($cats[$i])?0:$cats[$i]),'quantity'=>$quantitys[$i],'purchaseprice'=>$purchaseprices[$i],'item_name'=>$items[$i],'rmano'=>request('rmano'),'sellingprice'=>$sellingprices[$i]);
      // if($i==count($items)-1)
        //  dd($data);
        $validator=Validator::make($data, [
           'rmano' =>'required',
           'category_id' => ['required','numeric','gt:0'],
           'quantity' => ['required','numeric','gt:0'],
           'item_name' => ['required','string'],

       ]);

       if(!$validator->passes()){
             $test=true;
             break;
           }
     }

   if(count($items)<=0)
        return redirect()->back()
              ->withFlashDanger('You must fill categroy, item name, quantiy, price.');

   if($test)
      return redirect()->back()
            ->withFlashDanger('Invalid RMA #, Item Name, Quanity, Price');
    //  $service->service_rma->delete();
            $service->service_rma()->each(function($rma) use($ids) {
               if(!in_array($rma->id,$ids))
                  $rma->delete(); // <-- raise another deleting event on Post to delete comments
            });

     $rma_detail=[];

     for($i=0;$i < count($items);$i++){
         //$item = Item::find($items[$i])->first();
         //$cat_id = $item->category_id;
         if($ids[$i]==0)
           $rma_detail[$i] = new Service_rma;
         else
           $rma_detail[$i] = Service_rma::where('id',$ids[$i])->first();

          $rma_detail[$i]->category_id=$cats[$i];
          $rma_detail[$i]->item_name=$items[$i];
          $rma_detail[$i]->quantity=$quantitys[$i];
          $rma_detail[$i]->purchaseprice=$purchaseprices[$i];
          $rma_detail[$i]->sellingprice=$sellingprices[$i];
          $rma_detail[$i]->payment=in_array($ids[$i],(array) request('paymentrma'));

          if($ids[$i]==0){
              $rma_detail[$i]->paydate=null;
          }else{
          //  $currma = Service_rma::where('id',$ids[$i])->first();
            if(($rma_detail[$i]->payment==0) && in_array($ids[$i],(array) request('paymentrma')))
                  $rma_detail[$i]->paydate=date('Y-m-d');
            elseif(($rma_detail[$i]->payment==1) && in_array($ids[$i],(array) request('paymentrma')))
                  $rma_detail[$i]->paydate=$rma_detail[$i]->paydate;
            else
                  $rma_detail[$i]->paydate=null;
          }

     }
    // $rma_detail->saveMany($rma_detail);

     $service->service_rma()->saveMany($rma_detail,);


     //}
   }else {
     if(!auth()->user()->isServiceReceptionist()){
      $service->service_rma()->delete();
      $service->rmano=null;
      $service->rmadate=null;
    }
   } // end of if ordered_rma


   if($request->ordered==1 && !auth()->user()->isServiceReceptionist()){
      $validate_arr['rmano']='required';
   }

   $this->validate($request, $validate_arr);

   if($request->payment==1){
          if(in_array($request->status, array('not start','start','pending','processing')))
            return redirect()->route('admin.service.edit',['id'=>$id])
                            ->withFlashDanger(__('alerts.backend.service.warning1'));
      }



    //  if(auth()->user()->isServiceAdmin()){

        $problem = Problem::where('id',$request->problem)->first();
        $service->problem()->sync($problem);
        $brand = Brand::where('id',request('brand'))->first();
        $service->brand()->sync($brand);
    //  }

      if(in_array(request('status'),array('not start','start','pending','processing') ) || in_array($service->status, ['complete','incomplete']))

      $service->timestamps=false;

      // --- Add update status to status log table


      if(request('status')!=$service->status){
      $service_status[] = new ServiceStatus([
                'userid' => auth()->user()->id,
                'status' => request('status'),

              ]);

      $service->service_status()->saveMany($service_status);
      }

      // Engineers only touch diagnosis/status/RMA fields per the SOP's Engineer Solution
      // step; per-role $validate_arr above only controls which fields are required, not
      // which fields actually get saved, so clamp financial/customer/scheduling fields
      // back to their existing values regardless of what was submitted.
      if(auth()->user()->isServiceEnginner()){
          $locked_fields = ['chargeamount','labor_cost','chargetype','payment','paydate','customerid',
              'date','phone','serialno','model','assignto','warrenty','buyingdate'];
          foreach($locked_fields as $field){
              $input[$field] = $service->{$field};
          }
      }

      // labor_cost is an internal cost figure, not part of the customer-facing
      // payment flow — admin-only regardless of role, unlike chargeamount which
      // receptionist legitimately handles at the payment/delivery step.
      if(!auth()->user()->isServiceAdmin()){
          $input['labor_cost'] = $service->labor_cost;
      }

      if(strcmp($input['date'],$service->date)==0)
              unset($input['date']);
              //$input['date']=$service->date;
        //dd($input);
      $service->update($input);

      // ------------- Remove List from Calling List ---------------
      if($service->payment==1 && in_array($service->status,array('complete','incomplete'))){
          ServiceStatus::where('service_id',$service->id)->update(['calling_status' => 3]);

        //  $service_status->calling_status=3;
        //  $service_status->update();
      }


      if ($request->input('logo', false)) {

          if (!$service->logo || $request->input('logo') !== $service->logo->file_name) {
              if($service->logo)
                $service->logo->delete();
              $service->addMedia(storage_path('tmp/uploads/' . $request->input('logo')))->toMediaCollection('logo');
          }
      } elseif($service->logo) {
          $service->logo->delete();
      }

      return redirect()->route('admin.service.show',['id'=>$id])
                      ->withFlashSuccess(__('alerts.backend.service.updated'));


    }

    public function getcustomerPhone(Request $request){
        $input = $request->all();
        if($request->ajax()){

        $id=$input['id'];
        $customer=Customer::find($id);

        if($customer->count()>0)
          return response()->json(['success'=>$customer->phone]);
        else
          return response()->json(['success'=>'']);
      }
      return response()->json(['success'=>'']);
    }

    public function getServices(Request $request){

      $search = $request->search;

      if($request->input('model')=='shipping'){
           if($search == ''){
               $services = Service::doesnthave('Shipping')->orderby('receiptno','asc')->select('id','receiptno')->limit(5)->get(); //
            }else{
               $services = Service::doesnthave('Shipping')->orderby('receiptno','asc')->select('id','receiptno')->where('receiptno', 'like', '%' .$search . '%')->limit(5)->get();
            }
     } elseif($request->input('model')=='transfer'){
       if($search == ''){
           $services = Service::doesnthave('Transfer')->orderby('receiptno','asc')->select('id','receiptno')->limit(5)->get(); //
        }else{
           $services = Service::doesnthave('Transfer')->orderby('receiptno','asc')->select('id','receiptno')->where('receiptno', 'like', '%' .$search . '%')->limit(5)->get();
        }
     }

      $response = array();
      foreach($services as $service){
         $response[] = array(
              "id"=>$service->id,
              "text"=>$service->receiptno
         );
      }

      echo json_encode($response);
      exit;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $service = Service::findOrFail($id);
/*
      if($service->logo)
        $service->logo->delete();


*/
      if(in_array($service->status,['start','pending','processing','complete','incomplete']))
          return redirect()->back()->withFlashSuccess(__('you can not delete this receipt no. Cos Its already do the transaction.'));

      $service_deleted = Service_Deleted::find($id);

      if($service_deleted!=null){

            $service_deleted->delete();

      }

      $temp = $service->replicate();
      $temp = $service->toArray();
      unset($temp['logo']);unset($temp['media']);

      Service_Deleted::firstOrCreate($temp);

    //  $service->transfer()->delete();
      $service->service_rma()->delete();
      $service->service_status()->delete();
      $service->shipping()->detach();
      $service->transfer()->detach();
      $service->delete();

      return redirect()->route('admin.service.index')
                      ->withFlashSuccess(__('alerts.backend.service.deleted'));

    }
}
