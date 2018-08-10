<?php

use App\Controllers\HomeController;
use App\Controllers\VoucherController;

$app->get('/', HomeController::class . ':index');
$app->post('/api/offers/create', VoucherController::class . ':createOffers');
$app->post('/api/voucher/validate', VoucherController::class . ':validateVoucher');
$app->get('/api/voucher/list', VoucherController::class . ':fetchAllValidVoucherPerUser');

