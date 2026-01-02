<?php

namespace DiMedia\Utility\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Submitfreshwork extends Action
{
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $email = $this->getRequest()->getParam('email');
        $firstname = $this->getRequest()->getParam('firstname');
        $lastname = $this->getRequest()->getParam('lastname');

        if (!$email || !$firstname || !$lastname) {
            return $result->setData([
                'success' => false,
                'error' => 'Missing required parameters'
            ]);
        }

        $data = [
            "unique_identifier" => [
                "emails" => $email
            ],
            "contact" => [
                "first_name" => $firstname,
                "last_name" => $lastname
            ]
        ];

        $ch = curl_init('https://thehouseofthings-org.myfreshworks.com/crm/sales/api/contacts/upsert');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Token token=t6cbmvG6-1jolqKsJNb4eg',
            'Content-Type: application/json'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $result->setData([
                'success' => true,
                'response' => json_decode($response, true)
            ]);
        } else {
            return $result->setData([
                'success' => false,
                'error' => 'API request failed',
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $response
            ]);
        }
    }
}
