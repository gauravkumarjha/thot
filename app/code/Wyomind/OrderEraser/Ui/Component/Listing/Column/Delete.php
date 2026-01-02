<?php
/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\OrderEraser\Ui\Component\Listing\Column;

/**
 * Render column block in the order grid
 * 
 */
class Delete extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\Authorization
     */
    public $authorization = null;

    /**
     * @var \Magento\Framework\UrlInterface|null
     */
    public $urlInterface = null;

    /**
     * Delete constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Authorization $authorization
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Authorization $authorization,
        \Magento\Framework\UrlInterface $urlInterface,
        array $components = [],
        array $data = []
    ) {
    
        $this->urlInterface = $urlInterface;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->authorization = $authorization;
    }

    /**
     * Prepare component configuration
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if (false == $this->authorization->isAllowed('Wyomind_OrderEraser::delete')) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $html = "<a href='#' onclick='javascript:jQuery(this).parents(\"TD\").eq(0).off(\"click\");";
                $html .= "OrderEraser.delete(\"";
                $html .= $this->urlInterface->getUrl('ordereraser/orders/delete', ['selected' => $item['entity_id']]);
                $html .= "\");";
                $html .= "'>Delete</a></span>";
                $item[$this->getData('name')] = $html;
            }
        }
        return $dataSource;
    }
}
