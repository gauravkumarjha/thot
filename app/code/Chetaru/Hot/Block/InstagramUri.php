<?php
namespace Chetaru\Hot\Block;
class InstagramUri extends \Magento\Framework\View\Element\Template
{
	 protected $_varFactory;

    public function __construct(
        \Magento\Variable\Model\VariableFactory $varFactory,
        \Magento\Framework\View\Element\Template\Context $context)
    {
        $this->_varFactory = $varFactory;
        parent::__construct($context);
    }

    public function getInstagramUrl() {
        $var = $this->_varFactory->create();
        $var->loadByCode('instagram_url');
        return $var->getValue('text');
    }
	
}