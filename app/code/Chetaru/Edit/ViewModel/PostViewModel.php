<?php

declare(strict_types=1);

namespace Chetaru\Edit\ViewModel;

use Chetaru\Edit\Model\Post;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PostViewModel implements ArgumentInterface
{
    public function __construct(private UrlInterface $url) {}

    public function getPostUrl(Post $post): string
    {
        return $this->url->getBaseUrl() . 'inedit/index/view/' . $post->getData('url_key');
    }
}
