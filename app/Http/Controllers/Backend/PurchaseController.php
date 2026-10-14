<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\Purchases_detail;
use App\Models\SaleOrder\SaleOrder_detail;
use Illuminate\Support\Facades\Validator;
use App\Models\Vendor\Vendor;
use App\Models\Service\Service;
use App\Models\Category\Category;
use App\Models\Item\Item;
use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $input)
    {

      if(request()->ajax()){

        $purchases=Purchase::with(['service','purchase_detail.items']);

        $datatables=Datatables::of($purchases);

        $datatables->editColumn('payment',function(Purchase $s){
              return ucfirst((($s->payment==1)?'Yes':'No'));

         });

         $datatables->addColumn('action',function(Purchase $s){
               return $s->action_buttons;
          });

        $datatables->addColumn('receiptno',function(Purchase $s){
          if($s->service_id==0 || empty($s->service))
              return '';

            return "<a href='". route("admin.service.show",["id"=>$s->service_id]) . "'>" . $s->service->receiptno . "</a>";
        });

        $datatables->addColumn('detail',function(Purchase $s){
              return json_encode($s->purchase_detail->all());
        });

        /*

        $datatables.addColumn('item_name', function(Purchase $s){
              return $s->purchase_detail->itemname_name($s->purchase_detail->item_id);
        });
        */
        //rawColumns(['detail'])
        return $datatables->rawColumns(['detail','receiptno','action'])->make(true);
      }

        //$purchases = Purchase::orderBy('date','desc')->paginate(7);

        return view('backend.purchases.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $services=Service::orderBy('receiptno','asc')->pluck('receiptno', 'id');
      $categorys=Category::orderBy('name','asc')->pluck('name','id');
      $vendors=Vendor::orderBy('companyname','asc')->pluck('companyname','id');
      $items=Item::orderBy('name','asc')->pluck('name','id');
      return view('backend.purchases.addnew',compact('vendors','categorys','services','items'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'date' => ['required', 'date_format:"' . config('app.date_format') . '"'],
            'rma' => ['required', 'string', 'max:255'],
            'vendor_id' => ['required'],
            'service_id' => ['required'],
            'payment' => ['required'],
            'paydate' => 'nullable',
            'note' => ['required','string','max:255'],


        ]);


    }


    public function report_purchase(Request $request){

      $vendor=Vendor::all()->pluck('companyname', 'id');

        if($request->ajax()){

          $date_fr='';$date_to='';

           if(!empty(request('date_fr')))
              $date_fr=Carbon::createFromFormat(config('app.date_format'), request('date_fr'))->format('Y-m-d');

           if(!empty(request('date_to')))
              $date_to=Carbon::createFromFormat(config('app.date_format'), request('date_to'))->format('Y-m-d');

          $purchases=Purchase::with('vendor')
            ->join('purchases_details', 'purchases.id', '=', 'purchases_details.purchase_id')
            ->select(['purchases.id', 'purchases.date', 'purchases.rma', 'purchases.vendor_id', 'purchases.payment', 'purchases.paydate','purchases_details.item_id','purchases_details.quantity','purchases_details.purchaseprice']);

           $purchases->when(!empty(request('date_fr')), function($q) use ($date_fr){
             return $q->where('date','>=',$date_fr);
           });

           $purchases->when(!empty(request('date_to')), function($q) use ($date_to){
             return $q->where('date','<=',$date_to);
           });

           $purchases->when(in_array(request('payment'),array('0','1')), function($q) {
             return $q->where('payment','=',request('payment'));
           });


           $purchases->when(!empty(request('vendor')), function($q) {
             return $q->where('vendor_id','=',request('vendor'));
           });

           if(empty($date_fr) && empty($date_to))
              $purchases->whereNull('purchases.id');

          $datatables=Datatables::of($purchases);

          $datatables->editColumn('payment',function(Purchase $s){
                return ucfirst((($s->payment==1)?'Yes':'No'));
           });

           // Item is a tiny table (15 real rows) — prefetch once, not per row.
           $itemsById = Item::all()->keyBy('id');
           $datatables->addColumn('item_name',function(Purchase $s) use ($itemsById){
             $item = $itemsById->get($s->item_id);
             return $item ? $item->name : 'N/A';
            });

          $datatables->addColumn('supplyer_name',function(Purchase $s){
            return empty($s->vendor) ? '' : $s->vendor->companyname;
          });

          $datatables->addColumn('amount',function(Purchase $s){
            return ($s->quantity * $s->purchaseprice);
          });

          $datatables->editColumn('date',function(Purchase $s){
             return [
                  'display' => $s->date,
                  'timestamp' => $s->dttt
               ];
          });


          /*

          $datatables.addColumn('item_name', function(Purchase $s){
                return $s->purchase_detail->itemname_name($s->purchase_detail->item_id);
          });
          */
          //rawColumns(['detail'])
          //rawColumns(['detail','action'])->
          return $datatables->make(true);

        }

        return view('backend.purchases.report',compact('vendor'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      // request('field.0') not $request->field[0] — the latter throws an
      // "illegal offset" warning (fatal here, per this app's error handler)
      // once merge() has already converted the field to a scalar, or
      // whenever it's absent from the request entirely.
      $request->merge(['payment' => (empty(request('payment.0'))?0:request('payment.0'))]);
      if(empty($request->payment))
        $request->merge(['paydate' => null]);

      $this->validator($request->all())->validate();

      $puchase_detail = [];
      $cats = request('category');
      $items = request('item');
      $quantitys = request('quantity');
      $purchaseprices = request('purchaseprice');

      $test=false;

      for($i=0;$i < count($items);$i++){
        $data=array('quantity'=>$quantitys[$i],'purchaseprice'=>$purchaseprices[$i]);

         $validator=Validator::make($data, [
            'quantity' => ['required','numeric','gt:0'],
            //'purchaseprice' => ['required','numeric','gt:0'],
        ]);

        if($validator->passes())
              $test=true;
      }

    if(!$test)
       return redirect()->back()
             ->withFlashDanger('Invalid Quanity or Price.');


      $purchase = new Purchase();
      $purchase->userid=auth()->user()->id;
      $purchase->date = request('date');
      $purchase->rma = request('rma');
      $purchase->vendor_id = request('vendor_id');
      $purchase->service_id = request('service_id');
      $purchase->note = request('note');
      $purchase->payment = request('payment');
      $purchase->paydate = request('date');
      $purchase->save();

      for($i=0;$i < count($items);$i++){
        // $items[$i] is now an item_id from a catalog select, matching
        // Edit Purchase (the path that actually works) — see
        // Purchases_detail's $fillable comment and the schema comment on
        // purchases_details.item_id for why the old free-text field is gone.
        $item = Item::find($items[$i]);
        $cat_id = $item ? $item->category_id : $cats[$i];

	$puchase_detail[] = new Purchases_detail([
            'userid' => auth()->user()->id,
            'category_id' => $cat_id,
            'item_id' => $items[$i],
            'quantity' => $quantitys[$i],
            'purchaseprice' => $purchaseprices[$i]
          ]);
      }

      $purchase->purchase_detail()->saveMany($puchase_detail);

    //  for($i=0;$i < count($items);$i++){
      //    Item::find($items[$i])->increment('stockable', $quantitys[$i]);
      //}

      return redirect()->route('admin.purchase.create')
            ->withFlashSuccess(__('alerts.backend.purchase.created'));
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
      $services=Service::orderBy('receiptno','asc')->pluck('receiptno', 'id');
      $categorys=Category::orderBy('name','asc')->pluck('name','id');
      $vendors=Vendor::orderBy('companyname','asc')->pluck('companyname','id');
      $items= Item::orderBy('name','asc')->pluck('name','id');

      $purchase=Purchase::find($id);

      return view('backend.purchases.edit',compact('categorys','vendors','purchase','items','services'));
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
            'rma' => ['required', 'string', 'max:255'],
            'vendor_id' => ['required'],
            'service_id' => ['required'],
            'payment' => ['required'],
            'paydate'=>'nullable',
            'note'=>'nullable'

        ]);

        $purchase = Purchase::find($id);

        $puchase_detail = [];
        $cats = request('category');
        $items = request('item');
        $quantitys = request('quantity');
        $unitprices = request('unitprice');

        $test=false;

        for($i=0;$i < count($items);$i++){
          $data=array('quantity'=>$quantitys[$i],'purchaseprice'=>$unitprices[$i]);
           $validator=Validator::make($data, [
              'quantity' => ['required','numeric','gt:0'],
              'purchaseprice' => ['required','numeric','gt:0'],

          ]);

          if($validator->passes())
                $test=true;
        }

      if(!$test)
         return redirect()->back()
               ->withFlashDanger('Invalid Quanity or Price.');

        for($i=0;$i < count($items);$i++){
            // Item::find() already returns a resolved model (or null) —
            // chaining ->first() on it is a hard crash whenever the item
            // exists. This form (unlike New Purchase's free-text entry)
            // is a real catalog select, so item_id is the right field here.
            $item = Item::find($items[$i]);
            $cat_id = $item ? $item->category_id : $cats[$i];
            $puchase_detail[] = new Purchases_detail([
              'userid' => auth()->user()->id,
              'category_id' => $cat_id,
              'item_id' => $items[$i],
              'quantity' => $quantitys[$i],
              'purchaseprice' => $unitprices[$i]
            ]);
        }

        $purchase->purchase_detail()->delete();
        $purchase->purchase_detail()->saveMany($puchase_detail);

        $purchase->update($input);

        for($i=0;$i < count($items);$i++){
          $total_purchase = Purchases_detail::where('item_id',$items[$i])->sum('quantity');
          $total_sales = SaleOrder_detail::where('item_id',$items[$i])->sum('quantity');
          Item::where('id',$items[$i])->update(['stockable' => ($total_purchase-$total_sales)]);
      }
        if(request('service')>0)
          return redirect()->route('admin.service.edit',['id'=>request('service')])
                    ->withFlashSuccess(__('alerts.backend.purchase.updated'));
        else
          return redirect()->route('admin.purchase.index')
                      ->withFlashSuccess(__('alerts.backend.purchase.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $purchase = Purchase::findOrFail($id);

      for($i=0;$i < count($purchase->purchase_detail);$i++){
          // item_id is null for lines entered via New Purchase's free-text
          // item field (only the catalog-select Edit form populates it) —
          // nothing to adjust on the Item catalog for those.
          $detail_item_id = $purchase->purchase_detail[$i]->item_id;
          if(empty($detail_item_id))
              continue;

          $item = Item::where('id',$detail_item_id)->first();
          if(!$item)
              continue;

          $total_purchase = Purchases_detail::where('item_id',$detail_item_id)->first()->quantity;

          Item::where('id',$detail_item_id)->update(['stockable' => ($item->stockable - $total_purchase)]);

      }


      $purchase->purchase_detail()->delete();
      $purchase->delete();



      return redirect()->route('admin.purchase.index')
                      ->withFlashSuccess(__('alerts.backend.purchase.deleted'));
    }
}
