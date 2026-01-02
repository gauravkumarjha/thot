<?php

namespace Mageplaza\Simpleshipping\Plugin\Checkout;

use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Quote\Model\Quote;

class AddCurrencySymbolToTotals
{
    public function afterGetTotals(
        \Magento\Checkout\Model\TotalsInformationManagement $subject,
        $totals
    ) {
        // Add currency symbol to the totals response
        $quote = $totals->getQuote();
        $currencySymbol = $quote->getStore()->getBaseCurrency()->getCurrencySymbol();

        $totals->setData('currency_symbol', $currencySymbol);

        return $totals;
    }
}
