<?php
namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\Traits\Attribute\ServiceAttribute;
use App\Models\Auth\Traits\Method\ServiceMethod;

use App\Models\Problem\Problem;
use App\Models\Vendor\Vendor;
use App\Models\Customer\Customer;
use App\Models\Auth\User;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;




use Carbon\Carbon;


class Service_Deleted extends Model
{
  //const UPDATED_AT = null;

 protected $table = "services_deleted";


  protected $fillable = [
      'id','receiptno', 'date','userid','buyingdate', 'serialno','model','description',
      'chargetype','chargeamount','payment','paydate','customerid','assignto','phone','warrenty',
      'destination','solution','ordered','ordered_comment','status','problemid','returndate','transferdate'
  ];

  public function problem()
  {
    return $this->belongsToMany(Problem::class);
  }

  public function customer()
  {
    return $this->hasOne(Customer::class,'id','customerid');
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
    $user=User::where('id',$receipt_id);
    return ucfirst($user->first()->first_name) . ' ' . ucfirst($user->first()->last_name);
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
    return Carbon::createFromFormat('Y-m-d', $this->attributes['date'])->timestamp;
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
      $this->attributes['date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
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
*/

/*
  public function getReturndateAttribute($input)
  {
    if(empty($input))
      return null;

      return Carbon::createFromFormat('Y-m-d', $input)
        ->format(config('app.date_format'));
  }


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

}
