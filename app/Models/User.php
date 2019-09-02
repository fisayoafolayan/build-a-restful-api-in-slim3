<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email'
    ];

    /**
     * @param $emailList
     *
     * @return array
     */
    public static function findMultipleEmail($emailList)
    {
        // gets id of existing user, if user does not exist, create new user and return users id
        $usersId = [];
        foreach ($emailList as $email) {
            $userDetails = static::firstOrCreate(['email' =>$email]);
            $usersId[] = $userDetails->id;
        }

        return $usersId;
    }

    /**
     * @param $email
     *
     * @return mixed
     */
    public static function findEmail($email)
    {
        return static::where('email', $email)->first();
    }
}
