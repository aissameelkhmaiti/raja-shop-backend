<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Upload une image sur Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param array $transformations
     * @return string URL de l'image uploadée
     */
    public static function uploadImage(
        UploadedFile $file,
        string $folder = 'uploads',
        array $transformations = []
    ): string {
        $defaultTransformations = [
            'folder' => $folder,
            'resource_type' => 'auto',
            'quality' => 'auto',
            'fetch_format' => 'auto',
        ];

        $finalTransformations = array_merge($defaultTransformations, $transformations);

        return Cloudinary::upload($file->getRealPath(), $finalTransformations)->getSecurePath();
    }

    /**
     * Supprimer une image de Cloudinary
     *
     * @param string $imageUrl
     * @return bool
     */
    public static function deleteImage(string $imageUrl): bool
    {
        try {
            $publicId = self::getPublicIdFromUrl($imageUrl);

            if ($publicId) {
                Cloudinary::destroy($publicId);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Cloudinary delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extraire le public_id depuis l'URL Cloudinary
     *
     * @param string $url
     * @return string|null
     */
    private static function getPublicIdFromUrl(string $url): ?string
    {
        preg_match('/\/upload\/(?:v\d+\/)?(.+)\.\w+$/', $url, $matches);
        return $matches[1] ?? null;
    }
}
