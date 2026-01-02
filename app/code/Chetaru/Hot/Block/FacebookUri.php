<?php
namespace Chetaru\Hot\Block;
class FacebookUri extends \Magento\Framework\View\Element\Template
{
	 protected $_varFactory;

    public function __construct(
        \Magento\Variable\Model\VariableFactory $varFactory,
        \Magento\Framework\View\Element\Template\Context $context)
    {
        $this->_varFactory = $varFactory;
        parent::__construct($context);
    }

    public function getFacebookUrl() {
        $var = $this->_varFactory->create();
        $var->loadByCode('facebook_url');
        return $var->getValue('text');
    }
	
}