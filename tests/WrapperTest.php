<?php
use PHX\Wrapper;

class WrapperTest extends PHPUnit_Framework_TestCase {

    /**
     * The instance being tested.
     * @var Wrapper
     */
    protected $phx;

    /**
     * Test the main functionality of the PHX wrapper.
     * @author Mike Adamczyk <mike.adamczyk@brightstar.com>
     */
    protected function setUp()
    {
        $this->phx = new Wrapper();
    }


    /**
     * Try out the environment vars and class instances.
     */
    public function test_env_variables()
    {
        $this->assertNotEmpty($this->phx->serviceUrl);
        $this->assertNotEmpty($this->phx->servicePassword);
        $this->assertNotEmpty($this->phx->serviceUsername);

        // Check for a few extension instances.
        $instances = ['system','report','customer','payaccount'];
        foreach ($instances as $instance) {
            $this->assertNotNull($this->phx->$instance);
        }
    }

    /**
     * Try logging in and getting the API access token.
     */
    public function test_login_response()
    {
        $response = $this->phx->system->login();

        // check if the response is returning a token.
        $this->assertNotEmpty($response->access_token);

        // check if the token is being set in the wrapper.
        $this->assertNotEmpty($this->phx->serviceTokenID());
        $this->assertEquals($response->access_token, $this->phx->serviceTokenID());
    }
}