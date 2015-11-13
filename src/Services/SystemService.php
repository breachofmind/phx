<?php
namespace PHX\Services;

use PHX\Service;
use PHX\Response;
use ErrorException;
use PHX\Wrapper;

class SystemService extends Service {

    /**
     * PHX wrapper.
     * @var Wrapper
     */
    protected $phx;

    /**
     * Return the user's login data.
     * @return array
     */
    protected function getLoginData ()
    {
        return  [
            'username'   => $this->phx->serviceUsername,
            'password'   => $this->phx->servicePassword,
            'context_id' => Wrapper::CONTEXT_ID,
        ];
    }

    /**
     * Login into the API and store the access token in the session.
     * @return Response
     * @throws ErrorException
     */
    public function login()
    {
        $response = $this->post('system/login', $this->getLoginData());

        if ($response->hasError()) {
            throw new ErrorException('Failed to Log In To PHX Services');
        }
        $this->phx->serviceTokenID($response->access_token);

        return $response;
    }


    /**
     * Send a keepalive request.
     * @return mixed|null
     */
    protected function keepAlive()
    {
        if (!$this->phx->serviceTokenID()) {
            return null;
        }
        return $this->get('system/session/'.$this->phx->serviceTokenID());
    }

}