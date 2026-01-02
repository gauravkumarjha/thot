<?php

namespace DiMedia\WebpImage\Plugin;

use Psr\Log\LoggerInterface;
class RenderWebp
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public function afterGetUrl(\Magento\Framework\View\Asset\File $subject, $result)
    {
        $ext = strtolower(pathinfo($result, PATHINFO_EXTENSION));

        // Target only jpg/png
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $webpUrl = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $result);

            // Paths
            $relPath = parse_url($webpUrl, PHP_URL_PATH);
            $absPath = BP . '/pub' . $relPath;

            $origRel = parse_url($result, PHP_URL_PATH);
            $origAbs = BP . '/pub' . $origRel;
            $this->logger->info("frontend URL origAbs: " . $origAbs);
            // Create WebP if missing
            if (!file_exists($absPath) && file_exists($origAbs)) {
                $this->createWebp($origAbs, $absPath, $ext);
                $this->logger->info("frontend URL absPath: " . $absPath);
            }

            // If WebP now exists → serve it
            if (file_exists($absPath)) {
                return $webpUrl;
            }
        }

        // Fallback
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
                imagewebp($img, $dest, 80); // quality 80
                imagedestroy($img);
            }
        } catch (\Exception $e) {
            // Ignore silently, fallback will work
        }
    }
}
