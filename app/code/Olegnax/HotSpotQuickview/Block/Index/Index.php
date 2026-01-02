<?php
declare(strict_types=1);


namespace Olegnax\HotSpotQuickview\Block\Index;


use Exception;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\HotSpotQuickview\Helper\Helper;

class Index extends Template
{
    const AJAX_ATTR = 'block';
    /**
     * @var Http
     */
    protected $request;
    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var BlockFactory
     */
    protected $blockFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var FilterProvider
     */
    protected $filterProvider;
    protected $storeId;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Helper $helper
     * @param Http $request
     * @param BlockFactory $blockFactory
     * @param StoreManagerInterface $storeManager
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $helper,
        Http $request,
        BlockFactory $blockFactory,
        StoreManagerInterface $storeManager,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->request = $request;
        $this->helper = $helper;
        $this->blockFactory = $blockFactory;
        $this->storeManager = $storeManager;
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->helper->isEnabled()
            ? $this->request->getParam(static::AJAX_ATTR, '')
            : '';
    }

    /**
     * @param $blockId
     * @return string
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function getStaticBlockContent($blockId)
    {
        $store_id = $this->getStoreId();
        $block = $this->blockFactory->create()->setStoreId($store_id)->load($blockId);
        $content = '';
        if ($block) {
            $block_content = $block->getContent();
            if ($block_content) {
                $content = $this->getBlockTemplateProcessor($block_content, $store_id);
            }
        }

        return $content;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        if (!$this->storeId) {
            $this->storeId = $this->storeManager->getStore()->getId();
        }
        return $this->storeId;
    }

    /**
     * @param string $content
     * @param null $store_id
     * @return string
     */
    public function getBlockTemplateProcessor($content = '', $store_id = null)
    {
        if (empty($content)) {
            $content = '';
        }
        $content = trim($content);
        /** @var Template $filter */
        $filter = $this->filterProvider->getBlockFilter();
        if (!empty($store_id)) {
            $filter = $filter->setStoreId($store_id);
        }

        $content = $filter->filter($content);

        return $content;
    }

    public function showStaticBlockContent()
    {
        $id = $this->getId();
        if ($id) {
            return $this->getStaticBlockContent($id);
        }
        return '';
    }

//    public function toHtml()
//    {
//        $id = $this->getId();
//        $content = '';
//        if ($id) {
//            try {
//                $content = $this->getStaticBlockContent($id);
//            } catch (NoSuchEntityException $e) {
//                $content = '';
//            } catch (Exception $e) {
//                $content = '';
//            }
//        }
//
//        return $content;
//    }

}