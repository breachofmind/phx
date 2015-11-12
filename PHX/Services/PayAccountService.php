<?php
namespace PHX\Services;

use PHX\Response;
use PHX\Service;

class PayAccountService extends Service {


    /**
     * Return pay accounts for the given debt id.
     * @param $id string debt id
     * @param bool|false $only_active
     * @return Response
     */
    public function search($id, $only_active=false)
    {
        return $this->post("/debt/{$id}/payaccounts/search", compact('only_active'));
    }


    /**
     * Update a pay account.
     * @param $id debt id
     * @param $acct
     * @param array $data
     * @return Response
     */
    public function update($id,$acct,$data=[])
    {
        extract_args($data, [
            'nickname' => '',
            'auto_pay_active' => null,
            'active' => null,
        ]);
        return $this->put("debt/{$id}/payaccounts/{$acct}", $data);
    }

    /**
     * Create a new payaccount.
     * @param $id string debt id
     * @param array $data
     * @return Response
     */
    public function create($id, $data=[])
    {
        // TODO validate form data
        return $this->post("debt/{$id}/payaccounts/create", $data);
    }


    /**
     * Change the recurring pay account.
     * @param $id
     * @param $acct
     * @param array $data
     * @return Response|string
     */
    public function changeRecurringPayAccount($id, $acct, $data=[])
    {
        extract_args($data, [
            'pay_account_id'    => $acct,
            'nickname'          => 'Auto Monthly',
            'active'            => true,
            'replace_existing'  => true
        ]);

        if (!$acct) {
            $created = $this->create($data);
            if (!$created->pay_account_id) {
                return Response::error('Failed to Create Requested Pay Account', 1080);
            }
            $data['pay_account_id'] = $created->pay_account_id;
        }

        return $this->post("debt/{$id}/autopayments/create", $data);
    }


}