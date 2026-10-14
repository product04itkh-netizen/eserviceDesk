<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Brand\Brand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
     {
       $brands = Brand::orderBy('name','asc')->paginate(15);
       return view('backend.brand.index',compact('brands'));
     }


     protected function validator(array $data)
     {
         return Validator::make($data, [
             'name' => ['required', 'string', 'max:255', 'unique:brands'],
             'decription' => 'nullable',
         ]);


     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.brand.addnew');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validator($request->all())->validate();

      $brand = new Brand();
      $brand->name = request('name');
      $brand->userid=auth()->user()->id;
      $brand->description = request('description');
      $brand->save();

      return redirect()->route('admin.brand.create')->withFlashSuccess(__('alerts.backend.brand.created'));
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
      $brand = Brand::find($id);
      return view('backend.brand.edit',compact('brand'));

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
          'description'=>'required',
          'name' => 'required|unique:brands,name,'.$id,
      ]);

        $brand = Brand::find($id);
        $brand->update($input);

        return redirect()->route('admin.brand.index')
                        ->withFlashSuccess(__('alerts.backend.brand.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $services = Brand::find($id)->service();

      if($services->count()>0)
        return redirect()->route('admin.brand.index')
                      ->withFlashDanger(__('alerts.backend.brand.warning_delete'));
      $brand = Brand::findOrFail($id);
      $brand->delete();

      return redirect()->route('admin.brand.index')
                      ->withFlashSuccess(__('alerts.backend.brand.deleted'));
    }
}
