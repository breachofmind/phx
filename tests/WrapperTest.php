<?php
class WrapperTest extends PHPUnit_Framework_TestCase {

    protected $phx;

    /**
     * Test the main functionality of the PHX wrapper.
     * @author Mike Adamczyk <mike.adamczyk@brightstar.com>
     */
    protected function setUp()
    {
        $this->phx = new \PHX\Wrapper();
    }

    public function test_env_variables()
    {
        $this->assertNotEmpty($this->phx->serviceUrl);
        $this->assertNotEmpty($this->phx->servicePassword);
        $this->assertNotEmpty($this->phx->serviceUsername);
    }
}