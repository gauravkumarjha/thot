<?php

namespace DiMedia\ShiftItems\Controller\Cart;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use DiMedia\ShiftItems\Model\CartMigrator;

class MoveCart extends \Magento\Framework\App\Action\Action
{
    protected $cartMigrator;

    public function __construct(
        Context $context,
        CartMigrator $cartMigrator
    ) {
        parent::__construct($context);
        $this->cartMigrator = $cartMigrator;
    }

    /**
     * Move cart items from Store 1 to Store 2
     */
    public function execute()
    {
        try {
            $sourceStoreId = 1; // Store 1 ID
            $targetStoreId = 1; // Store 2 ID
            $newCurrency = 'USD'; // Desired currency

            $this->cartMigrator->migrateCartItemsWithCurrencyChange($sourceStoreId, $targetStoreId, $newCurrency);

            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $result->setData(['success' => true, 'message' => 'Cart items migrated successfully.']);
        } catch (\Exception $e) {
            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }

        return $result;
    }
}
