<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the NekloEULA that is bundled with this package in the file LICENSE.txt.
 *
 * It is also available through the world-wide-web at this URL: http://store.neklo.com/LICENSE.txt
 *
 * Copyright (c)  Neklo (http://store.neklo.com/)
 */

namespace Neklo\ProductPosition\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Neklo\ProductPosition\Model\Config;
use Neklo\ProductPosition\Model\Json\Product as ProductJson;

class Page extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public const ADMIN_RESOURCE = 'Magento_Catalog::categories';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ProductJson
     */
    private $productJson;

    /**
     * @param Context $context
     * @param Config $config
     * @param ProductJson $productJson
     */
    public function __construct(
        Context $context,
        Config $config,
        ProductJson $productJson
    ) {
        $this->config = $config;
        $this->productJson = $productJson;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     *
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     *
     * @param RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Generate product data
     *
     * @return ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(): ResultInterface
    {
        $data = [];

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($this->config->isEnabled()) {
            $page = $this->getRequest()->getParam('page', 1);
            $count = $this->getRequest()->getParam('count', 20);

            $data = $this->productJson->getCollectionData($page, $count, true);
        }

        $result->setData($data);

        return $result;
    }
}
