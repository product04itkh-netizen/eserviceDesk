<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rmacode\Rmacode;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class RmacodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rmacodes = Rmacode::orderBy('code','asc')->paginate(15);
        return view('backend.rmacode.index',compact('rmacodes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.rmacode.addnew');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     protected function validator(array $data)
     {
         return Validator::make($data, [
             'code' => ['required', 'string', 'max:255', 'unique:rmacodes'],

         ]);


     }

    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $rmacode = new Rmacode();
        $rmacode->code = request('code');
        $rmacode->userid=auth()->user()->id;

        $rmacode->save();

        return redirect()->route('admin.rmacode.create')->withFlashSuccess(__('alerts.backend.rmacode.created'));
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
        $rmacode = Rmacode::find($id);
        return view('backend.rmacode.edit',compact('rmacode'));
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
        $input = $request->all();

    //    dd($input);
        $this->validate($request, [

            'code' => 'required|unique:rmacodes,code,'.$id,
        ]);

          $rmacode = Rmacode::find($id);
          $rmacode->update($input);

          return redirect()->route('admin.rmacode.index')
                          ->withFlashSuccess(__('alerts.backend.rmacode.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $services = Rmacode::find($id)->service();

        if($services->count()>0)
          return redirect()->route('admin.rmacode.index')
                        ->withFlashDanger(__('alerts.backend.rmacode.warning_delete1'));
        $rmacode = Rmacode::findOrFail($id);
        $rmacode->delete();

        return redirect()->route('admin.rmacode.index')
                        ->withFlashSuccess(__('alerts.backend.rmacode.deleted'));
    }
}
