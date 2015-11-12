<?php
namespace PHX;

use ErrorException;
use Exception;

class Wrapper {

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
     * Loaded config ini file.
     * @var array
     */
    protected $ini = [];

    /**
     * Error messages.
     * @var array
     */
    protected $errorBag = [];

    protected $serviceUrl;
    protected $serviceUsername;
    protected $servicePassword;

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
     * @throws ErrorException
     */
    public function __construct()
    {
        include '../inc/helpers.php';

        $this->serviceUrl      = env('phx_url');
        $this->serviceUsername = env('phx_username');
        $this->servicePassword = env('phx_password');

        // Create the additional service provider instances.
        foreach ($this->providers as $provider=>$class) {
            $this->$provider = new $class($this);
        }
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
        if (is_string($value)) {
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