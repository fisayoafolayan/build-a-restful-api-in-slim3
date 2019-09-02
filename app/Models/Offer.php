<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'discount', 'expires_at'
    ];

    /**
     * @param $request
     *
     * @return mixed
     */
    public function create($request)
    {
        $createdOffer = self::firstOrCreate([
            'name'          => $request->getParam('name'),
            'discount'      => $request->getParam('discount'),
            'expires_at'    => $request->getParam('expires_at')
        ]);
        
        return $createdOffer;
    }

}
