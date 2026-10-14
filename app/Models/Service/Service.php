<?php
namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Traits\Attribute\ServiceAttribute;
use App\Models\Auth\Traits\Method\ServiceMethod;

use App\Models\Problem\Problem;
use App\Models\Vendor\Vendor;
use App\Models\Customer\Customer;
use App\Models\Service\ServiceStatus;
use App\Models\Purchase\Purchase;
use App\Models\Shipping\Shipping;
use App\Models\Transfer\Transfer;
use App\Models\Service\Service_rma;
use App\Models\Brand\Brand;
use App\Models\Auth\User;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use App\Models\Rmacode\Rmacode;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;



use Carbon\Carbon;


class Service extends Model implements HasMedia, Auditable
{
  //const UPDATED_AT = null;
  use ServiceAttribute,
      ServiceMethod,
      HasRoles,HasMediaTrait,
      AuditableTrait;

  protected $appends = [
      'logo',
  ];

  protected $fillable = [
      'receiptno', 'date','buyingdate', 'serialno','model','description',
      'chargetype','chargeamount','labor_cost','payment','paydate','transfer_hq','transfer_hq_date','customerid','assignto','phone','warrenty',
      'solution','ordered','ordered_comment','ordered_rma','ordered_costing','ordered_supplyer',
      'status','problemid','rmano','rmadate','rmacode','delivered'
  ];

  public function registerMediaConversions(Media $media = null)
  {
      $this->addMediaConversion('thumb')->width(50)->height(50);
  }

  public function getLogoAttribute()
  {
      $file = $this->getMedia('logo')->last();

      if ($file) {
          $file->url       = $file->getUrl();
          $file->thumbnail = $file->getUrl('thumb');
      }

      return $file;
  }

  public function service_status()
  {
    return $this->hasMany(ServiceStatus::class);
  }

  public function service_rma()
  {
    return $this->hasMany(Service_rma::class);
  }

  public function problem()
  {
    return $this->belongsToMany(Problem::class);
  }

  public function customer()
  {
    return $this->hasOne(Customer::class,'id','customerid');
  }

  public function rmacodes()
  {
    return $this->hasOne(Rmacode::class,'id','rmacode');
  }

  public function shipping()
  {
    return $this->belongsToMany(Shipping::class,'service_shipping');
  }

  public function transfer()
  {
    return $this->belongsToMany(Transfer::class,'service_transfer');
  }


  public function brand()
  {
    return $this->belongsToMany(Brand::class,'service_brand');
  }

  public function rma(){
    return $this->hasMany(Purchase::class);
  }

  public function users()
    {
    	return $this->belongsTo(User::class,'assignto','id');
    }

  public function enginner()
  {
    return $this->hasOne(User::class,'id','assignto');
  }
  /*
  public function current_problem($service_id){
    $services=Service::find($service_id)->first();

    return $services->problem()->pluck('problem')->implode(' ');
  }
  */

  public function customer_name($cust_id){
    $customer=Customer::where('id',$cust_id);
    if($customer->count()<=0)
      return 'N/A';

    return ucfirst($customer->first()->first_name) . ' ' . ucfirst($customer->first()->last_name);
  }

  public function enginner_name($enginner_id){
    $enginner=User::where('id',$enginner_id);
    if($enginner->count()<=0)
        return 'N/A';

    return ucfirst($enginner->first()->first_name) . ' ' . ucfirst($enginner->first()->last_name);
  }

  public function customer_phone($cust_id){
    $customer=Customer::where('id',$cust_id)->first();
    return ($customer->phone);
  }

  public function receiver($receipt_id){
    $user=User::where('id',$receipt_id)->first();
    if(empty($user))
      return '';
    return ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);
  }

  public function supplyer_name($supplyer_id){
    $vendor=Vendor::where('id',$supplyer_id);
    if($vendor->count()<=0)
      return '';
    return ucfirst($vendor->first()->companyname);
  }

  public function vendor_name($vendor_id){
    $vendor=Vendor::where('id',$vendor_id);
    if($vendor->count()<=0)
      return '';
    return ucfirst($vendor->first()->companyname);
  }

  public function getDttAttribute(){
    return $this->attributes['date'];
  }



  public function getDate1Attribute(){
    return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['date'])->timestamp;
  }

  public function getDate2Attribute(){
    return $this->attributes['updated_at'];
  }

  public function getDate3Attribute(){
    if(empty($this->attributes['paydate']))
       return null;

    return Carbon::createFromFormat('Y-m-d', $this->attributes['paydate'])->timestamp;
  }



  //public function getOriDate

  public function setDateAttribute($input)
  {
      if(empty($input))
        $this->attributes['date']=null;
      else{
        //$input = $input;

        $this->attributes['date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d H:i:s');

      }
  }

  public function setRmadateAttribute($input)
  {
    if(empty($input))
      $this->attributes['rmadate']=null;
    else
      $this->attributes['rmadate'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }

  public function getRmadateAttribute($input)
  {
    if(empty($input) || ($input=='0000-00-00'))
      return null;

      return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
  }

  public function setPaydateAttribute($input)
  {
    if(empty($input))
      $this->attributes['paydate']=null;
    else
      $this->attributes['paydate'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }

/*
  public function setReturndateAttribute($input)
  {
    if(empty($input))
      $this->attributes['returndate']=null;
    else
      $this->attributes['returndate'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }

  public function setTransferdateAttribute($input)
  {
    if(empty($input))
      $this->attributes['transferdate']=null;
    else
      $this->attributes['transferdate'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }



public function getTransferdateAttribute($input)
{
  if(empty($input))
    return null;
    return Carbon::createFromFormat('Y-m-d', $input)
      ->format(config('app.date_format'));
}
*/
/*
public function getReturndateAttribute($input)
{
  if(empty($input))
    return null;

    return Carbon::createFromFormat('Y-m-d', $input)
      ->format(config('app.date_format'));
}
*/
  public function setBuyingDateAttribute($input)
  {
    if(empty($input))
        $this->attributes['buyingdate']=null;
    else
      $this->attributes['buyingdate'] =
        Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
  }


/*
  public function getTransferdateAttribute($input)
  {
    if(empty($input))
      return null;

      return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
  }
*/
  public function getDateAttribute($input)
  {

      return Carbon::createFromFormat('Y-m-d H:i:s', $input)
        ->format(config('app.date_format'));
  }

  public function getTimeAttribute($input){
    return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['date'])
      ->format(config('app.time_format'));
  }

  public function getPaydateAttribute($input)
  {
    if(empty($input))
      return null;

      return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
  }

  public function getBuyingDateAttribute($input)
  {
    if(empty($input))
        return null;
    return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
  }


  public function getReturndate1Attribute($input)
  {
      if(empty($this->attributes['returndate']))
        return null;

        return Carbon::createFromFormat('Y-m-d', $this->attributes['returndate'])
            ->format(config('app.date_format'));
  }

  public function getTransferdate1Attribute($input)
  {
      if(empty($this->attributes['transferdate']))
        return null;

        return Carbon::createFromFormat('Y-m-d', $this->attributes['transferdate'])
            ->format(config('app.date_format'));
  }

  public function getCleardate1Attribute($input)
  {
      if(empty($this->attributes['credit_note_clear']) || ($this->attributes['credit_note_clear']=='0000-00-00'))
        return null;

        return Carbon::createFromFormat('Y-m-d', $this->attributes['credit_note_clear'])
            ->format(config('app.date_format'));
  }

}
