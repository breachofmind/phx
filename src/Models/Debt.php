<?php
namespace PHX\Models;

use PHX\Collection;
use PHX\Model;
use PHX\Response;

class Debt extends Model {

    /**
     * Return the ID field.
     * @return int
     */
    public function id()
    {
        return (int)$this->debt_id;
    }


}