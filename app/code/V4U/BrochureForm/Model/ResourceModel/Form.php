<?php

namespace V4U\BrochureForm\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Form extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('v4u_brochure_form', 'entity_id');
    }
}
