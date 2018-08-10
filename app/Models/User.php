<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'email'
    ];
    
    public static function findMultipleEmail($email_list)
    {
        // gets id of existing user, if user does not exist, create new user and return users id
        $users_id = [];
        foreach ($email_list as $email) {
            $user_details = static::firstOrCreate(['email' =>$email]);
            array_push($users_id, $user_details->id);
        }
        
        return $users_id;
    }
    
    public static function findEmail($email)
    {
        return static::where('email', $email)->first();
    }
}