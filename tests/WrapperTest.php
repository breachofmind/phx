<?php
use PHX\Wrapper;

class WrapperTest extends PHPUnit_Framework_TestCase {

    const TEST_CUSTOMER_USER    = 'accessifi_test';
    const TEST_CUSTOMER_PASS    = 'accessifi1';
    const TEST_CUSTOMER_ANSWER  = 'Sioux Falls';
    const TEST_CUSTOMER_ACCOUNT = 'BR03914646';

    /**
     * The instance being tested.
     * @var Wrapper
     */
    protected $phx;

    protected $loggedIn = false;

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

        $this->loggedIn = !empty($response->access_token);
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
        $this->assertInstanceOf('\PHX\Models\Customer', $customer);
        $this->assertEquals($customer->id(), $this->phx->customerID());

        // Check for Login method
        $this->assertTrue($this->phx->isLoggedIn());
    }

    /**
     * Test changing customer data.
     */
    public function test_customer_sync()
    {
        $customer = $this->phx->customer->getAccount(self::TEST_CUSTOMER_ACCOUNT);
        $this->assertInstanceOf('\PHX\Models\Customer', $customer);
        $original = $customer->name_first;
        // Change the name, fire the sync. A good sync is true.
        $customer->name_first = "TEST";
        $this->assertTrue($customer->sync());

        // Grab the customer object again.
        $customer = $this->phx->customer->getAccount(self::TEST_CUSTOMER_ACCOUNT);
        $this->assertEquals("TEST", $customer->name_first);
        // Change back
        $customer->name_first = $original;
        $this->assertTrue($customer->sync());

        $customer = $this->phx->customer->getAccount(self::TEST_CUSTOMER_ACCOUNT);
        $this->assertEquals($original, $customer->name_first);
    }

    /**
     * Test debt-related junk.
     */
    public function test_customer_debts()
    {
        if (!$this->loggedIn) {
            $this->phx->system->login();
        }

        $customer = $this->phx->customer->getAccount(self::TEST_CUSTOMER_ACCOUNT);
        $this->assertTrue(is_array($customer->debts));
        $this->assertGreaterThan(0, $customer->balance());

        $debtObjects = $customer->getDebts();
        $this->assertInstanceOf('PHX\Collection', $debtObjects);
        $this->assertGreaterThan(0, $debtObjects->count());
    }
}