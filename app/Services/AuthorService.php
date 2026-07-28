<?php

namespace App\Services;

use App\Models\Author;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class AuthorService
{
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
        $author->delete();
    }

    public function uploadImage(Author $author, UploadedFile $file): Author
    {
        if ($author->image) {
            Storage::disk('public')->delete($author->image);
        }

        $path = $file->store('author/images', 'public');
        $author->update(['image' => $path]);
        return $author;
    }

    public function removeImage(Author $author): Author
    {
        if ($author->image) {
            Storage::disk('public')->delete($author->image);
            $author->update(['image' => null]);
        }
        return $author;
    }
}