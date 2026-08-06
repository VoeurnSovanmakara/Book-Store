<?php

namespace App\Services;

use App\Models\Book;
use App\Services\Concerns\UploadsToCloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class BookService
{
    use UploadsToCloudinary;

    public function list(int $perPage = 10): LengthAwarePaginator
    {
        return Book::query()
            ->with('author')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Book
    {
        return Book::create($data);
    }

    public function update(Book $book, array $data): Book
    {
        $book->update($data);
        return $book;
    }

    public function delete(Book $book): void
    {
        if ($book->cover_public_id) {
            $this->deleteFromCloudinary($book->cover_public_id);
        }

        $book->delete();
    }

    public function uploadCover(Book $book, UploadedFile $file): Book
    {
        $this->deleteFromCloudinary($book->cover_public_id);

        $uploaded = $this->uploadToCloudinary($file, 'books/covers');

        $book->update([
            'cover' => $uploaded['url'],
            'cover_public_id' => $uploaded['public_id'],
        ]);

        return $book;
    }

    public function removeCover(Book $book): Book
    {
        $this->deleteFromCloudinary($book->cover_public_id);

        $book->update(['cover' => null, 'cover_public_id' => null]);

        return $book;
    }
    
}