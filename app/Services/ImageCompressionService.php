<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressionService
{
    /**
     * Compress and save an image with automatic resizing and EXIF orientation correction.
     *
     * @param UploadedFile|string $file
     * @param string $directory Subdirectory under storage/app/public
     * @param int $maxDimension Max width or height in pixels (default: 1400px)
     * @param int $quality JPEG/WebP compression quality (0-100, default: 80)
     * @return string Relative storage path (e.g., 'timbangan_sales/filename.jpg')
     */
    public static function compressAndStore($file, string $directory = 'timbangan_sales', int $maxDimension = 1400, int $quality = 80): ?string
    {
        try {
            $sourcePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

            if (!file_exists($sourcePath)) {
                return null;
            }

            $rawContent = file_get_contents($sourcePath);
            if (!$rawContent) {
                return null;
            }

            // Create GD image resource from string
            $image = @imagecreatefromstring($rawContent);
            if (!$image) {
                // Fallback: If GD cannot create from string, try normal store
                if ($file instanceof UploadedFile) {
                    return $file->store($directory, 'public');
                }
                return null;
            }

            // Correct EXIF orientation if available (for smartphone camera captures)
            if (function_exists('exif_read_data')) {
                $exif = @exif_read_data($sourcePath);
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $image = imagerotate($image, 180, 0);
                            break;
                        case 6:
                            $image = imagerotate($image, -90, 0);
                            break;
                        case 8:
                            $image = imagerotate($image, 90, 0);
                            break;
                    }
                }
            }

            // Get current dimensions
            $origWidth = imagesx($image);
            $origHeight = imagesy($image);

            $targetWidth = $origWidth;
            $targetHeight = $origHeight;

            // Calculate new proportional dimensions if exceeding maxDimension
            if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
                if ($origWidth >= $origHeight) {
                    $targetWidth = $maxDimension;
                    $targetHeight = (int)round(($origHeight / $origWidth) * $maxDimension);
                } else {
                    $targetHeight = $maxDimension;
                    $targetWidth = (int)round(($origWidth / $origHeight) * $maxDimension);
                }

                // Resample to high quality scaled canvas
                $resized = imagecreatetruecolor($targetWidth, $targetHeight);

                // Preserve transparency for PNG/GIF if converted
                imagealphablending($resized, false);
                imagesavealpha($resized, true);

                imagecopyresampled(
                    $resized,
                    $image,
                    0, 0, 0, 0,
                    $targetWidth,
                    $targetHeight,
                    $origWidth,
                    $origHeight
                );

                imagedestroy($image);
                $image = $resized;
            }

            // Ensure destination directory exists in storage
            $storageDir = storage_path('app/public/' . trim($directory, '/'));
            if (!file_exists($storageDir)) {
                mkdir($storageDir, 0755, true);
            }

            // Generate unique filename
            $filename = 'timbangan_' . date('Ymd_His') . '_' . Str::random(8) . '.jpg';
            $destPath = $storageDir . DIRECTORY_SEPARATOR . $filename;

            // Save as optimized JPEG
            imagejpeg($image, $destPath, $quality);
            imagedestroy($image);

            return trim($directory, '/') . '/' . $filename;
        } catch (\Throwable $e) {
            \Log::error('ImageCompressionService Error: ' . $e->getMessage());

            // Fallback to standard Laravel storage if compression encounters unexpected error
            if ($file instanceof UploadedFile) {
                return $file->store($directory, 'public');
            }
            return null;
        }
    }

    /**
     * Delete image from public storage.
     */
    public static function deleteImage(?string $relativePath): bool
    {
        if (!$relativePath) {
            return false;
        }

        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->delete($relativePath);
        }

        $fullPath = storage_path('app/public/' . $relativePath);
        if (file_exists($fullPath)) {
            return @unlink($fullPath);
        }

        return false;
    }
}
