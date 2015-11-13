<?php
namespace PHX\Models;

use PHX\Model;
use PHX\Response;

class Location extends Model {

    /**
     * Print the full address.
     * @return string
     */
    public function address()
    {
        $string = $this->address_1.", ";
        $string.= $this->address_2 ? $this->address_2." " : null;
        $string.= $this->city.", ".$this->state." ".$this->zip;
        return $string;
    }

}