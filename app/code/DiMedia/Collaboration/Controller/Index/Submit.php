<?php

namespace DiMedia\Collaboration\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\ResourceConnection;

class Submit extends Action
{
    protected $transportBuilder;
    protected $resource;

    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        ResourceConnection $resource
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->resource = $resource;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $connection = $this->resource->getConnection();
            $connection->insert('collaboration_form', [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'message' => $data['message']
            ]);

            // Email to Admin
            $this->sendEmail('gaurav@digitalimpressions.in', 'New Collaboration Request', $data);

            // Email to User
            $this->sendEmail($data['email'], 'Thank You for Your Request', $data);

            $this->messageManager->addSuccessMessage('Your request has been submitted.');
        }

        $this->_redirect('collaboration/index/index');
    }

    protected function sendEmail($to, $subject, $data)
    {
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('collaboration_email_template')
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => 1])
            ->setTemplateVars(['data' => $data])
            ->setFrom(['email' => 'Gaurav@digitalimpressions.in', 'name' => 'Gaurav Jha'])
            ->addTo($to)
            ->getTransport();

        $transport->sendMessage();
    }
}
