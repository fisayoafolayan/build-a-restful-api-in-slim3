<?php

namespace App\Controllers;

namespace App\Controllers;
use App\Controllers\Controller;
use App\Models\Offer;
use App\Models\User;
use App\Models\Voucher;
use Respect\Validation\Validator;




use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};


class VoucherController extends Controller
{
    public function createOffers(Request $request, Response $response, $args)
        {
            //checks to ensure we have valid inputs
            $validator = $this->c->validator->validate($request, [
                'name' => Validator::alnum("'-_")->notBlank(),
                'discount' => Validator::intVal()->noWhitespace()->notBlank(),
                'expires_at' => Validator::date()->notBlank(),
                'users_list' => Validator::arrayType(),
            ]);


            if ($validator->isValid()) {
                $offer_model    =   new Offer();
                $voucher_model  =   new Voucher();
                $user_model     =   new User();

                //Create new offer
                $created_offer   = $offer_model->create($request);

                if ($created_offer == true) {
                    //get id of users from the email, if email does not exist, create the user and return users_id
                    $get_user_user_ids  =   $user_model->findMultipleEmail($request->getParam('users_list'));
                    $voucher_codes      =   $voucher_model->create($created_offer->id, $get_user_user_ids );
                }    

                return $response->withJson([
                    'status' => (bool) $created_offer,
                    'offer_details'     => $created_offer,
                    'voucher_details'   => $voucher_codes,
                    'message' => $created_offer ? 'Offer Created!' : 'Error Creating Offer'
                ]);
            } else {
                //return an error on failed validation, with a statusCode of 400
                return $response->withStatus(400)->withJson([
                    'status' => 'Validation Error!',
                    'message' => $validator->getErrors()
                ]);
            }
     }

     public function validateVoucher(Request $request, Response $response, $args)
    {
        $validator = $this->c->validator->validate($request, [
            'voucher' => Validator::alnum()->notBlank(),
            'email' => Validator::email()->noWhitespace()->notBlank(),
        ]);

        if ($validator->isValid()) {

            $voucher    = $request->getParam('voucher');
            $email      = $request->getParam('email');

            $voucher_model    =   new Voucher();
            $user_model       =   new User();

            //check if user exist
            $user_details     =   $user_model->findEmail($email);

            if ($user_details) {
                //Assertain that the voucher code belongs to the user and has not expired/not yet used
                $validate_voucher =   $voucher_model->validateVoucher($voucher, $user_details->id);
                
                if (!$validate_voucher->isEmpty()) {
                    //activate and set date voucher was used
                    $activate_voucher   =   $voucher_model->activateVoucher($voucher, $user_details->id);
                    //return voucher details
                    return $response->withJson([
                        'status' => (bool) $validate_voucher,
                        'data'     => $validate_voucher
                    ]);
                } else {
                    //return failure message if voucher does not exist
                     return $response->withJson([
                    'status' => 'Failed!',
                    'message' => 'Voucher details is invalid!'
                    ]);
                }
            } else {
                //return failure message if user does not exist
                 return $response->withJson([
                    'status' => 'Failed!',
                    'message' => 'User does not exist!'
                    ]);
            }
        } else {
            return $response->withStatus(400)->withJson([
                'status' => 'Validation Error!',
                'message' => $validator->getErrors()
            ]);
        }
    }

     public function fetchAllValidVoucherPerUser(Request $request, Response $response, $args)
    {
        $validator = $this->c->validator->validate($request, [
            'email' => Validator::email()->noWhitespace()->notBlank(),
        ]);

        if ($validator->isValid()) {

            $email = $request->getQueryParam('email');

            $voucher_model    =   new Voucher();
            $user_model       =   new User();

            //check if user exist
            $user_details     =   $user_model->findEmail($email);

            if ($user_details) {

                //Fetch all valid user voucher codes
                $users_voucher =   $voucher_model->fetchSingleUserVoucher($user_details->id);

                //return voucher details
                    return $response->withJson([
                        'status' => (bool) $users_voucher,
                        'data'     => $users_voucher
                    ]);

            } else {
                //return failure message if user does not exist
                 return $response->withStatus(400)->withJson([
                    'status' => 'Failed!',
                    'message' => 'User does not exist!'
                    ]);
            }
        } else {
            return $response->withStatus(400)->withJson([
                'status' => 'Validation Error!',
                'message' => $validator->getErrors()
            ]);
        }

        
    }
}