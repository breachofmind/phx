<?php
namespace PHX\Services;

use PHX\Response;
use PHX\Service;
use PHX\Models\Debt;

class DebtService extends Service {


    /**
     * Get a debt object.
     * @return Debt
     * @throws \Exception
     */
    public function getObject($id)
    {
        $response = $this->get("debt/{$id}");
/*        if ($response->debt_id) {
            throw new \Exception("Debt ID '$id' not found");
        }*/
        return Debt::create($response);
    }

    /**
     * Return the transactions around this debt.
     * @param $id string debt id
     * @return Response
     */
    public function transactions($id)
    {
        return $this->post("debt/{$id}/transaction/search");

        // TODO, check for not implemented
    }


    /**
     * Process a payment.
     * @param string $id debt id
     * @param array $data
     * @return Response
     */
    public function pay($id,$data=[])
    {
        extract_args($data,[
            'payment_info' => [],
            'nickname' => null,
            'pay_account_id' => null,
            'amount' => null,
            'future_date' => null,
        ]);
        if (empty($data['payment_info'])) {
            return Response::error('Invalid Payment Information Specified', 1080);
        }
        // TODO, validate
        return $this->post("debt/{$id}/payment", $data);
    }

    /**
     * Return auto payments.
     * @param $id string debt id
     * @param bool|true $only_active
     * @return Response
     */
    public function autoPayments($id, $only_active=true)
    {
        return $this->post("debt/{$id}/autopayments/search", compact('only_active'));
    }

}