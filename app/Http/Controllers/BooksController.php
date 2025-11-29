<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use App\Http\Resources\BookResource;

class BooksController extends Controller
{
    /**
     * ==========1===========
     * Tampilkan daftar semua buku
     */
    public function index()
    {
        $books = Book::all();
        return BookResource::collection($books);
    }
    
    /**
     * ==========2===========
     * Simpan buku baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'author' => 'required|string',
        'published_year' => 'required|date_format:Y',
        'is_available' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please check your request',
                'errors' => $validator->errors()
            ], 422);
        }

        $book = Book::create($validator->validated());

        return (new BookResource($book))
            ->additional(['message' => 'Book created successfully'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * =========3===========
     * Tampilkan detail buku tertentu.
     */
    public function show(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        return new BookResource($book);
    }

    /**
     * =========4===========
     * Fungsi untuk memperbarui data buku tertentu
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string',
            'published_year' => 'required|date_format:Y',
            'is_available' => 'required|boolean',
        ]);

        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        if ($validator->fails()) {
            return response()->json([
            'message' => 'Please check your request',
            'errors' => $validator->errors()
            ], 422);
        }

        $book->update($validator->validated());

        return (new BookResource($book))
            ->additional(['message' => 'Book updated successfully'])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * =========5===========
     * Hapus buku tertentu dari penyimpanan.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted successfully'], 200);
    }

    /**
     * =========6===========
     * Ubah status ketersediaan buku (ubah field is_available)
     */
    public function borrowReturn(string $id)
    {
        $book = Book::findOrFail($id);
        $book->is_available = !$book->is_available;
        $book->save();
        return response()->json([
            'message'=>'Status ketersediaan berhasil diperbarui!',
            'data'=>[
                'book'=>$book
            ]
            ]);
    }
    
}
