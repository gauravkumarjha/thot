<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCard\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Stdlib\CookieManagerInterface;

class CartIndexLoadObserver implements ObserverInterface
{
    /**
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @param MessageManager $messageManager
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        MessageManager $messageManager,
        CookieManagerInterface $cookieManager
    ) {
        $this->messageManager = $messageManager;
        $this->cookieManager = $cookieManager;
    }

    /**
     * Execute observer method
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $errorRedirectCookie = $this->cookieManager->getCookie('gift_validation_error_message');
        if ($errorRedirectCookie) {
            $this->messageManager->addError($errorRedirectCookie);
        }
    }
}
