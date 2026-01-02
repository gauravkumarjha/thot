<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCard\Block\Adminhtml;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;

/**
 * Class Admin DatePicker
 */
class DatePicker extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Return Html Element
     *
     * @param AbstractElement $element
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        /* Get configuration element */
        $html = $element->getElementHtml();

        /* Check datepicker set or not */
        if (!$this->_coreRegistry->registry('datepicker_loaded')) {
            $this->_coreRegistry->registry('datepicker_loaded', 1);
        }

        /* Add icon on datepicker */
        $html .= '<button type="button" style="display:none;" class="ui-datepicker-trigger '
          .'v-middle"><span>Select Date</span></button>';

        /* Add datepicker with element by jquery */
        $html .= '<script type="text/javascript">
            require(["jquery", "jquery/ui"], function (jq) {
                jq(document).ready(function () {
                    jq("#' . $element->getHtmlId() . '").datepicker( { dateFormat: "dd/mm/yy" } );
                    jq(".ui-datepicker-trigger").removeAttr("style");
                    jq(".ui-datepicker-trigger").click(function(){
                        jq("#' . $element->getHtmlId() . '").focus();
                    });
                });
            });
            </script>';
        /* return datepicker element */
        return $html;
    }
}
