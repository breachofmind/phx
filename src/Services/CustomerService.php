<?php
namespace PHX\Services;

use PHX\Collection;
use PHX\Models\Customer;
use PHX\Models\Location;
use PHX\Service;
use PHX\Response;
use ErrorException;

class CustomerService extends Service {


    /**
     * Get a customer.
     * @return Customer
     * @throws \Exception
     */
    public function getObject()
    {
        $id = $this->phx->customerID();
        $response = $this->get("customer/{$id}");
        if ($response->hasError()) {
            return $response;
        }
        return Customer::create($response);
    }

    /**
     * Search for a customer.
     * @param array $criteria
     * @return Response
     */
    public function search($criteria=[])
    {
        return $this->post("customer/search", $criteria);
    }

    /**
     * Look up a customer by the account number.
     * @param $number
     * @return Response
     */
    public function searchAccount($number)
    {
        return $this->search(['account_number'=>$number]);
    }

    /**
     * Get a customer object given the account number.
     * @param $number string
     * @return Customer|null
     */
    public function getAccount($number)
    {
        $response = $this->searchAccount($number);
        if ($response->hasError()) {

            return $response;
        }
        if (count($response->body())===1) {
            return Customer::create($response("0"));
        }
        return null;
    }


    /**
     * Get the security question for the given user name.
     * @param $username string
     * @return Response
     */
    public function securityQuestion ($username)
    {
        return $this->post('customer/login/question', compact('username'));
    }

    /**
     * Login a customer.
     * @param $username string
     * @param $password string
     * @param $answer string
     * @param bool|false $skip_answer_check
     * @return Response
     * @throws ErrorException
     */
    public function login ($username,$password,$answer,$skip_answer_check=false)
    {
        $response = $this->post ('customer/login', compact('username','password','answer','skip_answer_check'));

        if ($response->success()) {
            $this->phx->customerTokenID($response->access_token);
            $this->phx->customerID($response->customer_id);
        }

        return $response;
    }

    /**
     * Update the customer login info.
     * @param $id string customer id
     * @param null|string $password
     * @param null|string $question_id
     * @param null|string $answer
     * @return Response
     */
    public function setLogin ($id, $password=null, $question_id=null, $answer=null)
    {
        $data = compact('password','question_id','answer');
        foreach($data as $key=>$val) {
            if (empty($data[$key])) unset($data[$key]);
        }
        return $this->put("customer/{$id}/security", $data);
    }

    /**
     * Searches for customer and registers based on registration info.
     *
     * "registration_info": {
     *    "account_number": "4132079847652534",
     *    "zipcode": "57701",
     *    "email_address": "joe@registerme.com"
     * }
     *
     * Requires Full Login Info to be supplied for successL
     * "login_info":{
     *    "username": "jLogin",
     *    "password": "jr98dndTE",
     *    "question_id": 2,
     *    "answer": "this time"
     * }
     * @param $registration_info
     * @param $login_info
     * @return Response
     */
    public function register($registration_info, $login_info)
    {
        return $this->post('customer/register', compact('registration_info','login_info'));
    }





    /**
     * Customer forgot their login info, provide reset.
     * @param $email_address string
     * @param $username string
     * @return Response
     */
    public function forgotLogin($email_address, $username)
    {
        return $this->post('customer/forgotpassword', compact('email_address','username'));
    }


    /**
     * Return client locations.
     * @param $id
     * @return Collection
     */
    public function locations ($id)
    {
        $response = $this->post("client/{$id}/division/search", [
            'active' => true,
            'order_by' => 'State, Name'
        ]);

        $collection = new Collection();

        foreach ($response->body() as $location) {
            $collection->add(new Location($location));
        }

        return $collection;
    }


//
//
//    public function SaveProfileInformation($updates)
//    {
//        $params = array();
//        if(isset($updates->FirstName) && strlen($updates->FirstName)){$params['name_first'] = $updates->FirstName;}
//        /*if(isset($updates->MiddleName) && strlen($updates->MiddleName)){$params['name_middle'] = $updates->MiddleName;}*/
//        if(isset($updates->MiddleName)){$params['name_middle'] = $updates->MiddleName;}
//        if(isset($updates->LastName) && strlen($updates->LastName)){$params['name_last'] = $updates->LastName;}
//        if(isset($updates->DateOfBirth) && strlen($updates->DateOfBirth)){$params['date_of_birth'] = $updates->DateOfBirth;}
//
//        $phones = array();
//        /*if(isset($updates->HomePhone) && strlen($updates->HomePhone)){*/
//        if(isset($updates->HomePhone)){
//            $new_phone = array('type'=>'home','number'=> str_replace(" ", "", preg_replace("/[^a-zA-Z0-9\s]/", "", $updates->HomePhone)));
//            //$new_phone = array('type'=>'home','number'=> $updates->HomePhone);
//            array_push($phones,(object)$new_phone);
//        }
//        if(isset($updates->CellPhone) && strlen($updates->CellPhone)){
//            $new_phone = array('type'=>'cell','number'=> str_replace(" ", "", preg_replace("/[^a-zA-Z0-9\s]/", "", $updates->CellPhone)));
//            //$new_phone = array('type'=>'cell','number'=> $updates->CellPhone);
//            array_push($phones,(object)$new_phone);
//        }
//        if(count($phones)){ $params['phones'] = $phones; }
//
//        $emails = array();
//        if(isset($updates->EmailAddress) && strlen($updates->EmailAddress)){
//            $new_email = array('type'=>'home','email'=> $updates->EmailAddress);
//            array_push($emails,(object)$new_email);
//        }
//        if(count($emails)){ $params['emails'] = $emails; }
//
//        $addresses = array();
//        $mailing_address = array();
//        if(isset($updates->MailingLine1) && strlen($updates->MailingLine1)){$mailing_address['line_1'] = $updates->MailingLine1;}
//        if(isset($updates->MailingLine2) && strlen($updates->MailingLine2)){$mailing_address['line_2'] = $updates->MailingLine2;}
//        if(isset($updates->MailingCity) && strlen($updates->MailingCity)){$mailing_address['city'] = $updates->MailingCity;}
//        if(isset($updates->MailingState) && strlen($updates->MailingState)){$mailing_address['state'] = $updates->MailingState;}
//        if(isset($updates->MailingZipcode) && strlen($updates->MailingZipcode)){$mailing_address['zipcode'] = $updates->MailingZipcode;}
//
//        if(count($mailing_address)){
//            $mailing_address['type'] = 'mailing';
//            array_push($addresses,$mailing_address);
//        }
//        if(count($addresses)){ $params['addresses'] = $addresses; }
//
//        if(count($params) == 0){
//            return (object)array('msg'=>'done','action'=>'none'); //Early Exit due to nothing to process
//        }
//
//        $endpoint = "/customer/".$this->CustomerID();
//        $result = $this->DoServiceRequest($endpoint,'PUT',(object)$params);
//        return $result;
//    }
//
//
//
//    public function SaveSecurityInformation($updates)
//    {
//        $params = array();
//        if(isset($updates->Password) && strlen($updates->Password)){$params['password'] = $updates->Password;}
//        if(isset($updates->QuestionID) && strlen($updates->QuestionID)){$params['question_id'] = $updates->QuestionID;}
//        if(isset($updates->Answer) && strlen($updates->Answer)){$params['answer'] = $updates->Answer;}
//
//        if(count($params) == 0){
//            return (object)array('msg'=>'done','action'=>'none'); //Early Exit due to nothing to process
//        }
//
//        $params = array('login_info' => (object)$params);
//
//        $endpoint = "/customer/".$this->CustomerID().'/security';
//        $result = $this->DoServiceRequest($endpoint,'PUT',(object)$params);
//        return $result;
//    }


}