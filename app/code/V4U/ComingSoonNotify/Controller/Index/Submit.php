<?php

namespace V4U\ComingSoonNotify\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use V4U\ComingSoonNotify\Model\ContactFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Submit extends Action
{
    protected $contactFactory;
    protected $formKeyValidator;
    protected $request;
    protected $transportBuilder;
    protected $storeManager;
    protected $scopeConfig;

    public function __construct(
        Context $context,
        ContactFactory $contactFactory,
        FormKeyValidator $formKeyValidator,
        Http $request,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->contactFactory   = $contactFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->request          = $request;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager     = $storeManager;
        $this->scopeConfig      = $scopeConfig;
    }

    public function execute()
    {
        // die();
        // if (!$this->formKeyValidator->validate($this->getRequest())) {
        //     $this->messageManager->addErrorMessage(__('Invalid form key.'));
        //     return $this->_redirect('comingsoon/index/index');
        // }

        $data = [
            'name'     => trim((string)$this->request->getParam('name')),
            'email'    => trim((string)$this->request->getParam('email')),
            'phone'    => trim((string)$this->request->getParam('phone')),
            'message'  => trim((string)$this->request->getParam('message')),
            'page_url' => (string)$this->request->getParam('page_url')
                ?: (string)$this->request->getServer('REQUEST_URI')
        ];

        if (!$data['name'] || !$data['email']) {
            $this->messageManager->addErrorMessage(__('Please fill required fields.'));
            return $this->_redirect('comingsoon/index/index');
        }

        try {
            // Save
            $model = $this->contactFactory->create();
            $model->setData($data)->save();

            // Email to admin
            try {
                // Save to DB
                $model = $this->contactFactory->create();
                $model->setData($data)->save();

                // Prepare email data
                $storeId = $this->storeManager->getStore()->getId();
                $adminEmail = "gaurav@digitalimpressions.in";  // ✅ Custom
                $adminName  = "Admin"; // आपका नाम भी डाल सकते हैं

                // ✅ Make sure email template exists in etc/email_templates.xml
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier('v4u_comingsoon_email_template')
                    ->setTemplateOptions([
                        'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId
                    ])
                    ->setTemplateVars([
                        'data' => $data   // array OR object allowed
                    ])
                    ->setFrom([
                        'name'  => 'The House of Things - Website Notification',
                        'email' => 'concierge@thehouseofthings.com' // बेहतर (कोई भी valid email)
                    ])
                    ->addTo($adminEmail, $adminName)
                    ->getTransport();
                $transport->getMessage()
                    ->setSubject('New Coming Soon Request from ' . $data['name']);
                $transport->sendMessage();

                $userEmail = $data['email'];
                $userName  = $data['name'];

                $userTransport = $this->transportBuilder
                    ->setTemplateIdentifier('v4u_comingsoon_user_email_template') // ✅ user template ID
                    ->setTemplateOptions([
                        'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId
                    ])
                    ->setTemplateVars(['data' =>$data])
                    ->setFrom([
                        'name'  => 'The House of Things - Website Notification',
                        'email' => 'concierge@thehouseofthings.com' // बेहतर (कोई भी valid email)
                    ])
                    ->addTo($userEmail, $userName)
                    ->getTransport();
                $userTransport->getMessage()
                    ->setSubject('Thank you for Interest New Coming Soon');
                $userTransport->sendMessage();


                //$this->messageManager->addSuccessMessage(__('Thanks! We will notify you soon.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Mail sending failed: ' . $e->getMessage()));
            }


            $this->messageManager->addSuccessMessage(__('Thanks! We will notify you soon.'));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage(__('Unable to submit at the moment.'));
        }

        return $this->_redirect('comingsoon/index/index');
    }
}
