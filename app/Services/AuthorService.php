<?php

namespace App\Services;

use App\Models\Author;
use App\Services\Concerns\UploadsToCloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class AuthorService
{
    use UploadsToCloudinary;

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Author::query()
            ->withCount('books')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Author
    {
        return Author::create($data);
    }

    public function update(Author $author, array $data): Author
    {
        $author->update($data);
        return $author;
    }

    public function delete(Author $author): void
    {
        if ($author->image_public_id) {
            $this->deleteFromCloudinary($author->image_public_id);
        }

        $author->delete();
    }

    public function uploadImage(Author $author, UploadedFile $file): Author
    {
        $this->deleteFromCloudinary($author->image_public_id);

        $uploaded = $this->uploadToCloudinary($file, 'authors/images');

        $author->update([
            'image' => $uploaded['url'],
            'image_public_id' => $uploaded['public_id'],
        ]);

        return $author;
    }

    public function removeImage(Author $author): Author
    {
        $this->deleteFromCloudinary($author->image_public_id);

        $author->update(['image' => null, 'image_public_id' => null]);

        return $author;
    }
}