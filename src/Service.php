<?php
namespace PHX;

use ErrorException;
use Exception;

class Service {

    /**
     * Constructor.
     * @throws ErrorException
     */
    public function __construct(Wrapper $phx)
    {
        $this->phx = $phx;
    }

    /**
     * Send a request to the service url and returns the JSON as an object.
     * @param $endpoint string
     * @param string $method
     * @param array $data
     * @return Response
     * @throws Exception
     */
    protected function send ($endpoint,$method="POST",$data=[])
    {
        $query = [];
        $query['access_token'] = $data['access_token'] = $this->phx->serviceTokenID();

        $options = [
          CURLOPT_CUSTOMREQUEST  => $method,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_HEADER         => false,
          CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
          CURLOPT_ENCODING       => "utf-8",
          CURLOPT_CONNECTTIMEOUT => Wrapper::SERVICE_TIMEOUT,
          CURLOPT_TIMEOUT        => Wrapper::SERVICE_TIMEOUT,
          CURLOPT_POSTFIELDS     => $method=="GET" ? null : json_encode($data),

        ];

        if ($method=="GET") {
            $query = array_merge($data,$query);
        }

        $endpoint = $this->getEndpoint($endpoint, $query);
        $response = $this->curl($endpoint, $options);

        return new Response($response, compact('endpoint','data','options'));
    }

    /**
     * Send a GET request.
     * @param $endpoint string
     * @param array $data
     * @return Response
     */
    public function get ($endpoint, $data=[])
    {
        return $this->send($endpoint,"GET", $data);
    }

    /**
     * Send a POST request.
     * @param $endpoint string
     * @param array $data
     * @return Response
     */
    public function post ($endpoint, $data=[])
    {
        return $this->send($endpoint,"POST",$data);
    }

    /**
     * Send a PUT request.
     * Response should be true if all is well.
     * @param $endpoint string
     * @param array $data
     * @return boolean
     * @throws \HttpException
     */
    public function put ($endpoint,$data=[])
    {
        $response = $this->send($endpoint,"PUT",$data);
        if ($response->msg !== "done") {
            return $response;
        }
        return true;
    }

    /**
     * Builds an endpoint using the service url and any GET params.
     * @param $endpoint string
     * @param array $query
     * @return string
     */
    protected function getEndpoint ($endpoint, $query=[])
    {
        $query = http_build_query($query);
        return $this->phx->serviceUrl.$endpoint.($query ? "?$query" : "");
    }

    /**
     * Perform a CURL request.
     * @param $url string
     * @param array $options curlopt
     * @return false|string
     * @throws Exception
     */
    protected function curl ($url, array $options)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch,$options);
        $response = curl_exec($ch);
        $err = curl_errno($ch);
        if ($err > 0) {
          throw new Exception(curl_error($ch), $err);
        }
        curl_close($ch);
        return $response;
    }


    /**
     * Send a direct message.
     * @param $recipient
     * @param $body
     * @param $subject
     * @param $from
     * @param $message_type
     * @param $message_replacements
     * @param $send_date
     * @param $DocumentID
     * @return Response
     */
    public function message ($recipient,$body,$subject,$from,$message_type,$message_replacements,$send_date,$DocumentID)
    {
        return $this->post('message', [
            'recipient' => $recipient,
            'message_body' => $body,
            'subject_line' => $subject,
            'from' => $from,
            'message_type_name' => $message_type,
            'message_replacements' => $message_replacements,
            'send_date' => $send_date,
            'document_id' => $DocumentID
        ]);
    }


}