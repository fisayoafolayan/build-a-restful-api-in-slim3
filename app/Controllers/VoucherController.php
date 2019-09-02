<?php

namespace App\Controllers;

use App\Models\Offer;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherResponseConstants;
use App\Helpers\Validator;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class VoucherController extends Controller
{

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return mixed
     */
    public function createOffers(Request $request, Response $response)
    {

        // checks to ensure we have valid inputs
        $validator = $this->container->validator->validate($request, [
            'email_list' => Validator::arrayType(),
            'expires_at' => Validator::date()->notBlank(),
            'name' => Validator::alnum("'-_")->notBlank(),
            'discount' => Validator::intVal()->noWhitespace()->notBlank(),
        ]);

        if ($validator->isValid()) {
            $offerModel = new Offer();
            $voucherModel = new Voucher();
            $voucherCodes = [];

            if (Validator::validateEmails($request->getParam('email_list'))) {
                // Create new offer
                $createNewOffer = $offerModel->create($request);
               
            }
            else {
                return $response->withStatus(400)->withJson([
                    'status' => VoucherResponseConstants::ERROR_STATUS,
                    'message' => VoucherResponseConstants::INVALID_EMAIL
                ]);
            }

            if ($createNewOffer) {
                // get id of users from the email, if email does not exist, create the user and return users_id
                $getUserIds  =  User::findMultipleEmail($request->getParam('email_list'));
                $voucherCodes =  $voucherModel->create($createNewOffer->id, $getUserIds);
            }    

            return $response->withStatus(201)->withJson([
                'status'  => VoucherResponseConstants::SUCCESS_STATUS,
                'offer_details'  => $createNewOffer,
                'voucher_details'  => $voucherCodes,
                'message'  => $createNewOffer ? VoucherResponseConstants::OFFER_CREATED
                    : VoucherResponseConstants::ERROR_CREATING_OFFER
            ]);
        }
        // return an error on failed validation, with a statusCode of 400
        return $response->withStatus(400)->withJson([
            'status'  => VoucherResponseConstants::VALIDATION_ERROR,
            'message'  => $validator->getErrors()
        ]);

    }


    /**
     * @param Request $request
     * @param Response $response
     *
     * @return mixed
     */
    public function validateVoucher(Request $request, Response $response)
    {
        $validator = $this->container->validator->validate($request, [
            'voucher' => Validator::alnum()->notBlank(),
            'email' => Validator::email()->noWhitespace()->notBlank(),
        ]);

        if ($validator->isValid()) {

            $voucher    = $request->getParam('voucher');
            $email      = $request->getParam('email');
            $voucherModel    =   new Voucher();

            // check if user exist
            $userDetails     =   User::findEmail($email);

            if ($userDetails) {
                // Ensure that the voucher code belongs to the user and has not expired/not yet used
                $validateVoucher =   $voucherModel->validateVoucher($voucher, $userDetails->id);
                if (!$validateVoucher->isEmpty()) {
                    // activate and set date voucher was used
                    $voucherModel->activateVoucher($voucher, $userDetails->id);
                    // return voucher details
                    return $response->withStatus(200)->withJson([
                        'status'    => (bool) $validateVoucher,
                        'count'     => count($validateVoucher),
                        'data'      => $validateVoucher,
                        'message'   => count($validateVoucher) >= 1 ? VoucherResponseConstants::SUCCESS_STATUS
                            : VoucherResponseConstants::INVALID_VOUCHER_DETAILS
                    ]);
                }
                // return failure message if voucher does not exist
                 return $response->withStatus(403)->withJson([
                    'status' => VoucherResponseConstants::ERROR_STATUS,
                    'message' => VoucherResponseConstants::INVALID_VOUCHER_DETAILS
                ]);
            }
            // return failure message if user does not exist
             return $response->withStatus(400)->withJson([
                'status' => VoucherResponseConstants::ERROR_STATUS,
                'message' => VoucherResponseConstants::USER_DOES_NOT_EXIST
             ]);
        }
        // return failure message if validation fails
        return $response->withStatus(400)->withJson([
            'status' =>  VoucherResponseConstants::VALIDATION_ERROR,
            'message' => $validator->getErrors()
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return mixed
     */
    public function fetchAllValidVoucherPerUser(Request $request, Response $response)
    {
        $validator = $this->container->validator->validate($request, [
            'email' => Validator::email()->noWhitespace()->notBlank(),
        ]);

        if ($validator->isValid()) {

            $email = $request->getQueryParam('email');

            $voucherModel    =   new Voucher();

            //check if user exist
            $userDetails     =   User::findEmail($email);

            if ($userDetails) {
                //Fetch all valid user voucher codes
                $usersVoucher =   $voucherModel->fetchSingleUserVoucher($userDetails->id);
                //return voucher details
                    return $response->withStatus(200)->withJson([
                        'status'  => (bool) $usersVoucher,
                        'count' => count($usersVoucher),
                        'data'  => $usersVoucher
                    ]);

            }
            //return failure message if user does not exist
            return $response->withStatus(400)->withJson([
                'status' => VoucherResponseConstants::ERROR_STATUS,
                'message' => VoucherResponseConstants::USER_DOES_NOT_EXIST
                ]);
        }
        return $response->withStatus(400)->withJson([
            'status' => VoucherResponseConstants::VALIDATION_ERROR,
            'message' => $validator->getErrors()
        ]);

    }
}
