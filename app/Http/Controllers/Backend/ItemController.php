<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category\Category;
use App\Models\Item\Item;
use App\Models\Purchase\Purchases_detail;

class ItemController extends Controller
{


    public function fetchstock(Request $request){
      if($request->ajax()){
      $value = $request->get('value');

      $item=Item::find($value);

      echo $item ? $item->stockable : 0;
      die();
    }
      echo 0;
    }

    public function stock()
    {
      $items = Item::orderBy('name','asc')->paginate(15);



      return view('backend.items.stock',compact('items'));

    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $items = Item::orderBy('name','asc')->paginate(7);



      return view('backend.items.index',compact('items'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorys = Category::all()->pluck('name', 'id');
        return view('backend.items.addnew',compact('categorys'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:items'],
            'description' => 'required',
            'category_id' => 'required'

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
      $this->validator($request->all())->validate();

      $item = new Item();
      $item->userid=auth()->user()->id;
      $item->name = request('name');
      $item->brand_id=0;
      $item->description = request('description');
      $item->category_id = request('category_id');
      $item->saleprice = request('saleprice');
      $item->stockable = 0;
      $item->stockreorder = 5;
      $item->save();

      return redirect()->route('admin.item.create')->withFlashSuccess(__('alerts.backend.item.created'));
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
      $categorys = Category::all()->pluck('name', 'id');
      $item = Item::find($id);
      return view('backend.items.edit',compact('item','categorys'));
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
          'name' => 'required|unique:items,name,'.$id,
          'saleprice'=>'required|numeric',
          'category_id'=>'required|numeric',
      ]);

        $item = Item::find($id);
        $item->update($input);

        return redirect()->route('admin.item.index')
                        ->withFlashSuccess(__('alerts.backend.item.updated'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $purchase = Purchases_detail::where('item_id',$id);

      if($purchase->count()>0)
        return redirect()->route('admin.item.index')
                      ->withFlashDanger(__('alerts.backend.item.warning_delete1'));

      $item = Item::findOrFail($id);
      $item->delete();

      return redirect()->route('admin.item.index')
                      ->withFlashSuccess(__('alerts.backend.item.deleted'));
    }
}
