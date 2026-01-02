<?php

declare(strict_types=1);

namespace Chetaru\Article\ViewModel;

use Chetaru\Article\Model\Post;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PostViewModel implements ArgumentInterface
{
    public function __construct(private UrlInterface $url) {}

    public function getPostUrl(Post $post): string
    {
        return $this->url->getBaseUrl() . 'article/' . $post->getData('url_key');
    }
}
