<?php

declare(strict_types=1);

namespace Chetaru\Edit\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;

class Index implements HttpGetActionInterface
{
    public function __construct(private PageFactory $pageFactory)
    {}

    public function execute(): ResultInterface
    {
        $page = $this->pageFactory->create();
       // $page->getConfig()->getTitle()->set(__('Inedit'));
        $page->getConfig()->getTitle()->set(__('Curated Spaces: Modern to Traditional Home Edits'));
        $page->getConfig()->setMetaTitle("Curated Spaces: Modern to Traditional Home Edits");
        $page->getConfig()->setDescription("Discover well chosen areas that are suited to your taste, whether it be minimalist or elegant, traditional or modern.");

        return $page;
    }
}
