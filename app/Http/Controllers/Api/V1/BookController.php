<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    use ApiResponse;
    
    public function __construct(
        private BookService $bookService
    )
    {}

    public function index(): JsonResponse
    {
        $books = $this->bookService->list();
        return $this->success(BookResource::collection($books), 'Books retrieved successfully');
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->bookService->create($request->validated());
        return $this->success(
            new BookResource($book),
            'Book created successfully',
            201
        );
    }

    public function show(Book $book): JsonResponse
    {
        return $this->success(new BookResource($book), 'Book retrieved successfully');
    }

    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $book = $this->bookService->update($book, $request->validated());
        return $this->success(new BookResource($book), 'Book updated successfully');
        
    }
    public function destroy(Book $book): JsonResponse
    {
        $this->bookService->delete($book);
        return $this->successNoContent('Book deleted successfully');
    }
}
