<?php

namespace V4U\BrochureForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use V4U\BrochureForm\Model\FormFactory;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Controller\Result\JsonFactory;

class Submit extends Action
{
    protected $formFactory;
    protected $transportBuilder;
    protected $storeManager;
    protected $remoteAddress;
    protected $jsonFactory;

    public function __construct(
        Context $context,
        FormFactory $formFactory,
        TransportBuilder $transportBuilder,
        RemoteAddress $remoteAddress,
        JsonFactory $jsonFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->formFactory = $formFactory;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->remoteAddress = $remoteAddress;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultJson->setData(['success' => false, 'message' => 'No data received']);
        }

        $data['ip_address'] = $this->remoteAddress->getRemoteAddress();
        $data['submitted_at'] = date('Y-m-d H:i:s');

        $model = $this->formFactory->create();
        $model->setData($data)->save();

        $storeId = $this->storeManager->getStore()->getId();

        // Send admin and user emails
        try {
            // Email to Admin
            $this->transportBuilder
                ->setTemplateIdentifier('brochure_form_admin_template')
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars(['data' => $data])
                ->setFrom(['email' => 'garuav@digitalimpressions', 'name' => 'Brochure'])
                ->addTo('garuav@digitalimpressions')
                ->getTransport()
                ->sendMessage();

            // Email to User
            $this->transportBuilder
                ->setTemplateIdentifier('brochure_form_client_template')
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars(['data' => $data])
                ->setFrom(['email' => 'garuav@digitalimpressions', 'name' => 'Brochure'])
                ->addTo($data['email'])
                ->getTransport()
                ->sendMessage();
        } catch (\Exception $e) {
            return $resultJson->setData(['success' => false, 'message' => 'Email failed: ' . $e->getMessage()]);
        }

        // Return JSON with PDF link
        return $resultJson->setData([
            'success' => true,
            'message' => 'Thank you for your request. Your brochure will download shortly.',
        ]);
    }
}
