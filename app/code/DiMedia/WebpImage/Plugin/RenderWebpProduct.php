<?php

namespace DiMedia\WebpImage\Plugin;

use Psr\Log\LoggerInterface;

class RenderWebpProduct
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function afterGetUrl(\Magento\Catalog\Model\Product\Image\UrlBuilder $subject, $result)
    {
        $ext = strtolower(pathinfo($result, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $webpUrl = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $result);

            $relPath = parse_url($webpUrl, PHP_URL_PATH);
            $absPath = BP . '/pub' . $relPath;

            $origRel = parse_url($result, PHP_URL_PATH);
            $origAbs = BP . '/pub' . $origRel;

            $this->logger->info("Product URL check: " . $origAbs);

            if (!file_exists($absPath) && file_exists($origAbs)) {
                $this->createWebp($origAbs, $absPath, $ext);
                $this->logger->info("Created WebP: " . $absPath);
            }

            if (file_exists($absPath)) {
                return $webpUrl;
            }
        }

        return $result;
    }

    private function createWebp($src, $dest, $ext)
    {
        try {
            if ($ext === 'png') {
                $img = @imagecreatefrompng($src);
            } else {
                $img = @imagecreatefromjpeg($src);
            }

            if ($img) {
                imagepalettetotruecolor($img);
                imagewebp($img, $dest, 80);
                imagedestroy($img);
            }
        } catch (\Exception $e) {
            $this->logger->error("WebP Conversion Error: " . $e->getMessage());
        }
    }
}
