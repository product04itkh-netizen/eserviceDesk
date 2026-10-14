<?php

use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use App\Models\Customer\Customer;
use App\Models\Problem\Problem;
use App\Models\Problem\Groupproblem;
use App\Models\Category\Category;
use App\Models\Brand\Brand;
use App\Models\Item\Item;
use App\Models\Vendor\Vendor;
use App\Models\Rmacode\Rmacode;
use App\Models\Service\Enginner_busy;
use App\Models\Service\Service;
use App\Models\Service\ServiceStatus;
use App\Models\Service\Service_rma;

/**
 * Synthetic test data — NOT wired into DatabaseSeeder, run on demand:
 *   php artisan db:seed --class=TestDataSeeder
 *
 * Exists purely to exercise the app's actual workflows (ticket edit, status
 * transitions, payment/delivery gate, reports) while the real server is
 * unreachable and full historical data is intentionally not being migrated
 * yet. Disposable — safe to wipe and re-run.
 */
class TestDataSeeder extends Seeder
{
    public function run()
    {
        // --- Staff: one engineer, one receptionist, so role-based behavior is testable ---
        $engineer = User::firstOrCreate(
            ['email' => 'engineer@test.com'],
            ['first_name' => 'John', 'last_name' => 'Engineer', 'password' => 'secret',
             'confirmation_code' => md5(uniqid(mt_rand(), true)), 'confirmed' => true]
        );
        $engineer->assignRole(config('access.users.service_enginner_role'));

        $receptionist = User::firstOrCreate(
            ['email' => 'reception@test.com'],
            ['first_name' => 'Sarah', 'last_name' => 'Reception', 'password' => 'secret',
             'confirmation_code' => md5(uniqid(mt_rand(), true)), 'confirmed' => true]
        );
        $receptionist->assignRole(config('access.users.service_receptionist_role'));

        Enginner_busy::firstOrCreate(
            ['user_id' => $engineer->id],
            ['userid' => 1, 'busy_limit' => 5]
        );

        // --- Master data ---
        $customers = [
            Customer::firstOrCreate(['emails' => 'sokha.customer@test.com'], ['first_name' => 'Sokha', 'last_name' => 'Chan', 'phone' => '012345001', 'status' => 1, 'address' => 'Phnom Penh']),
            Customer::firstOrCreate(['emails' => 'dara.customer@test.com'], ['first_name' => 'Dara', 'last_name' => 'Kim', 'phone' => '012345002', 'status' => 1, 'address' => 'Phnom Penh']),
            Customer::firstOrCreate(['emails' => 'general.user@test.com'], ['first_name' => 'General', 'last_name' => 'User', 'phone' => '012345003', 'status' => 1, 'address' => '']),
        ];

        $problems = [
            Problem::firstOrCreate(['problem' => 'NO POWER'], ['description' => 'Device does not power on']),
            Problem::firstOrCreate(['problem' => 'CHECK SCREEN'], ['description' => 'Display issue']),
            Problem::firstOrCreate(['problem' => 'CHECK BATTERY'], ['description' => 'Battery not charging/holding charge']),
            Problem::firstOrCreate(['problem' => 'OTHER PROBLEM'], ['description' => 'Unclassified issue']),
        ];

        // userid isn't in these models' $fillable (matching how their real
        // controllers set it — direct property assignment, not mass-assignment),
        // so build via firstOrNew + explicit assignment rather than firstOrCreate.
        $groupProblem = Groupproblem::firstOrNew(['name' => 'Hardware Faults']);
        if(!$groupProblem->exists){ $groupProblem->userid = 1; $groupProblem->save(); }
        $groupProblem->problem()->syncWithoutDetaching([$problems[0]->id, $problems[1]->id, $problems[2]->id]);

        $categories = [];
        foreach([['Hardware','Physical parts'],['Software','OS/software services']] as [$name,$desc]){
            $cat = Category::firstOrNew(['name' => $name]);
            if(!$cat->exists){ $cat->description = $desc; $cat->userid = 1; $cat->save(); }
            $categories[] = $cat;
        }

        $brands = [];
        foreach(['ASUS','MSI','Lenovo'] as $name){
            $brand = Brand::firstOrNew(['name' => $name]);
            if(!$brand->exists){ $brand->description = $name; $brand->userid = 1; $brand->save(); }
            $brands[] = $brand;
        }

        $vendor = Vendor::firstOrCreate(
            ['emails' => 'supplier@test.com'],
            ['companyname' => 'TK Parts Supply', 'contactname' => 'Vendor Contact', 'contacttitle' => 'Sales', 'phone' => '023456789', 'status' => 1, 'address' => 'Phnom Penh']
        );

        $rmacode = Rmacode::firstOrNew(['code' => 'IW']);
        if(!$rmacode->exists){ $rmacode->userid = 1; $rmacode->save(); }

        // Real production catalog is stock/purchase-driven (Purchase/Sale item_id
        // FKs both point here) — direct property assignment matches
        // ItemController@store, which never mass-assigns.
        $items = [];
        foreach([
            ['DC Jack / Charging Port', 0, 1, 10],
            ['Laptop Battery (Generic)', 0, 1, 25],
            ['LCD Screen 15.6"', 0, 1, 60],
            ['Windows 11 Pro License', 0, 2, 40],
        ] as [$name, $brand_id, $cat_idx, $saleprice]){
            $item = Item::firstOrNew(['name' => $name]);
            if(!$item->exists){
                $item->userid = 1;
                $item->brand_id = $brand_id;
                $item->category_id = $categories[$cat_idx - 1]->id;
                $item->description = $name;
                $item->saleprice = $saleprice;
                $item->stockable = 0;
                $item->stockreorder = 5;
                $item->save();
            }
            $items[] = $item;
        }

        // --- Service tickets covering different workflow states ---
        // Service's date/paydate mutators expect display format (config('app.date_format')), not Y-m-d.
        $today = date(config('app.date_format'));

        // Ticket 1: fully closed out — Paid, Delivered, has a part with markup,
        // and a Labor Cost set, so the Revenue & Profit report has real numbers.
        $t1 = new Service();
        $t1->userid = 1;
        $t1->receiptno = date('Ymd') . '-TEST1';
        $t1->date = $today;
        $t1->serialno = 'T3ST0001';
        $t1->model = 'ASUS G614JU';
        $t1->description = 'Laptop no power, suspected battery/charging circuit fault';
        $t1->chargetype = 'cash';
        $t1->chargeamount = 25;
        $t1->labor_cost = 8;
        $t1->payment = 1;
        $t1->paydate = $today;
        $t1->customerid = $customers[0]->id;
        $t1->assignto = $engineer->id;
        $t1->phone = $customers[0]->phone;
        $t1->warrenty = 0;
        $t1->solution = 'Replaced charging port, tested OK';
        $t1->ordered = 0;
        $t1->status = 'complete';
        $t1->delivered = 1;
        $t1->save();
        $t1->problem()->sync([$problems[0]->id]);
        $t1->brand()->sync([$brands[0]->id]);
        Service_rma::create(['service_id' => $t1->id, 'category_id' => $categories[0]->id, 'item_name' => 'DC Jack / Charging Port', 'quantity' => 1, 'purchaseprice' => 4, 'sellingprice' => 10, 'payment' => 1]);
        ServiceStatus::create(['userid' => 1, 'service_id' => $t1->id, 'status' => 'complete']);

        // Ticket 2: fresh intake, untouched — exercises the basic list/dashboard state.
        $t2 = new Service();
        $t2->userid = 1;
        $t2->receiptno = date('Ymd') . '-TEST2';
        $t2->date = $today;
        $t2->serialno = 'T3ST0002';
        $t2->model = 'MSI Cyborg 15';
        $t2->description = 'Screen flickering intermittently';
        $t2->chargetype = 'free';
        $t2->chargeamount = 0;
        $t2->payment = 0;
        $t2->customerid = $customers[1]->id;
        $t2->assignto = $engineer->id;
        $t2->phone = $customers[1]->phone;
        $t2->warrenty = 1;
        $t2->solution = '';
        $t2->ordered = 0;
        $t2->status = 'not start';
        $t2->delivered = 0;
        $t2->save();
        $t2->problem()->sync([$problems[1]->id]);
        $t2->brand()->sync([$brands[1]->id]);
        ServiceStatus::create(['userid' => 1, 'service_id' => $t2->id, 'status' => 'not start']);

        // Ticket 3: mid-workflow, engineer actively on it, not yet paid.
        $t3 = new Service();
        $t3->userid = 1;
        $t3->receiptno = date('Ymd') . '-TEST3';
        $t3->date = $today;
        $t3->serialno = 'T3ST0003';
        $t3->model = 'Lenovo ThinkPad E14';
        $t3->description = 'Battery drains fast, suspected degraded cell';
        $t3->chargetype = 'cash';
        $t3->chargeamount = 15;
        $t3->payment = 0;
        $t3->customerid = $customers[2]->id;
        $t3->assignto = $engineer->id;
        $t3->phone = $customers[2]->phone;
        $t3->warrenty = 0;
        $t3->solution = 'Diagnosing battery health';
        $t3->ordered = 0;
        $t3->status = 'pending';
        $t3->delivered = 0;
        $t3->save();
        $t3->problem()->sync([$problems[2]->id]);
        $t3->brand()->sync([$brands[2]->id]);
        ServiceStatus::create(['userid' => 1, 'service_id' => $t3->id, 'status' => 'not start']);
        ServiceStatus::create(['userid' => $engineer->id, 'service_id' => $t3->id, 'status' => 'pending']);

        echo "TestDataSeeder: created 3 service tickets, 2 staff users, master data.\n";
        echo "Engineer login: engineer@test.com / secret\n";
        echo "Receptionist login: reception@test.com / secret\n";
    }
}
