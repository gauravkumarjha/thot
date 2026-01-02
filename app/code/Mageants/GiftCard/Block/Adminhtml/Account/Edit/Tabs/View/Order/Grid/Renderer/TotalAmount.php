<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCard\Block\Adminhtml\Account\Edit\Tabs\View\Order\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Backend\Model\Session;
use Magento\Directory\Model\CurrencyFactory;

class TotalAmount extends AbstractRenderer
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @param Session $session
     * @param CurrencyFactory $currencyFactory
     */
    public function __construct(
        Session $session,
        CurrencyFactory $currencyFactory
    ){
        $this->session = $session;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * Render Data
     *
     * @param DataObject $row
     * @return void
     */
    public function render(DataObject $row)
    {
        $orderId = $row->getData('increment_id');
        $giftCardValue = $this->session->getGiftCardValue();
        $giftBaseAmount = number_format((float)$row->getData('base_order_gift'), 2);
        $giftAmount = number_format((float)$row->getData('order_gift'), 2);
        $giftAmountRefund = $row->getData('gift_value_refund_status');

        $baseCurrencyCode = $row->getData('base_currency_code');
        $orderCurrencyCode = $row->getData('order_currency_code');
        $baseCurrencySymbole = $this->getCurrencySymbolByCode($baseCurrencyCode);
        $orderCurrencySymbole = $this->getCurrencySymbolByCode($orderCurrencyCode);

        $convertedAmount = '';
        if ($baseCurrencyCode != $orderCurrencyCode) {
            $convertedAmount = "[$orderCurrencySymbole"."$giftAmount]";
        }

        $giftCardOwnOrderId = $this->session->getGiftCardOwnOrderId();
        $html = '';
        if ($orderId == $giftCardOwnOrderId) {
            $html = '<span style="color: green;">' . $baseCurrencySymbole . $giftCardValue . '</span>';
            $html .= '<br><span style="color: green;">Credited</span>';
        } else {
            $html = '<span style="color: red;">' . $baseCurrencySymbole . $giftBaseAmount . '</span>';
            $html .= ($convertedAmount) ? '~<span>'.$convertedAmount.'</span>' : '';
            $html .= '<br><span style="color: red;">Debited</span>';
            if ($giftAmountRefund) {
                $html .= ' | <span style="color: green;">Refunded</span>';
            }
        }

        return $html;
    }

    /**
     * Get currency symbol by currency code
     *
     * @param string $currencyCode
     * @return string
     */
    protected function getCurrencySymbolByCode($currencyCode)
    {
        $currency = $this->currencyFactory->create()->load($currencyCode);
        return $currency->getCurrencySymbol();
    }
}
