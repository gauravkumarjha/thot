<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Mageants\GiftCard\ViewModel\ViewHelperData;
use Magento\Catalog\Helper\Output;
use Mageants\GiftCard\Helper\Data;
use Magento\Framework\App\RequestInterface;


/**
 * RemoveBlock Observer before render block
 */
class GiftCategory implements ObserverInterface
{
    /**
     * @var Mageants\GiftCard\Helper\Data
     */
    public $data;

    /**
     * @var Mageants\GiftCard\ViewModel\ViewHelperData
     */
    public $helpdata;

    /**
     * @var Magento\Catalog\Helper\Output
     */
    public $output;

    /**
     * @var Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Magento\Framework\App\RequestInterface
     */
    public $request;

    /**
     * @param Registry       $registry
     * @param Output         $output
     * @param ViewHelperData $helpdata
     * @param Data           $data
     * @param RequestInterface $request
     */
    public function __construct(
        Registry $registry,
        Output $output,
        ViewHelperData $helpdata,
        Data $data,
        RequestInterface $request
    ) {
        $this->registry = $registry;
        $this->output = $output;
        $this->helpdata = $helpdata;
        $this->data = $data;
        $this->request = $request;
    }

    /**
     * To Set Gift category
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->request->getFullActionName() != 'catalog_category_view') {
            return;
        }
        $category = $this->registry->registry('current_category');

        if ($category) {
            if ($category->getName()=='Gift Card') {
                $layout = $observer->getLayout();
                $blocklist = $layout->getBlock('category.products.list');
                if ($blocklist) {
                    $blocklist->setTemplate('Mageants_GiftCard::product/list.phtml');
                }
            }
        }
    }
}
