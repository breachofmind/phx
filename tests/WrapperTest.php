<?php
use PHX\Wrapper;

class WrapperTest extends PHPUnit_Framework_TestCase {

    const TEST_CUSTOMER_USER    = 'accessifi_test';
    const TEST_CUSTOMER_PASS    = 'accessifi1';
    const TEST_CUSTOMER_ANSWER  = 'Sioux Falls';

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
        $this->phx->testing(true);
    }


    /**
     * Try out the environment vars and class instances.
     */
    public function test_env_variables()
    {
        // Check instance.
        $this->assertInstanceOf('\PHX\Wrapper', Wrapper::$instance);

        // Check if the correct URL being set from environment.
        $this->assertEquals(Wrapper::SERVICE_URL_DEV, $this->phx->serviceUrl);

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
        echo "Logging in to ".$this->phx->serviceUrl;
        $response = $this->phx->system->login();

        // check if the response is returning a token.
        $this->assertNotEmpty($response->access_token);

        // check if the token is being set in the wrapper.
        $this->assertNotEmpty($this->phx->serviceTokenID());
        $this->assertEquals($response->access_token, $this->phx->serviceTokenID());
    }

    /**
     * Test logging in as a test customer.
     */
    public function test_customer_login()
    {
        $response = $this->phx->customer->login(self::TEST_CUSTOMER_USER, self::TEST_CUSTOMER_PASS, self::TEST_CUSTOMER_ANSWER);

        // The response should return an access token and a customer ID.
        // The login method will set those variables globally.
        $this->assertEquals($this->phx->customerTokenID(), $response->access_token);
        $this->assertEquals($this->phx->customerID(), $response->customer_id);

        // Our service token should still be set.
        $this->assertNotEmpty($this->phx->serviceTokenID());

        // We should get a security question back.
        $question = $this->phx->customer->securityQuestion(self::TEST_CUSTOMER_USER);

        $this->assertNotEmpty($question('0.question_id'));
        $this->assertNotEmpty($question('0.question_text'));

        // We should get a customer object back, once we're logged in.
        $customer = $this->phx->customer->getObject();
        $this->assertEquals($customer->customer_id, $this->phx->customerID());

        // Check for Login method
        $this->assertTrue($this->phx->isLoggedIn());
    }
}