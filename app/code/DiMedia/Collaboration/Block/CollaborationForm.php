<?php

namespace DiMedia\Collaboration\Block;

use Magento\Framework\View\Element\Template;

class CollaborationForm extends Template
{
    protected $_formKey;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        $this->_formKey = $formKey;
        parent::__construct($context, $data);
    }

    public function getFormAction()
    {
        return $this->getUrl('collaborations/index/index'); // Define your form action URL
    }

    public function getFormKey()
    {
        return $this->_formKey->getFormKey();
    }
}
