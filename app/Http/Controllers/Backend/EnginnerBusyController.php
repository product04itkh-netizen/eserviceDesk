<?php

namespace App\Http\Controllers\Backend;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use Carbon\Carbon;
use DataTables;
use App\Models\Service\Enginner_busy;

class EnginnerBusyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

      if(request()->ajax()){

         $chks=request('limit');

        //  $chks=array(9=>10,10=>11);

         if(!empty($chks)){
         foreach($chks as $k=>$chk){
           $user= User::with('enginner_busy')->find($k);

           if($user->enginner_busy){
             $user->enginner_busy()->update(['busy_limit' => $chk,'userid'=>auth()->user()->id]);
           }else{
             $user->enginner_busy()->create(['busy_limit' => $chk,'userid'=>auth()->user()->id,'user_id'=>$k]);
           }


           //$user->save();
         }
        }

        $enginner=User::with('enginner_busy')->whereHas('roles', function($q){
          $q->where('name', 'service-engineer')->where('active',1); });


      $datatables=Datatables::of($enginner);

      $datatables->addColumn('status',function( $enginner){
          return (($enginner->active)?'Active':'Inactive');
      });

      $datatables->addColumn('action',function($enginner){


           return '<input type="number" required="true" uid="' . $enginner->id . '" class="form-control" name="chk[]" value="' . (empty($enginner->enginner_busy)?'0':$enginner->enginner_busy->busy_limit) . '">';
            //return ucfirst((($service->payment==1)?'Yes':'No'));
       });

       $datatables->rawColumns(['action']);

       return $datatables->make(true);;
    }
        return view('backend.engineerbusy.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
