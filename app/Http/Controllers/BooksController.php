<?php
namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::OrderBy('id', 'DESC');

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->has('author')) {
            $query->where('author', 'like', '%' . $request->query('author') . '%');
        }

        return response()->json($query->paginate(10), 200);
    }

    public function show($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        return response()->json($book, 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title'          => 'required|max:150',
            'author'         => 'required|max:100',
            'isbn'           => 'required|max:20|unique:books,isbn',
            'publisher'      => 'required|max:100',
            'published_year' => 'required|integer|min:1000|max:' . date('Y'),
            'price'          => 'required|numeric|min:0',
            'stock'          => 'integer|min:0',
            'status'         => 'in:available,out_of_stock,discontinued',
        ]);

        $book = Book::create($request->all())->fresh();

        return response()->json($book, 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $this->validate($request, [
            'title'          => 'max:150',
            'author'         => 'max:100',
            'isbn'           => 'max:20|unique:books,isbn,' . $book->id,
            'publisher'      => 'max:100',
            'published_year' => 'integer|min:1000|max:' . date('Y'),
            'price'          => 'numeric|min:0',
            'stock'          => 'integer|min:0',
            'status'         => 'in:available,out_of_stock,discontinued',
        ]);

        $book->fill($request->all());
        $book->save();

        return response()->json($book, 200);
    }

    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $book->delete();

        return response()->json([
            'message' => 'deleted successfully',
            'book_id' => $id
        ], 200);
    }
}