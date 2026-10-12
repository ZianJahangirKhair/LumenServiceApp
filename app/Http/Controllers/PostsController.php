<?php
namespace App\Http\Controllers;

use App\Models\Post;

class PostsController extends Controller
{
    public function index()
    {
        $posts = Post::OrderBy("id", "DESC")->paginate(10);

        return response()->json($posts, 200);
    }

    public function getPublishedPosts()
    {
        $posts = Post::where('status', 'published')->get();
        
        return response()->json([
            "message" => "Published posts",
            "results" => $posts
        ], 200);
    }

    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(["message" => "Post not found"], 404);
        }

        return response()->json([
            "message" => "Detail post",
            "result" => $post
        ], 200);
    }

    public function store()
    {
        $post = Post::create([
            'title' => 'Belajar Query Lumen',
            'content' => 'Ini adalah contoh isi postingan baru dari method create.',
            'status' => 'published',
            'user_id' => 1
        ]);

        return response()->json([
            "message" => "Post berhasil ditambahkan",
            "result" => $post
        ], 201);
    }

    public function update($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(["message" => "Post not found"], 404);
        }

        $post->update([
            'title' => 'Judul Postingan yang Telah Diperbarui',
            'status' => 'published'
        ]);

        return response()->json([
            "message" => "Post berhasil diperbarui",
            "result" => $post
        ], 200);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(["message" => "Post not found"], 404);
        }

        $post->delete();

        return response()->json([
            "message" => "Post berhasil dihapus"
        ], 200);
    }
}