<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class BookService
{
    public function list(int $perPage = 15): LengthAwarePaginator
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
        $book->delete();
    }

    public function uploadCover(Book $book, UploadedFile $file): Book
    {
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }

        $path = $file->store('books/covers', 'public');
        $book->update(['cover' => $path]);
        return $book;
    }

    public function removeCover(Book $book): Book
    {
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
            $book->update(['cover' => null]);
        }
        return $book;
    }
    
}