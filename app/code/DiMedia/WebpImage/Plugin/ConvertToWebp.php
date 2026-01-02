<?php

namespace DiMedia\WebpImage\Plugin;

use Psr\Log\LoggerInterface;

class ConvertToWebp
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function afterSave(\Magento\MediaStorage\Model\File\Uploader $subject, $result)
    {
        if (isset($result['path']) && isset($result['file'])) {
            $filePath = $result['path'] . $result['file'];
            $this->convertToWebp($filePath);
        }
        return $result;
    }

    private function convertToWebp($filePath)
    {
        $this->logger->info("Converting file: " . $filePath);

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $this->logger->info("Skipped (Not an image): " . $ext);
            return;
        }

        $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $filePath);

        try {
            if ($ext === 'png') {
                $image = @imagecreatefrompng($filePath);
            } else {
                $image = @imagecreatefromjpeg($filePath);
            }

            if ($image) {
                imagepalettetotruecolor($image); // PNG fix
                imagewebp($image, $webpPath, 80);
                imagedestroy($image);
                $this->logger->info("Converted to WebP: " . $webpPath);
            } else {
                $this->logger->error("Image resource could not be created for: " . $filePath);
            }
        } catch (\Exception $e) {
            $this->logger->error("WebP Conversion Error: " . $e->getMessage());
        }
    }
}
