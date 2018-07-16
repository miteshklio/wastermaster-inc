<?php

class HomePageTest extends IntegrationTestCase {

    public function testHomePage()
    {
        $response = $this->call('GET', '/');
        $this->assertEquals(200, $response->status());
    }

}
