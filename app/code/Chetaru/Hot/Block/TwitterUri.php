<?php
namespace Chetaru\Hot\Block;
class TwitterUri extends \Magento\Framework\View\Element\Template
{
	 protected $_varFactory;

    public function __construct(
        \Magento\Variable\Model\VariableFactory $varFactory,
        \Magento\Framework\View\Element\Template\Context $context)
    {
        $this->_varFactory = $varFactory;
        parent::__construct($context);
    }

    public function getTwitterUrl() {
        $var = $this->_varFactory->create();
        $var->loadByCode('twitter_url');
        return $var->getValue('text');
    }
	
}