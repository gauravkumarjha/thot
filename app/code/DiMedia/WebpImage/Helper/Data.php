<?php

namespace DiMedia\WebpImage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Psr\Log\LoggerInterface;

class Data extends AbstractHelper
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->logger = $logger;
    }

    /**
     * Convert image to WebP format
     */
    public function createWebp($src, $dest, $ext, $quality = 80)
    {
        try {
            $img = null;

            if ($ext === 'png') {
                $img = @imagecreatefrompng($src);
                if ($img) {
                    imagepalettetotruecolor($img);
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                }
            } elseif (in_array($ext, ['jpg', 'jpeg'])) {
                $img = @imagecreatefromjpeg($src);
                if ($img) {
                    imagepalettetotruecolor($img);
                }
            }

            if ($img) {
                if (!imagewebp($img, $dest, $quality)) {
                    $this->logger->error("WebP Conversion Failed: Cannot save $dest");
                }
                imagedestroy($img);
            } else {
                $this->logger->error("WebP Conversion Failed: Cannot read source $src");
            }
        } catch (\Exception $e) {
            $this->logger->error("WebP Conversion Error: " . $e->getMessage());
        }
    }

    /**
     * Get WebP URL (convert if missing)
     */
    public function getWebpUrl($imageUrl, $quality = 80)
    {
        $ext = strtolower(pathinfo($imageUrl, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return $imageUrl; // not supported
        }

        $webpUrl = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $imageUrl);

        $relPath = parse_url($webpUrl, PHP_URL_PATH);
        $absPath = BP . '/pub' . $relPath;
        $absPath = str_replace('/pub/pub/', '/pub/', $absPath);

        $origRel = parse_url($imageUrl, PHP_URL_PATH);
        $origAbs = BP . '/pub' . $origRel;
        $origAbs = str_replace('/pub/pub/', '/pub/', $origAbs);

        // ✅ Check if WebP already exists
        if (file_exists($absPath)) {
            return $webpUrl;
        }

        // ✅ If original exists but WebP not created → convert
        if (file_exists($origAbs)) {
            $this->createWebp($origAbs, $absPath, $ext, $quality);

            if (file_exists($absPath)) {
                return $webpUrl; // return converted
            }
        }

        // ❌ fallback original
        return $imageUrl;
    }
}
