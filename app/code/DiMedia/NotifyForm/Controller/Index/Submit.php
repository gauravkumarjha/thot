<?php

namespace DiMedia\NotifyForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use DiMedia\NotifyForm\Model\FormFactory;
use DiMedia\NotifyForm\Model\ResourceModel\Form\CollectionFactory as FormCollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class Submit extends Action
{
    protected $jsonFactory;
    protected $formFactory;
    protected $formCollectionFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        FormCollectionFactory $formCollectionFactory,
        FormFactory $formFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->formFactory = $formFactory;
        $this->formCollectionFactory = $formCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                $this->validate($data);

                $existingForm = $this->formCollectionFactory->create()
                    ->addFieldToFilter('notify_email', $data['notify_email'])
                    ->getFirstItem();

                if ($existingForm->getId()) {
                    $result->setData([
                        'success' => false,
                        'errors' => "OHH HO!",
                        'message' => __("Your subscription already exists! You'll be notified when our exclusive collection goes live.")
                    ]);
                    return $result;
                   // throw new LocalizedException(__("Your subscription already exists! You'll be notified when our exclusive collection goes live."));
                } 

                $formModel = $this->formFactory->create();
                $formModel->setData($data);
                $formModel->save();

                $result->setData([
                    'success' => true,
                    'message' => __("Thank you for subscribing! You' ll be notified when our exclusive collection goes live.")
                ]);
            } catch (LocalizedException $e) {
                $result->setData([
                    'success' => false,
                    'errors'=> "Error",
                    'message' => $e->getMessage()
                ]);
            } catch (\Exception $e) {
                $result->setData([
                    'success' => false,
                    'message' => __('Unable to submit your data. Please try again later.')
                ]);
            }
        } else {
            $result->setData([
                'success' => false,
                'message' => __('Invalid form data.')
            ]);
        }
        return $result;
    }

    protected function validate($data)
    {
        if (empty($data['notify_name'])) {
            throw new LocalizedException(__('Name is required.'));
        }

        if (empty($data['notify_number']) || !preg_match('/^[0-9]{8,15}$/', $data['notify_number'])) {
            throw new LocalizedException(__('A valid 8 to 10 digit Mobile Number is required.'));
        }

        if (empty($data['notify_email']) || !filter_var($data['notify_email'], FILTER_VALIDATE_EMAIL)) {
            throw new LocalizedException(__('A valid Email Address is required.'));
        }
    }
}
