<?php

namespace Dimedia\Utility\Model;

class Abandoned extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'dimedia_utility_abandoned';

    protected $_cacheTag = 'dimedia_utility_abandoned';

    protected $_eventPrefix = 'dimedia_utility_abandoned';

    protected function _construct()
    {
        $this->_init('DiMedia\Utility\Model\ResourceModel\Abandoned');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
