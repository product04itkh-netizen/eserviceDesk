<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category\Category;
use App\Models\Item\Item;
use App\Models\Purchase\Purchases_detail;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $categorys = Category::orderBy('id','asc')->paginate(15);
      return view('backend.category.index',compact('categorys'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
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
        return view('backend.category.addnew');
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

      $category = new Category();
      $category->name = request('name');
      $category->userid=auth()->user()->id;
      $category->description = request('description');
      $category->save();

      return redirect()->route('admin.category.create')->withFlashSuccess(__('alerts.backend.category.created'));
    }

    public function fetch(Request $request){
        $value = $request->get('value');

        if(empty($value))
          $items=Item::orderBy('name','asc')->get();
        else
          $items=Item::where('category_id',$value)->get();

        $output= '<option value="">Select Item</option>';
        foreach($items as $item){
          $output .= '<option value="' . $item->id . '">'. $item->name . '</option>';
        }

        echo $output;

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
      $category = Category::find($id);
      return view('backend.category.edit',compact('category'));

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
          'name' => 'required|unique:categories,name,'.$id,
      ]);

        $category = Category::find($id);
        $category->update($input);

        return redirect()->route('admin.category.index')
                        ->withFlashSuccess(__('alerts.backend.category.updated'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

      $item = Item::where('category_id',$id);

      if($item->count()>0)
        return redirect()->route('admin.category.index')
                      ->withFlashDanger(__('alerts.backend.category.warning_delete1'));

      $purchase = Purchases_detail::where('category_id',$id);

      if($purchase->count()>0)
        return redirect()->route('admin.category.index')
                      ->withFlashDanger(__('alerts.backend.category.warning_delete2'));

      $category = Category::findOrFail($id);
      $category->delete();

      return redirect()->route('admin.category.index')
                      ->withFlashSuccess(__('alerts.backend.category.deleted'));
    }
}
