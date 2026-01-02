<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2017 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Megamenu\Block\Widget;

use Magento\Framework\View\Element\Template;

class contactUs extends \Magento\Contact\Block\ContactForm implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Magento\Contact\ViewModel\UserDataProvider
     */
    protected $_userDataProvider;

    /**
     * @param Template\Context $context
     * @param \Magento\Contact\ViewModel\UserDataProvider $userDataProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Contact\ViewModel\UserDataProvider $userDataProvider,
        array $data = []
    ) {
        $data["view_model"] = $userDataProvider;
        parent::__construct($context, $data);
        $this->_userDataProvider = $userDataProvider;
    }

    /**
     * @inheritdoc
     */
	protected function _beforeToHtml()
	{
		$customTemplate = $this->getConfig("custom_template");
        if (!empty($customTemplate) && strpos(".phtml", $customTemplate) > 0) {
            $this->setTemplate($customTemplate);
        } else {
            $this->setTemplate("Ves_Megamenu::widget/contact_form.phtml");
        }
        $this->setData('view_model', $this->_userDataProvider);
		return parent::_beforeToHtml();
	}

    /**
     * Get config
     *
     * @param string $key
     * @param mixed|string|int|null $default
     * @return mixed|string|int|null
     */
	public function getConfig($key, $default = '')
	{
		if($this->hasData($key) && $this->getData($key)) {
			return $this->getData($key);
		}
		return $default;
	}

    /**
     * Get view model
     *
     * @return \Magento\Contact\ViewModel\UserDataProvider
     */
    public function getViewModel()
    {
        return $this->_userDataProvider;
    }
}
