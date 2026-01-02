<?php

namespace Mageplaza\Simpleshipping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

class ValidateAttributeRelation implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param ManagerInterface $messageManager
     */
    public function __construct(LoggerInterface $logger, ManagerInterface $messageManager)
    {
        $this->logger = $logger;
        $this->messageManager = $messageManager;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        $attribute1Value = $product->getData('shipping_charges_feature_enabl');
        $attribute2Value = $product->getData('weight_price');

        if ($attribute1Value == '1' && empty($attribute2Value)) {
            // Log the error
            $this->logger->error(__('Weight Price is required when Shipping Charges Feature is enabled.')->render());

            // Add error message to the session
            $this->messageManager->addErrorMessage(__('Weight Price is required when Shipping Charges Feature is enabled.'));

            // Optionally, redirect or take some other action
            return; // Prevent further processing if necessary
        }
    }
}
