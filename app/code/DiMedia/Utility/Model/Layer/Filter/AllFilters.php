<?php

namespace DiMedia\Utility\Model\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;

class AllFilters extends AbstractFilter
{
    /**
     * Modify filter logic to always show all filter options
     */
    protected function _getItemsData()
    {
        
        $itemsData = parent::_getItemsData(); // Get existing filters
        $selectedFilters = $this->getRequest()->getParam($this->_requestVar);

        if ($selectedFilters) {
            // Ensure all options are always available
            foreach ($this->getLayer()->getProductCollection()->getFacetedData($this->_requestVar) as $key => $data) {
              
                if (!array_key_exists($key, $itemsData)) {
                    $itemsData[] = [
                        'label' => $key,
                        'value' => $key,
                        'count' => $data['count'] ?? 1, // Fake count to keep it visible
                    ];
                }
            }
        }

        return $itemsData;
    }
}
