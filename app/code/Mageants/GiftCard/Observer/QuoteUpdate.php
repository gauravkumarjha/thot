<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCard\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Session;
use Mageants\GiftCard\Model\Giftquote;
use Mageants\GiftCard\Helper\Data;

/**
 * Quote update class for quote id store
 */
class QuoteUpdate implements ObserverInterface
{
    /**
     * @var Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @var Mageants\GiftCard\Model\Giftquote
     */
    protected $_giftquote;

    /**
     * @var Mageants\GiftCard\Helper\Data
     */
    protected $_helperData;

    /**
     * @param Session $catalogSession
     * @param Giftquote $giftquote
     * @param Data $helperData
     */
    public function __construct(
        Session $catalogSession,
        Giftquote $giftquote,
        Data $helperData
    ) {
        $this->_catalogSession = $catalogSession;
        $this->_giftquote = $giftquote;
        $this->_helperData = $helperData;
    }
    
    /**
     * Execute and perform save gift for temporary
     *
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helperData->checkStatus()) {
            $giftCollection = $this->_giftquote->getCollection()->setOrder('id', 'DESC')->setPageSize('1');
            $csession = $this->_catalogSession->getGiftQuoteId();
            if (!empty($giftCollection->getData())) {
                $giftData = $giftCollection->getData()[0];
                $giftData['quote_id'] = $csession;
                $this->_giftquote->setData($giftData);
                $this->_giftquote->save();
            }
        }
    }
}
