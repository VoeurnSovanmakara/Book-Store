<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Requests\Author\UpdateAuthorRequest;
use App\Http\Requests\Author\UploadAuthorImageRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use App\Services\AuthorService;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AuthorService $authorService
    ) {}

    public function index(): JsonResponse
    {
        $author = $this->authorService->list();
        return $this->success(
            AuthorResource::collection($author),
            'Authors retrieved successfully'
        );
    }

    public function store(StoreAuthorRequest $request): JsonResponse
    {
        $author = $this->authorService->create($request->validated());
        return $this->success(
            new AuthorResource($author),
            'Author created successfully',
            201
        );
    }



    public function show(Author $author): JsonResponse
    {
        return $this->success(
            new AuthorResource($author->loadCount('books')),
            'Author retrieved successfully'
        );
    }

    public function update(UpdateAuthorRequest $request, Author $author): JsonResponse
    {
        $author = $this->authorService->update($author, $request->validated());
        return $this->success(
            new AuthorResource($author),
            'Author updated successfully'
        );
    }

    public function destroy(Author $author): JsonResponse
    {
        $this->authorService->delete($author);
        return $this->successNoContent('Author deleted successfully');
    }

    public function uploadImage(UploadAuthorImageRequest $request, Author $author): JsonResponse
    {
        $author = $this->authorService->uploadImage($author, $request->file('image'));

        return $this->success(
            new AuthorResource($author),
            'Author image uploaded successfully'
        );

    }

    public function removeImage(Author $author): JsonResponse {
        $author = $this->authorService->removeImage($author);
        return $this->success(new AuthorResource($author), 'Author image removed successfully');
    }
}
