<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
    ];

    public function create($offer_id, $users_id){
        //Generate 8 random hex code for voucher
        
        foreach ($users_id as $key => $user_id) {
            $vouchers['voucher'][$key]['code']        =   substr(md5(rand()), 0, 8);
            $vouchers['voucher'][$key]['offer_id']    =   $offer_id;
            $vouchers['voucher'][$key]['user_id']     =   $user_id;
        }
        // insert into the database
        self::insert($vouchers['voucher']);

        return $vouchers;
    }
    //Assertain that the voucher code belongs to the user and has not expired/not yet used
    public function validateVoucher($voucher, $user_id)
    {    
        $voucher_details = self::leftjoin('users', 'vouchers.user_id', '=', 'users.id')
                                ->leftjoin('offers', 'vouchers.offer_id', '=', 'offers.id')
                                ->select('vouchers.code', 'users.id as user_id', 'users.email', 'offers.expires_at','offers.name as offer_name','offers.discount as percentage_discount')
                                ->where([
                                            ['vouchers.code', $voucher],
                                            ['vouchers.user_id', $user_id],
                                            ['vouchers.is_used', 0],
                                            ['offers.expires_at', '>', \Carbon\Carbon::now()],
                                        ])
                                ->get();
                                
        return ($voucher_details == null ? [] : $voucher_details);
    }

    // activate voucher code, set is_used and date_used fields
    public function activateVoucher($voucher, $user_id)
    {  
        $activate_voucher = self::where([
                                            ['code', $voucher],
                                            ['user_id', $user_id],
                                        ])
                                ->update(array('is_used' => 1, 'date_used' => \Carbon\Carbon::now() ));

        return $activate_voucher;
 
    }
    //method to fetch a single user's voucher details
    public function fetchSingleUserVoucher($user_id)
    {    
        $voucher_details = self::leftjoin('users', 'vouchers.user_id', '=', 'users.id')
                                ->leftjoin('offers', 'vouchers.offer_id', '=', 'offers.id')
                                ->select('vouchers.code','users.id as user_id', 'users.email', 'offers.expires_at','offers.name as offer_name','offers.discount as percentage_discount')
                                
                                ->where([
                                            ['vouchers.user_id', $user_id],
                                            ['vouchers.is_used', 0],
                                            ['offers.expires_at', '>',  \Carbon\Carbon::now()],
                                        ])
                                ->get();
   
        return ($voucher_details == null ? [] : $voucher_details);
 
    }
 }