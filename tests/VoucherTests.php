<?php

use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;


class VoucherTests extends TestCase
{
    public function setUp()
    {
        try {
            (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
        } catch (Dotenv\Exception\InvalidPathException $e) {
            //
        }
        $this->http = new GuzzleHttp\Client([
                'base_uri' => getenv('APP_URL'),
                'exceptions' => false
            ]);       
    }

    public function testcreateOfferWithValidInformation() 
    {
        $request = $this->http->post('/api/offers/create', [
            'query' => [
                "name" => "Party Time",
                "discount" => 20,
                "expires_at" => "2018-8-15 23:50:49",
                "email_list[0]" => "fisayo@gmail.com",
                "email_list[1]" => "james@gmail.com"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(201,$request->getStatusCode());
        $this->assertArrayHasKey('offer_details',$body);
        $this->assertArrayHasKey('voucher_details',$body);
        $this->assertArrayHasKey('voucher',$body['voucher_details']);
    }

    public function testcreateOfferWithInvalidOfferName() 
    {
        $request = $this->http->post('/api/offers/create', [
            'query' => [
                "name" => "Party Time Special&*^*&%^&^%^",
                "discount" => 20,
                "expires_at" => "2018-8-15 23:50:49",
                "email_list[0]" => "fisayo@gmail.com"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(400,$request->getStatusCode());
    }

    public function testcreateOfferWithEmptyDiscount() 
    {
        $request = $this->http->post('/api/offers/create', [
            'query' => [
                "name" => "Party Time Special",
                "discount" => '',
                "expires_at" => "2018-8-15 23:50:49",
                "email_list[0]" => "fisayo@gmail.com"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(400,$request->getStatusCode());
    }

    public function testcreateOfferWithEmptyEmailAddress() 
    {
        $request = $this->http->post('/api/offers/create', [
            'query' => [
                "name" => "Party Time Special",
                "discount" => 25,
                "expires_at" => "",
                "email_list[0]" => "fisayo@gmail.com"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(400,$request->getStatusCode());
    }

    public function testValidateVoucherWithInvalidVoucherInput() 
    {
        $request = $this->http->post('/api/voucher/validate', [
            'query' => [
                "voucher" => "0f5ed4712sfgd",
                "email" => "fisayo@gmail.com"
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(403,$request->getStatusCode());
        $this->assertArrayHasKey('status',$body);
        $this->assertArrayHasKey('message',$body);
    }

    public function testValidateVoucherWithInvalidEmailInput() 
    {
        $request = $this->http->post('/api/voucher/validate', [
            'query' => [
                "voucher" => "0f5ed47",
                "email" => "fisayo @gmail.com"   
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(400,$request->getStatusCode());
            
    }

    public function testValidateVoucherWithValidInput() 
    {
        $request = $this->http->post('/api/voucher/validate', [
            'query' => [
                "voucher" => "0f5ed47",
                "email" => "fisayo@gmail.com"   
            ]
        ]);
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(403,$request->getStatusCode());
        $this->assertArrayHasKey('data',$body);
        $this->assertArrayHasKey('count',$body);
        $this->assertArrayHasKey('message',$body);

            
    }

    public function testUserVoucherLists() 
    {
        $request = $this->http->get('/api/voucher/list?email=fisayo@gmail.com');
        $body = json_decode($request->getBody(),true);
        $this->assertEquals(200,$request->getStatusCode());
        $this->assertArrayHasKey('status',$body);
        $this->assertArrayHasKey('count',$body);
        $this->assertArrayHasKey('data',$body);
    }

}


