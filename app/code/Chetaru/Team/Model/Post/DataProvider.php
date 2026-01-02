<?php

declare(strict_types=1);

namespace Chetaru\Team\Model\Post;

use Chetaru\Team\Model\Post;
use Chetaru\Team\Model\PostFactory;
use Chetaru\Team\Model\ResourceModel\Post as PostResource;
use Chetaru\Team\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Magento\Store\Model\StoreManagerInterface;

class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var array
     */
    private array $loadedData;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PostResource $resource
     * @param PostFactory $postFactory
     * @param RequestInterface $request
     * @param array $storeManager
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        private PostResource $resource,
        private PostFactory $postFactory,
        private RequestInterface $request,
        private StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $post = $this->getCurrentPost();

        $this->loadedData = $post->getData();

        $items = $this->collection->getItems();

        foreach ($items as $model) {
            if ($model->getFeaturedImage()) {
                $m[0]['name'] = basename($model->getFeaturedImage());
                $m[0]['url'] = $model->getFeaturedImage();
                $this->loadedData['featured_image'] = $m;
            }
        }
       //echo "<pre>";  print_r($this->loadedData); echo "</pre>";
      
       // echo '<script> console.log('. json_encode($this->loadedData) .') </script>';
        $this->loadedData[$post->getId()] = $this->loadedData;

        return $this->loadedData;
    }

    /**
     * @return Post
     */
    private function getCurrentPost(): Post
    {
        $postId = $this->getPostId();
        $post = $this->postFactory->create();
        if (!$postId) {
            return $post;
        }

        $this->resource->load($post, $postId);

        return $post;
    }

    /**
     * @return int
     */
    private function getPostId(): int
    {
        return (int) $this->request->getParam($this->getRequestFieldName());
    }
}
