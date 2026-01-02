<?php

namespace V4U\CmsWatcher\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;


class CmsBlockSaveBefore implements ObserverInterface
{
    /** @var Registry */
    private $registry;


    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }


    public function execute(Observer $observer)
    {
        try {
            $block = $observer->getEvent()->getObject();


            $oldContent = null;
            if ($block->getId()) {
                $orig = $block->getOrigData();
                if (!empty($orig['content'])) {
                    $oldContent = $orig['content'];
                }
            }


            $key = 'v4u_cms_old_content_' . ($block->getId() ?: md5(uniqid('v4u', true)));
            // register only if not already registered
            if ($this->registry->registry($key) === null) {
                $this->registry->register($key, $oldContent);
            }


            $block->setData('v4u_old_content_key', $key);
        } catch (\Throwable $e) {
            // do not block save
        }
    }
}
