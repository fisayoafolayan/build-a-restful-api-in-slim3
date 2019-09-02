<?php 

namespace App\Helpers;

use Respect\Validation\Validator as Respect;

class Validator extends Respect {
    
    public static function validateEmails($emailList) 
    {
        if(!is_array($emailList)) return false;
        foreach ($emailList as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }
        return true;
    }

}
