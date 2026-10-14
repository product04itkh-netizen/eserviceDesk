<?php

//namespace App\Http\Controllers;
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Vendor\Vendor;
use App\Models\Service\Service;
use App\Models\Purchase\Purchase;
use Illuminate\Http\Request;
use App\Http\Requests\Backend\Auth\Service\ManageVendorRequest;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ManageVendorRequest $request)
    {
      $vendors = Vendor::orderBy('companyname','asc')->paginate(15);

      return view('backend.vendors.index',compact('vendors'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ManageVendorRequest $request)
    {
        return view('backend.vendors.addnew');
    }


    protected function validator(array $data)
    {
        return Validator::make($data, [
            'companyname' => ['required', 'string', 'max:255'],
            'contactname' => ['required', 'string', 'max:255'],
            'contacttitle' => ['required', 'string', 'max:255'],
            'emails' => ['required', 'string', 'email', 'max:255', 'unique:vendors'],
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,ManageVendorRequest $request1)
    {
      $this->validator($request->all())->validate();

      $vendor = new Vendor();
      $vendor->companyname = request('companyname');
      $vendor->contactname = request('contactname');
      $vendor->contacttitle = request('contacttitle');
      $vendor->emails = request('emails');
      $vendor->status = request('status');
      $vendor->phone = request('phone');
      $vendor->address = request('address');
      $vendor->save();

      return redirect()->route('admin.vendor.index')->withFlashSuccess(__('alerts.backend.vendor.created'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,ManageVendorRequest $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,ManageVendorRequest $request)
    {
      $vendor = Vendor::find($id);
      return view('backend.vendors.edit',compact('vendor'));
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
      $this->validate($request, [
          'companyname' => 'required',
          'contactname'=>'required',
          'contacttitle'=>'required',
          'emails' => 'required|email|unique:vendors,emails,'.$id,
          'status' => 'required',
      ]);

        $vendor = Vendor::find($id);
        $vendor->update($input);

        return redirect()->route('admin.vendor.index')
                        ->withFlashSuccess(__('alerts.backend.vendor.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      // services.destination was never a real column in production (confirmed
      // against a live schema dump) and nothing ever set it — this check could
      // never actually block anything. Removed rather than kept as dead code
      // referencing a column that doesn't exist.
      $purchase = Purchase::where('vendor_id',$id);

      if($purchase->count()>0)
        return redirect()->route('admin.vendor.index')
                      ->withFlashDanger(__('alerts.backend.vendor.warning_delete2'));

      $vendor = Vendor::findOrFail($id);
      $vendor->delete();



      return redirect()->route('admin.vendor.index')
                      ->withFlashSuccess(__('alerts.backend.vendor.deleted'));

    }
}
