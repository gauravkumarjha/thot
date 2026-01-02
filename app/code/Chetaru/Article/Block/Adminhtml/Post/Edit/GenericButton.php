<?php

declare(strict_types=1);

namespace Chetaru\Article\Block\Adminhtml\Post\Edit;


use Magento\Framework\UrlInterface;

class GenericButton
{
    public function __construct(
       private UrlInterface $url
    ) {}

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->url->getUrl($route, $params);
    }
}
