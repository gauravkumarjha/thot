<?php
namespace Chetaru\Hot\Block;
class AssitContactNumber extends \Magento\Framework\View\Element\Template
{
	 protected $_varFactory;

    public function __construct(
        \Magento\Variable\Model\VariableFactory $varFactory,
        \Magento\Framework\View\Element\Template\Context $context)
    {
        $this->_varFactory = $varFactory;
        parent::__construct($context);
    }

    public function getAssitContactNumber() {
        $var = $this->_varFactory->create();
        $var->loadByCode('assistance_contact_number');
        return $var->getValue('text');
    }
	
}
