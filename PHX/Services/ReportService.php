<?php
namespace PHX\Services;

use PHX\Service;
use PHX\Response;

class ReportService extends Service {

    /**
     * Return all transactions for the given date range.
     * @param $start_date string yyyy-mm-dd
     * @param null|string $end_date yyyy-mm-dd
     * @return Response
     */
    public function transactions($start_date,$end_date=null)
    {
        return $this->post("reports/transactions", [
            'allocation_group' => 'Payment',
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }

    /**
     * Return all applications for the given date range.
     * @param $start_date string yyyy-mm-dd
     * @param null|string $end_date yyyy-mm-dd
     * @return Response
     */
    public function applications($start_date,$end_date=null)
    {
        return $this->post("reports/applications", compact('start_date','end_date'));
    }


    /**
     * Return all promotions for the given division ID.
     * @param $division_id string
     * @return Response
     */
    public function promotions ($division_id)
    {
        return $this->post("reports/promotions", compact('division_id'));
    }

}