<?php
namespace PHX;

class Model
{
    protected $attributes = [];

    public function __construct($response)
    {
        $this->fill($response);
    }

    /**
     * Fill the models attributes.
     * @param $response
     */
    public function fill($response)
    {
        $this->attributes = $response;
    }
}