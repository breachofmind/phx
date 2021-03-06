<?php
namespace PHX;

class Response {

    protected $response;

    protected $request;

    protected $raw;

    /**
     * PHXResponse constructor.
     * @param string $response
     */
    public function __construct($response, $request=null)
    {
        $this->raw = $response;
        $this->response = json_decode($response);
        $this->request  = $request;
    }

    /**
     * Named constructor for chaining.
     * @param $response string
     * @return static
     */
    public static function create($response)
    {
        return new static ($response);
    }

    /**
     * Access response properties.
     * @param $name string
     * @return mixed
     */
    public function __get($name)
    {
        if (!$this->response || !isset($this->response->$name)) {
            return null;
        }
        return $this->response->$name;
    }

    /**
     * Get the response body.
     * @return mixed
     */
    public function body()
    {
        return $this->response;
    }

    public function __invoke($dotIndex)
    {
        $parts = explode(".",$dotIndex);
        $val = $this->response;
        foreach ($parts as $key) {
            if (is_array($val) && isset($val[$key])) {
                $val = $val[$key];
            } elseif (isset($val->$key)) {
                $val = $val->$key;
            } else {
                return null;
            }
        }
        return $val;
    }


    /**
     * Check if there was an error with this response.
     * @return bool
     */
    public function hasError()
    {
        return empty($this->response) || !empty($this->errors);
    }

    /**
     * Check if everything is hunky dorey.
     * @return bool
     */
    public function success()
    {
        return ! $this->hasError();
    }

    /**
     * Return a JSON response error.
     * @param $message string
     * @param int $code
     * @return string
     */
    public static function error($message,$code=0)
    {
        return json_encode([
            'errors' => [
                ['code'=>$code, 'description'=>$message]
            ]
        ]);
    }

    /**
     * Return the response as an array..
     * @return mixed
     */
    public function toArray()
    {
        return [
            'request' => $this->request,
            'response' => $this->response,
        ];
    }

    /**
     * Return the response as a JSON encoded string.
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Return the response as a string.
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Return the raw response string.
     * @return string
     */
    public function toRaw()
    {
        return $this->raw;
    }
}