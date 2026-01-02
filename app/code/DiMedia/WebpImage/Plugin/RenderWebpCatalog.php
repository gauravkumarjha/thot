<?php

namespace DiMedia\WebpImage\Plugin;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\RequestInterface;
class RenderWebpCatalog
{
    private $logger;
    private $request;

    public function __construct(LoggerInterface $logger, RequestInterface $request)
    {
        $this->logger = $logger;
        $this->request = $request;
    }

    public function afterGetUrl(\Magento\Catalog\Model\View\Asset\Image $subject, $result)
    {
        $this->logger->info("pagename: ". $this->request->getFullActionName());
        if ($this->request->getFullActionName() === 'catalog_product_view') {
              $this->logger->info("pagename Excute:". $this->request->getFullActionName());
        $ext = strtolower(pathinfo($result, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $webpUrl = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $result);

            $relPath = parse_url($webpUrl, PHP_URL_PATH);
            $absPath = BP . '/pub' . $relPath;

            $origRel = parse_url($result, PHP_URL_PATH);
            $origAbs = BP . '/pub' . $origRel;

            $this->logger->info("Catalog Image Check: " . $origAbs);

            // Generate if missing
            if (!file_exists($absPath) && file_exists($origAbs)) {
                $this->createWebp($origAbs, $absPath, $ext);
                $this->logger->info("Created WebP: " . $absPath);
            }

            if (file_exists($absPath)) {
                return $webpUrl;
            }
        }
    }

        return $result;
    }

    private function createWebp($src, $dest, $ext)
    {
        try {
            if ($ext === 'png') {
                $img = @imagecreatefrompng($src);
                if ($img) {
                    // preserve transparency for PNG
                    imagepalettetotruecolor($img);
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                }
            } else {
                $img = @imagecreatefromjpeg($src);
                if ($img) {
                    imagepalettetotruecolor($img);
                }
            }

            if ($img) {
                if (!imagewebp($img, $dest, 80)) {
                    $this->logger->error("WebP Conversion Failed: Cannot save $dest");
                }
                imagedestroy($img);
                $this->logger->error("WebP Conversion save $dest");
            } else {
                $this->logger->error("WebP Conversion Failed: Cannot read source $src");
            }
        } catch (\Exception $e) {
            $this->logger->error("WebP Conversion Error: " . $e->getMessage());
        }
    }
}
