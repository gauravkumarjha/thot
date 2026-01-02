<?php

declare(strict_types=1);

namespace Chetaru\Article\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Chetaru\Article\Model\ImageUploader;

class Edit extends Action implements HttpGetActionInterface
{
    public ImageUploader $imageUploader;

 
    
    public function execute(): Page
    {

        $pageResult = $this->createPageResult();
        $title = $pageResult->getConfig()->getTitle();
        $title->prepend(__('Articles'));
        $title->prepend(__('New Article'));

        return $pageResult;
    }

    private function createPageResult(): Page|ResultInterface
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
