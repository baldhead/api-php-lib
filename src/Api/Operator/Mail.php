<?php
// Copyright 1999-2019. Plesk International GmbH.

namespace PleskX\Api\Operator;
use PleskX\Api\Struct\Mail as Struct;

class Mail extends \PleskX\Api\Operator
{

    /**
     * @param string $name
     * @param integer $siteId
     * @param boolean $mailbox
     * @param string $password
     * @return Struct\Info
     */
    public function create($name, $siteId, $mailbox = false, $password = '')
    {
        $packet = $this->_client->getPacket();
        $info = $packet->addChild($this->_wrapperTag)->addChild('create');

        $filter = $info->addChild('filter');
        $filter->addChild('site-id', $siteId);
        $mailname = $filter->addChild('mailname');
        $mailname->addChild('name', $name);
        if ($mailbox) {
            $mailname->addChild('mailbox')->addChild('enabled', 'true');
        }
        if (!empty($password)) {
            $mailname->addChild('password')->addChild('value', $password);
        }

        $response = $this->_client->request($packet);
        return new Struct\Info($response->mailname);
    }

    /**
     * @param string $field
     * @param integer|string $value
     * @param integer $siteId
     * @return bool
     */
    public function delete($field, $value, $siteId)
    {
        $packet = $this->_client->getPacket();
        $filter = $packet->addChild($this->_wrapperTag)->addChild('remove')->addChild('filter');
        $filter->addChild('site-id', $siteId);
        $filter->addChild($field, $value);
        $response = $this->_client->request($packet);
        return 'ok' === (string)$response->status;
    }

     /**
     * @param string $field
     * @param integer|string $value
     * @return array
     */
    public function get($field, $value)
    {
        $packet = $this->_client->getPacket();
        $getTag = $packet->addChild($this->_wrapperTag)->addChild('get_info');

        $filterTag = $getTag->addChild('filter');
        if (!is_null($field)) {
            $filterTag->addChild($field, $value);
        }

        $response = $this->_client->request($packet, \PleskX\Api\Client::RESPONSE_FULL);

        $items = [];
        foreach ($response->xpath('//result/mailname') as $xmlResult) {
            $items[] = (string) $xmlResult->name;
        }

        return $items;
    }

}
