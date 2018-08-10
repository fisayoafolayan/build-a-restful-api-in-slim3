<?php

use App\Controllers\VoucherController;

$app->post('/offers/create', VoucherController::class . ':createOffers');
$app->post('/voucher/validate', VoucherController::class . ':validateVoucher');
$app->get('/voucher/list', VoucherController::class . ':fetchAllValidVoucherPerUser');

