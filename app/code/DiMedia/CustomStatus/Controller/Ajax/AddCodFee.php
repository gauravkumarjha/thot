<?php

namespace DiMedia\CustomStatus\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

class AddCodFee extends Action
{
    protected $checkoutSession;
    protected $jsonFactory;
    protected $quoteRepository;
    protected $request;
    protected $logger;

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        JsonFactory $jsonFactory,
        \Magento\Framework\App\Request\Http $request,
        LoggerInterface $logger,
        QuoteRepository $quoteRepository
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->jsonFactory = $jsonFactory;
        $this->quoteRepository = $quoteRepository;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        $quote = $this->checkoutSession->getQuote();

        // Check if the quote is valid
        if (!$quote->getId()) {
            return $result->setData(['success' => false, 'message' => __('No quote found.')]);
        }

        // Get the selected payment method from the request or the quote
        $payment = $quote->getPayment();
        $postData = $this->request->getPostValue();
        $paymentMethod = isset($postData['payment_method']) ? $postData['payment_method'] : $payment->getMethod();

        // Define the additional fee
        $additionalFee = 1000;
        $existingFee = $quote->getData('cod_fee') ?: 0;

        if ($paymentMethod === 'cashondelivery') {
            // Check if the COD fee is already applied
            if ($existingFee == 0) {
                // Add COD fee to the grand total and base grand total
                $newGrandTotal = $quote->getGrandTotal() + $additionalFee;
                $newBaseGrandTotal = $quote->getBaseGrandTotal() + $additionalFee;

                // Log the new totals
                $this->logger->debug('Adding COD Fee. New Grand Total: ' . $newGrandTotal . ', Base Grand Total: ' . $newBaseGrandTotal);

                // Update totals in the quote
                $quote->setGrandTotal($newGrandTotal);
                $quote->setBaseGrandTotal($newBaseGrandTotal);
                $quote->setData('cod_fee', $additionalFee); // Save the fee in the quote
            }
        } else {
            // If a different payment method is selected, remove the COD fee
            if ($existingFee > 0) {
                // Subtract the COD fee from the grand total and base grand total
                $newGrandTotal = $quote->getGrandTotal() - $existingFee;
                $newBaseGrandTotal = $quote->getBaseGrandTotal() - $existingFee;

                // Log the new totals
                $this->logger->debug('Removing COD Fee. New Grand Total: ' . $newGrandTotal . ', Base Grand Total: ' . $newBaseGrandTotal);

                // Update totals in the quote
                $quote->setGrandTotal($newGrandTotal);
                $quote->setBaseGrandTotal($newBaseGrandTotal);
                $quote->setData('cod_fee', null); // Remove the saved fee
            }
        }

        // Ensure totals are recalculated by flagging totals for recalculation
        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();

        // Save the updated quote and update session
        try {
            $this->quoteRepository->save($quote);
            $this->checkoutSession->replaceQuote($quote); // Update session with new quote data
            $this->logger->debug('Quote saved successfully: ' . json_encode($quote->getData()));
        } catch (\Exception $e) {
            $this->logger->error('Error saving quote: ' . $e->getMessage());
            return $result->setData(['success' => false, 'message' => __('Unable to save quote.')]);
        }

        return $result->setData([
            'success' => true,
            'grand_total' => $quote->getBaseGrandTotal(),
            'message' => $paymentMethod === 'cashondelivery'
                ? __('Cash on Delivery Fee of ₹1000 has been added.')
                : __('Cash on Delivery Fee has been removed.'),
            'existingFee' => $existingFee,
            'paymentMethod' => $paymentMethod
        ]);
    }
}
