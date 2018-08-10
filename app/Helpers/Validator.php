<?php 

namespace App\Helpers;

use Respect\Validation\Validator as Respect;

class Validator extends Respect {
    public static function validateEmails($email_list) 
    {
        if(!is_array($email_list)) return false;
        foreach ($email_list as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }
        return true;
    }

}
