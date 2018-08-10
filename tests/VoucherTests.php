<?php

use PHPUnit\Framework\TestCase; 

class VoucherTests extends TestCase
{
    public function testRecipientGet() {
        $this->assertTrue(true);
    } 
    public function testGettingAllUnusedVoucher()
    {
        $this->client->get('/version');
        $this->assertEquals(200, $this->client->response->status());
        $this->assertEquals($this->app->config('version'), $this->client->response->body());
    }
}


