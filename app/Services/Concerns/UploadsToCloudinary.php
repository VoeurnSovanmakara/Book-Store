<?php

namespace App\Services\Concerns;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

trait UploadsToCloudinary
{
    protected function cloudinary(): Cloudinary
    {
        return new Cloudinary(config('services.cloudinary.url'));
    }

    protected function uploadToCloudinary(UploadedFile $file, string $folder): array
    {
        $result = $this->cloudinary()->uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder,
        ]);

        return [
            'url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    protected function deleteFromCloudinary(?string $publicId): void
    {
        if ($publicId) {
            $this->cloudinary()->uploadApi()->destroy($publicId);
        }
    }
}
