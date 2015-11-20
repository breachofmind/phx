<?php
namespace PHX;

use ErrorException;
use Exception;
use Dotenv;
use PHX\Services\CustomerService;
use PHX\Services\DebtService;
use PHX\Services\SystemService;

class Wrapper {

    static $instance;

    const SERVICE_TIMEOUT   = 120;
    const CONTEXT_ID        = 10008;

    /**
     * Session variable names.
     * @var string
     */
    const SESSION_SERVICETOKEN  = 'SERVICE_TOKENID';
    const SESSION_CUSTOMERTOKEN = 'CUSTOMER_TOKENID';
    const SESSION_CUSTOMERID    = 'CUSTOMER_ID';
    const SESSION_DEBTID        = 'CUSTOMER_ID';
    const SESSION_LOGIN         = 'CUSTOMER_USERNAME';

    /**
     * Web Service endpoints.
     * @var string
     */
    const SERVICE_URL           = "https://phxservices.ws.totalcardinc.com/";
    const SERVICE_URL_DEV       = "https://phxservices.staging.ws.totalcardinc.com/";

    /**
     * Error messages.
     * @var array
     */
    protected $errorBag = [];

    /**
     * CustomerService instance.
     * @var CustomerService
     */
    public $customer;

    /**
     * DebtService instance.
     * @var DebtService
     */
    public $debts;

    /**
     * SystemService instance.
     * @var SystemService
     */
    public $system;

    /**
     * Service URL credentials.
     * @var string
     */
    public $serviceUrl;
    public $serviceUsername;
    public $servicePassword;

    /**
     * Are we running in test mode?
     * @var bool
     */
    public $isTest = false;

    /**
     * Additional PHX service providers.
     * @var array
     */
    protected $providers = [
        'system'       => 'PHX\Services\SystemService',
        'customer'     => 'PHX\Services\CustomerService',
        'document'     => 'PHX\Services\DocumentService',
        'debt'         => 'PHX\Services\DebtService',
        'payaccount'   => 'PHX\Services\PayAccountService',
        'report'       => 'PHX\Services\ReportService',
    ];

    /**
     * Constructor.
     * Sets up the environment login and service class instances.
     * @throws ErrorException
     */
    public function __construct()
    {
        static::$instance = $this;

        $this->testing(DotEnv::findEnvironmentVariable('PHX_ENV')==="testing");
        $this->serviceUsername = DotEnv::findEnvironmentVariable('PHX_USER');
        $this->servicePassword = DotEnv::findEnvironmentVariable('PHX_PASS');

        // Create the additional service provider instances.
        foreach ($this->providers as $provider=>$class) {
            $this->$provider = new $class($this);
        }
    }

    /**
     * Shortcut for changing the testing URL.
     * @param bool|true $boolean
     */
    public function testing($boolean=true)
    {
        $this->isTest = $boolean;
        $this->serviceUrl = $boolean
            ? self::SERVICE_URL_DEV
            : self::SERVICE_URL;
    }

    /**
     * Named constructor for connecting.
     * @param $user string (customer)
     * @param $pass string (customer)
     * @param $answer string (customer)
     * @return Wrapper
     */
    public static function connect($user=null,$pass=null,$answer=null)
    {
        $phx = new static();
        $phx->system->login();
        if ($user) {
            $phx->customer->login($user,$pass,$answer);
        }
        return $phx;
    }

    /**
     * Return the error message array.
     * @return array
     */
    public function getErrors()
    {
        return $this->errorBag;
    }


    /**
     * Get/Set a session variable.
     * @param $name string
     * @param null|string $value
     * @return string
     */
    public function sessionVar ($name, $value=null)
    {
        if (!is_null($value)) {
            $_SESSION[$name] = $value;
        }
        return isset($_SESSION[$name]) && !empty($_SESSION[$name]) ? $_SESSION[$name] : null;
    }


    /**
     * Methods for returning common session variables.
     * @param null|string $value
     * @return string|null
     */
    public function serviceTokenID($value=null) {
        return $this->sessionVar(self::SESSION_SERVICETOKEN, $value);
    }
    public function customerTokenID($value=null) {
        return $this->sessionVar(self::SESSION_CUSTOMERTOKEN, $value);
    }
    public function customerID($value=null) {
        return $this->sessionVar(self::SESSION_CUSTOMERID, $value);
    }
    public function debtID($value=null) {
        return $this->sessionVar(self::SESSION_DEBTID, $value);
    }
    public function customerUsername($value=null) {
        return $this->sessionVar(self::SESSION_LOGIN, $value);
    }

    /**
     * Check if the customer is logged in.
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->customerTokenID() && $this->customerID();
    }

    /**
     * Return the token currently being used.
     * @return string
     */
    public function activeToken()
    {
        if ($token = $this->customerTokenID()) {
            return $token;
        }
        return $this->serviceTokenID();
    }
}