<?php

use App\Controllers\VoucherController;

$app->post('/api/offers/create', VoucherController::class . ':createOffers');
$app->post('/api/voucher/validate', VoucherController::class . ':validateVoucher');
$app->get('/api/voucher/list', VoucherController::class . ':fetchAllValidVoucherPerUser');

