<?php
namespace PHX\Services;

use PHX\Service;
use PHX\Response;

class DocumentService extends Service {


    /**
     * Return a document object.
     * @param $id string
     * @return Response
     */
    public function object($id)
    {
        return $this->get("document/{$id}");
    }

    /**
     * Get the contents of a document.
     * @param $id string
     * @return Response
     */
    public function content($id)
    {
        return $this->get("document/{$id}/content");
    }

    /**
     * Save a document.
     * @param $debtId string
     * @param $clientId string
     * @param $file_content string
     * @param $file_name string
     * @return Response
     */
    public function save($debtId,$clientId,$file_content,$file_name)
    {
        return $this->put("document/save/{$debtId}/{$clientId}", compact('file_content','file_name'));
    }

}