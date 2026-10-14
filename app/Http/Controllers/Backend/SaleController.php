<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleOrder\SaleOrder;
use App\Models\SaleOrder\SaleOrder_detail;
use App\Models\Purchase\Purchases_detail;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer\Customer;
use App\Models\Category\Category;
use App\Models\Item\Item;
use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    if(request()->ajax()){

        $sales=SaleOrder::with('saleorder_detail.items');

        $datatables=Datatables::of($sales);

        $datatables->editColumn('payment',function(SaleOrder $s){
              return ucfirst((($s->payment==1)?'Yes':'No'));

         });

         $datatables->addColumn('action',function(SaleOrder $s){
               return $s->action_buttons;
          });

        $datatables->addColumn('detail',function(SaleOrder $s){
            return json_encode($s->saleorder_detail->all());
        });

        /*

        $datatables.addColumn('item_name', function(Purchase $s){
              return $s->purchase_detail->itemname_name($s->purchase_detail->item_id);
        });
        */
        //rawColumns(['detail'])
        return $datatables->rawColumns(['detail','action'])->make(true);
      }

        //$purchases = Purchase::orderBy('date','desc')->paginate(7);

        return view('backend.sales.index');
    }


    public function report_sale(Request $request){

      $customers=Customer::get()->pluck('fullname','id');

        if($request->ajax()){

          $date_fr='';$date_to='';

           if(!empty(request('date_fr')))
              $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

           if(!empty(request('date_to')))
              $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

          $sales=SaleOrder::with('customer')
            ->join('sale_order_details', 'sale_orders.id', '=', 'sale_order_details.soid')
            ->select(['sale_orders.id', 'sale_orders.date', 'sale_orders.ref', 'sale_orders.customer_id', 'sale_orders.payment', 'sale_orders.paydate','sale_order_details.item_id','sale_order_details.quantity','sale_order_details.saleprice']);


           $sales->when(!empty(request('date_fr')), function($q) use ($date_fr){
             return $q->where('date','>=',$date_fr);
           });

           $sales->when(!empty(request('date_to')), function($q) use ($date_to){
             return $q->where('date','<=',$date_to);
           });

           $sales->when(in_array(request('payment'),array('0','1')), function($q) {
             return $q->where('payment','=',request('payment'));
           });


           $sales->when(!empty(request('customer')), function($q) {
             return $q->where('customer_id','=',request('customer'));
           });

           if(empty($date_fr) && empty($date_to))
              $sales->whereNull('sale_orders.id');

          $datatables=Datatables::of($sales);

          $datatables->editColumn('payment',function(SaleOrder $s){
                return ucfirst((($s->payment==1)?'Yes':'No'));
           });

           // Item is a tiny table (15 real rows) — prefetch once, not per row.
           $itemsById = Item::all()->keyBy('id');
           $datatables->editColumn('item_name',function(SaleOrder $s) use ($itemsById){
             $item = $itemsById->get($s->item_id);
             return $item ? $item->name : 'N/A';
            });

          $datatables->addColumn('customer_name',function(SaleOrder $s){
            return empty($s->customer) ? '' : $s->customer->fullname;
          });

          $datatables->addColumn('amount',function(SaleOrder $s){
            return ($s->quantity * $s->saleprice);
          });

          $datatables->editColumn('date',function(SaleOrder $s){
             return [
                  'display' => $s->date,
                  'timestamp' => $s->dttt
               ];
          });

          return $datatables->make(true);

        }

        return view('backend.sales.report',compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $categorys=Category::orderBy('name','asc')->pluck('name','id');
      $customers=Customer::get()->pluck('fullname','id');
      return view('backend.sales.addnew',compact('customers','categorys'));
    }


    protected function validator(array $data)
    {
        return Validator::make($data, [
            'date' => ['required', 'date_format:"' . config('app.date_format') . '"'],
            'ref' => 'nullable',
            'customer_id' => ['required'],
            'payment' => ['required'],
            'paydate' => 'nullable',
            'note' => ['required','string','max:255'],


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
      // request('field.0') not $request->field[0] — same illegal-offset
      // crash risk found and fixed across ServiceController/PurchaseController.
      $request->merge(['payment' => (empty(request('payment.0'))?0:request('payment.0'))]);
      if(empty($request->payment))
        $request->merge(['paydate' => null]);

      $this->validator($request->all())->validate();

       $saleorder_detail = [];

      $cats = request('category');
      $items = request('item');
      $quantitys = request('quantity');
      $unitprices = request('unitprice');

      $test=false;

      for($i=0;$i < count($items);$i++){
        $data=array('quantity'=>$quantitys[$i],'saleprice'=>$unitprices[$i]);
	       $validator=Validator::make($data, [
            'quantity' => ['required','numeric','gt:0'],
            'saleprice' => ['required','numeric','gt:0'],

        ]);

        if($validator->passes())
              $test=true;
      }

    if(!$test)
	     return redirect()->back()
             ->withFlashDanger('Invalid Quanity or Price.');


      $saleorder = new SaleOrder();
      $saleorder->userid=auth()->user()->id;
      $saleorder->date = request('date');
      $saleorder->ref = request('ref');
      $saleorder->customer_id = request('customer_id');
      $saleorder->note = request('note');
      $saleorder->payment = request('payment');
      $saleorder->paydate = request('paydate');
      $saleorder->save();



      for($i=0;$i < count($items);$i++){
          // Item::find() already returns a resolved model (or null) —
          // chaining ->first() on it is a hard crash whenever the item exists.
          $item = Item::find($items[$i]);
          $cat_id = $item ? $item->category_id : $cats[$i];
          $saleorder_detail[] = new SaleOrder_detail([
            'userid' => auth()->user()->id,
            'category_id' => $cat_id,
            'item_id' => $items[$i],
            'quantity' => $quantitys[$i],
            'saleprice' => $unitprices[$i]
          ]);
      }

      $saleorder->saleorder_detail()->saveMany($saleorder_detail);

      for($i=0;$i < count($items);$i++){
          Item::find($items[$i])->decrement('stockable', $quantitys[$i]);
      }

      return redirect()->route('admin.saleorder.create')
            ->withFlashSuccess(__('alerts.backend.saleorder.created'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $saleorder = SaleOrder::find($id);
      return view('backend.sales.invoice',compact('saleorder'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $categorys=Category::orderBy('name','asc')->pluck('name','id');
      $customers=Customer::get()->pluck('fullname','id');
      $items= Item::orderBy('name','asc')->pluck('name','id');

      $saleorder=SaleOrder::find($id);

      return view('backend.sales.edit',compact('categorys','customers','saleorder','items'));

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
      $request->merge(['payment' => (empty(request('payment.0'))?0:request('payment.0'))]);
      if(empty($request->payment))
        $request->merge(['paydate' => null]);

        $input = $request->all();

        $this->validate($request, [
            'date' => ['required', 'date_format:"' . config('app.date_format') . '"'],
            'ref' => 'nullable',
            'customer_id' => ['required'],
            'payment' => ['required'],
            'paydate'=>'nullable',
            'note'=>'nullable'

        ]);

        $saleorder = SaleOrder::find($id);

        $saleorder_detail = [];
        $cats = request('category');
        $items = request('item');
        $quantitys = request('quantity');
        $unitprices = request('unitprice');

        $test=false;

        for($i=0;$i < count($items);$i++){
          $data=array('quantity'=>$quantitys[$i],'saleprice'=>$unitprices[$i]);
           $validator=Validator::make($data, [
              'quantity' => ['required','numeric','gt:0'],
              'saleprice' => ['required','numeric','gt:0'],

          ]);

          if($validator->passes())
                $test=true;
        }

      if(!$test)
         return redirect()->back()
               ->withFlashDanger('Sale Detail is invalid Quanity or Price.');

        for($i=0;$i < count($items);$i++){
            // Two bugs here: Item::find() already returns a resolved model
            // (or null), so ->first() on it is a hard crash whenever the
            // item exists; and this pushed into $puchase_detail, an
            // undeclared variable never used below (copy-paste leftover
            // from PurchaseController) — $saleorder_detail (declared above,
            // and what saveMany() is actually called with further down)
            // was silently never populated.
            $item = Item::find($items[$i]);
            $cat_id = $item ? $item->category_id : $cats[$i];
            $saleorder_detail[] = new SaleOrder_detail([
              'userid' => auth()->user()->id,
              'category_id' => $cat_id,
              'item_id' => $items[$i],
              'quantity' => $quantitys[$i],
              'saleprice' => $unitprices[$i]
            ]);
        }

        $saleorder->saleorder_detail()->delete();
        $saleorder->saleorder_detail()->saveMany($saleorder_detail);

        $saleorder->update($input);

        for($i=0;$i < count($items);$i++){
            $total_purchase = Purchases_detail::where('item_id',$items[$i])->sum('quantity');
            $total_sales = SaleOrder_detail::where('item_id',$items[$i])->sum('quantity');
            Item::where('id',$items[$i])->update(['stockable' => ($total_purchase-$total_sales)]);
        }

        return redirect()->route('admin.saleorder.index')
                      ->withFlashSuccess(__('alerts.backend.saleorder.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $saleorder = SaleOrder::findOrFail($id);

      if($saleorder->payment==1)
          return redirect()->route('admin.saleorder.index')
                      ->withFlashDanger(__('alerts.backend.saleorder.warning_delete1'));

      for($i=0;$i < count($saleorder->saleorder_detail);$i++){
          $stock_able = Item::where('id',$saleorder->saleorder_detail[$i]->item_id)->first()->stockable;
          $total_sale = SaleOrder_detail::where('item_id',$saleorder->saleorder_detail[$i]->item_id)->first()->quantity;

          Item::where('id',$saleorder->saleorder_detail[$i]->item_id)->update(['stockable' => ($stock_able + $total_sale)]);

      }

      $saleorder->saleorder_detail()->delete();
      $saleorder->delete();

      return redirect()->route('admin.saleorder.index')
                      ->withFlashSuccess(__('alerts.backend.saleorder.deleted'));

    }
}
