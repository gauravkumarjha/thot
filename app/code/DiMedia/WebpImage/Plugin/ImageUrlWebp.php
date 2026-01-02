<?php

namespace DiMedia\WebpImage\Plugin;

use Psr\Log\LoggerInterface;
class ImageUrlWebp
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public function afterGetUrl(\Magento\Catalog\Model\Product\Image\UrlBuilder $subject, $result)
    {
        $ext = strtolower(pathinfo($result, PATHINFO_EXTENSION));

        // Check only jpg/png images
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $webpUrl = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $result);

            // Absolute path for existence check
            $relativePath = parse_url($webpUrl, PHP_URL_PATH);
            $absPath = BP . '/pub' . $relativePath;
            $this->logger->info("frontend URL: " . $webpUrl);
            if (file_exists($absPath)) {
                return $webpUrl; // ✅ Serve WebP
            }
        }

        return $result; // ❌ fallback jpg/png
    }
}
