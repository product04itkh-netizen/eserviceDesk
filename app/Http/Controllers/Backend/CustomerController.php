<?php
namespace App\Http\Controllers\Backend;

/*namespace App\Http\Controllers;*/
use App\Models\Auth\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer\Customer;
use App\Models\Service\Service;
use App\Http\Requests\Backend\Auth\Service\ManageCustomerRequest;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(ManageCustomerRequest $request)
    {

      $customers = Customer::orderBy('first_name','asc')->paginate(15);

      return view('backend.customers.index',compact('customers'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => 'required|string|max:255',
            'emails' => ['required', 'string', 'email', 'max:255', 'unique:customers'],
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);


    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ManageCustomerRequest $request)
    {
      return view('backend.customers.addnew');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,ManageCustomerRequest $request1)
    {
        $this->validator($request->all())->validate();

        $customer = new Customer();
        $customer->first_name = request('first_name');
        $customer->last_name = request('last_name');
        $customer->emails = request('emails');
        $customer->status = request('status');
        $customer->phone = (empty(request('phone'))?"":request('phone'));
        $customer->address = request('address');
        $customer->save();

        return redirect()->route('admin.customer.index')->withFlashSuccess(__('alerts.backend.customer.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,ManageCustomerRequest $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,ManageCustomerRequest $request)
    {
        $customer = Customer::findOrFail($id);

        return view('backend.customers.edit',compact('customer'));
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
          'first_name' => 'required',
          'last_name'=>'required',
          'emails' => 'required|email|unique:customers,emails,'.$id,
          'status' => 'required',
      ]);

        $customer = Customer::find($id);
        $customer->update($input);

        return redirect()->route('admin.customer.index')
                        ->withFlashSuccess(__('alerts.backend.customer.updated'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $services = Service::where('customerid',$id);

        if($services->count()>0)
          return redirect()->route('admin.customer.index')
                        ->withFlashDanger(__('alerts.backend.customer.warning_delete'));

        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customer.index')
                        ->withFlashSuccess(__('alerts.backend.customer.deleted'));


    }
}
