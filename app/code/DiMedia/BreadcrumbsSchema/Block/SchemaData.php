<?php

namespace DiMedia\BreadcrumbsSchema\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class SchemaData extends Template
{
    protected $categoryFactory;
    protected $registry;
    protected $urlInterface;

    public function __construct(
        Template\Context $context,
        CategoryFactory $categoryFactory,
        Registry $registry,
        UrlInterface $urlInterface,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->registry = $registry;
        $this->urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    public function getBreadcrumbs()
    {
        $breadcrumbs = [];
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => $this->urlInterface->getBaseUrl()
        ];
        $position = 2;
        $category = $this->registry->registry('current_category');
        if ($category) {
            $path = $category->getPath();
            $pathIds = explode('/', $path);
          

            foreach ($pathIds as $categoryId) {
                if ($categoryId == 1) { // Skipping the root category
                    continue;
                }

                $category = $this->categoryFactory->create()->load($categoryId);
                if($category->getName() == "Default Category") continue;
                if ($category->getId()) {
                    $breadcrumbs[] = [
                        '@type' => 'ListItem',
                        'position' => $position,
                        'name' => $category->getName(),
                        'item' => $category->getUrl()
                    ];
                    $position++;
                }
            }
        }

        $product = $this->registry->registry('current_product');
        if ($product) {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $product->getName(),
                'item' => $product->getProductUrl()
            ];
        }
        $path = $this->urlInterface->getCurrentUrl();
        $pathIds = explode('/', $path); 
    
        if(in_array("inedit", $pathIds) && end($pathIds)!= "inedit") {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => "Inedit",
                'item' => $this->urlInterface->getBaseUrl(). "/inedit"
            ];
             $position++;
        } else if(in_array("makers", $pathIds) && end($pathIds) != "makers") {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => "Makers",
                'item' => $this->urlInterface->getBaseUrl() . "/makers"
            ];
            $position++;
        }
        $currentPageTitle = $this->pageConfig->getTitle()->get();
        if ($currentPageTitle && !$category && !$product) {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $currentPageTitle,
                'item' => $this->urlInterface->getCurrentUrl()
            ];
        }



        return $breadcrumbs;
    }

    public function getBreadcrumbsJson()
    {
     
       
        if($this->urlInterface->getBaseUrl() != $this->urlInterface->getCurrentUrl()) {
        $breadcrumbs = [
            '@context' => 'http://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $this->getBreadcrumbs()
        ];

        return json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            return '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "The House of Things",
  "url": "https://thehouseofthings.com/",
  "logo": "https://thehouseofthings.com/pub/media/logo/stores/1/the-house-of-thing-logo.png",
  "sameAs": [
    "https://www.facebook.com/thehouseofthings/",
    "https://www.instagram.com/thehouseofthings",
    "https://www.linkedin.com/company/the-house-of-things",
    "https://twitter.com/_houseofthings"
  ]
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FurnitureStore",
  "name": "The House of Things",
  "image": "https://thehouseofthings.com/pub/media/logo/stores/1/the-house-of-thing-logo.png",
  "url": "https://thehouseofthings.com/",
  "telephone": "08003011110",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Road, Bedla, Sapetiya, Udaipur, Rajasthan",
    "addressLocality": "Udaipur",
    "postalCode": "313011",
    "addressCountry": "IN"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 24.5880,
    "longitude": 73.6904
  },
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday"
    ],
    "opens": "10:30",
    "closes": "18:00"
  },
  "sameAs": [
    "https://www.facebook.com/thehouseofthings",
    "https://www.instagram.com/thehouseofthings",
    "https://twitter.com/_houseofthings"
  ]
}
</script>';
        }
    }
}
