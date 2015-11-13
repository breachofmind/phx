<?php
namespace PHX;
use ArrayAccess;
use JsonSerializable;

class Model implements ArrayAccess, JsonSerializable
{
    protected $attributes = [];

    public function __construct($response)
    {
        $this->fill($response);
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

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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

    function jsonSerialize()
    {
        return $this->attributes;
    }
}