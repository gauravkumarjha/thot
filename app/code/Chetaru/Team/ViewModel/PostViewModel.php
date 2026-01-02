<?php

declare(strict_types=1);

namespace Chetaru\Team\ViewModel;

use Chetaru\Team\Model\Post;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PostViewModel implements ArgumentInterface
{
    public function __construct(private UrlInterface $url) {}

    public function getPostUrl(Post $post): string
    {
        return $this->url->getBaseUrl() . 'team/' . $post->getData('url_key');
    }
}
