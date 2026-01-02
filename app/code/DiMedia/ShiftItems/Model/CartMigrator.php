<?php

namespace DiMedia\ShiftItems\Model;

use Magento\Framework\App\ResourceConnection;

class CartMigrator
{
    protected $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function migrateCartItemsWithCurrencyChange($sourceStoreId, $targetStoreId, $newCurrency)
    {
        $connection = $this->resourceConnection->getConnection();

        // Load items from Store 1
        $query = $connection->select()
            ->from(['item' => 'quote_item'])
            ->where('store_id = ?', $sourceStoreId);

        $cartItems = $connection->fetchAll($query);

        // Insert into Store 2 with updated currency
        foreach ($cartItems as $item) {
            unset($item['item_id']); // Unset auto-increment ID
            $item['store_id'] = $targetStoreId;
            $connection->insert('quote_item', $item);
        }
    }
}

