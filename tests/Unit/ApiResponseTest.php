<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ApiResponse;

class ApiResponseTest extends TestCase
{
    public function test_success_response()
    {
        $data = ['title' => 'Test'];
        $response = ApiResponse::success($data, 'OK', 200);

        $responseArray = $response->getData(true);

        $this->assertEquals(200, $responseArray['status']);
        $this->assertEquals('OK', $responseArray['message']);
        $this->assertEquals($data, $responseArray['data']);
    }

    public function test_error_response()
    {
        $response = ApiResponse::error('Failed', 400);

        $responseArray = $response->getData(true);

        $this->assertEquals(400, $responseArray['status']);
        $this->assertEquals('Failed', $responseArray['message']);
        $this->assertNull($responseArray['data']);
    }
}