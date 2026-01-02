<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Backend system config module version field renderer
 */
class Version extends Field
{
    const MODULE_NAME = 'Magecomp_Gstcharge';
    /**
     * @var ConfigInterface
     */
    protected $moduleList;

    /**
     * Version constructor.
     * @param Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        array $data = []
    ) {
        $this->moduleList = $moduleList;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $this->moduleList
                ->getOne(self::MODULE_NAME)['setup_version'] .
            ' <a href="https://magecomp.com/magento-2-indian-gst.html" target="_blank" class="changelog module-version popup" id="custom-release-notes">' .
            __('Check Latest Version') .
            '</a>';

        $element->setData('text', $html);
        return parent::_getElementHtml($element);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderScopeLabel(AbstractElement $element)
    {
        return '';
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderInheritCheckbox(AbstractElement $element)
    {
        return '';
    }
}
