<?php

declare(strict_types=1);

namespace Chetaru\Team\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class Index extends Action implements HttpGetActionInterface
{
    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Chetaru_Team::post');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Teams'));

        return $resultPage;
    }
}
