<?php
namespace PHX;
use ArrayAccess;
use JsonSerializable;

class Model implements ArrayAccess, JsonSerializable
{
    /**
     * The PHX wrapper instance.
     * @var Wrapper
     */
    protected $phx;

    /**
     * Fields that are mass-assignable.
     * @var array
     */
    protected $fillable = [];

    /**
     * Array of attributes.
     * @var array
     */
    protected $attributes = [];

    public function __construct($response)
    {
        if ($response instanceof Response) {
            $response = $response->body();
        }
        $this->fill($response);
        $this->phx = Wrapper::$instance;
    }

    /**
     * Named constructor.
     * @param $response array|Response
     * @return mixed
     */
    public static function create($response)
    {
        $class = get_called_class();
        return new $class($response);
    }

    /**
     * Fill the models attributes.
     * @param $response
     * @return Model
     */
    public function fill($response)
    {
        foreach ($response as $prop=>$val) {
            $this->attributes[$prop] = $val;
        }
        return $this;
    }

    /**
     * Get an attribute.
     * @param $name string
     * @return null
     */
    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Set an attribute.
     * @param $name string
     * @param $value mixed
     * @return mixed
     */
    public function __set($name,$value)
    {
        return $this->attributes[$name] = $value;
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Implementation of jsonSerialize. Returns attributes to serialize.
     * @return array
     */
    function jsonSerialize()
    {
        return $this->attributes;
    }

    /**
     * Returns this object as a JSON-encoded string.
     * @return string
     */
    function __toString()
    {
        return json_encode($this->attributes);
    }

    /**
     * Modify the input params to just use fields that are fillable/mass-assignable.
     * @param $params array
     * @return array
     */
    protected function getFillableFields($params=[])
    {
        $out = [];
        foreach ($params as $key=>$value) {
            if (in_array($key,$this->fillable)) {
                $out[$key] = $value;
            }
        }
        return $out;
    }
}