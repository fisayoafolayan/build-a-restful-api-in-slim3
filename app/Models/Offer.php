<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'discount', 'expires_at'
    ];

    public function create($request)
    {
        
        $created_offer = self::firstOrCreate([
            'name'          => $request->getParam('name'),
            'discount'      => $request->getParam('discount'),
            'expires_at'    => $request->getParam('expires_at')
        ]);
        
        return $created_offer;
    }

}