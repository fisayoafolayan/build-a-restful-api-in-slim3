<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
    ];

    /**
     * @param $offerId
     * @param $usersId
     *
     * @return array
     */
    public function create($offerId, $usersId): array
    {
        $vouchers = [];
        // Generate 8 random hex code for voucher
        foreach ($usersId as $key => $userId) {
            $vouchers['voucher'][$key]['code']        =   substr(md5(mt_rand()), 0, 8);
            $vouchers['voucher'][$key]['offer_id']    =   $offerId;
            $vouchers['voucher'][$key]['user_id']     =   $userId;
        }
        // insert into the database
        self::insert($vouchers['voucher']);

        return $vouchers;
    }



    /**
     * @param $voucher
     * @param $userId
     *
     * @return array
     */
    public function validateVoucher($voucher, $userId)
    {
        // Ensure that the voucher code belongs to the user and has not expired/not yet used
        $voucherDetails = self::leftjoin('users', 'vouchers.user_id', '=', 'users.id')
                                ->leftjoin('offers', 'vouchers.offer_id', '=', 'offers.id')
                                ->select('vouchers.code', 'users.id as user_id', 'users.email', 'offers.expires_at','offers.name as offer_name','offers.discount as percentage_discount')
                                ->where([
                                            ['vouchers.code', $voucher],
                                            ['vouchers.user_id', $userId],
                                            ['vouchers.is_used', 0],
                                            ['offers.expires_at', '>', Carbon::now()],
                                        ])
                                ->get();
        return ($voucherDetails ?? []);
    }


    /**
     * @param $voucher
     * @param $userId
     *
     * @return mixed
     */
    public function activateVoucher($voucher, $userId)
    {
        // activate voucher code, set is_used and date_used fields
        $activateVoucher = self::where([
                                            ['code', $voucher],
                                            ['user_id', $userId],
                                        ])
                                ->update(array('is_used' => 1, 'date_used' => Carbon::now() ));

        return $activateVoucher;
 
    }

    /**
     * @param $userId
     *
     * @return array
     */
    public function fetchSingleUserVoucher($userId): array
    {
        // method to fetch a single user's voucher details
        $voucherDetails = self::leftjoin('users', 'vouchers.user_id', '=', 'users.id')
                                ->leftjoin('offers', 'vouchers.offer_id', '=', 'offers.id')
                                ->select('vouchers.code','users.id as user_id', 'users.email', 'offers.expires_at','offers.name as offer_name','offers.discount as percentage_discount')
                                ->where([
                                            ['vouchers.user_id', $userId],
                                            ['vouchers.is_used', 0],
                                            ['offers.expires_at', '>',  Carbon::now()],
                                        ])
                                ->get()->toArray();
        return ($voucherDetails ?? []);
    }
 }
