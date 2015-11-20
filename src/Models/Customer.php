<?php
namespace PHX\Models;

use PHX\Model;
use PHX\Response;
use PHX\Collection;

class Customer extends Model {

    /**
     * Attributes that can be synced.
     * @var array
     */
    protected $fillable = [
        'name_prefix',
        'name_first',
        'name_middle',
        'name_last',
        'name_suffix',
        'date_of_birth',
        'addresses',
        'phones',
        'emails',
        'sticky_note',
        'attributes'
    ];

    /**
     * Returns the security question id and text.
     * @return Response
     */
    public function question()
    {
        $q = $this->phx->customer->securityQuestion($this->login_name);
        return $q('0');
    }

    /**
     * Return the ID field.
     * @return int
     */
    public function id()
    {
        return (int)$this->customer_id;
    }

    /**
     * Shortcut for setting the customer password.
     * @param $password string
     * @param null $question_id int
     * @param null $answer string
     * @return Response
     */
    public function setLogin($password,$question_id=null,$answer=null)
    {
        return $this->phx->customer->setLogin($this->id(),$password,$question_id,$answer);
    }


    /**
     * Return a collection of all Debt objects for this customer.
     * @return Collection
     */
    public function getDebts()
    {
        $debts = new Collection();
        foreach ((array)$this->debts as $debt) {
            $debts->add( $this->phx->debts->getObject($debt->debt_id) );
        }
        return $debts;
    }

    /**
     * Return the total balance for this customer.
     * @return int
     */
    public function balance()
    {
        $balance = 0;
        foreach ($this->debts as $debt) {
            $balance+=$debt->balance;
        }
        return $balance;
    }

    /**
     * Updates Customer Information (partial accepted)
     * @param $params array
     * @return boolean|string
     */
    public function update($params=[])
    {
        $id = $this->id();
        return $this->phx->system->put("customer/$id", $params);
    }

    /**
     * Sync the current state of this model with the database.
     * @return boolean|string
     */
    public function sync()
    {
        return $this->update( $this->getFillableFields($this->attributes) );
    }



}