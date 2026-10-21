<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

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

        return response()->json($post, 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title'   => 'required|max:100',
            'content' => 'required',
            'status'  => 'in:draft,published',
            'user_id' => 'required|integer',
        ]);

        $post = Post::create($request->all())->fresh();

        return response()->json($post, 201);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(["message" => "Post not found"], 404);
        }

        $this->validate($request, [
            'title'   => 'max:100',
            'status'  => 'in:draft,published',
            'user_id' => 'integer',
        ]);

        $post->fill($request->all());
        $post->save();

        return response()->json($post, 200);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(["message" => "Post not found"], 404);
        }

        $post->delete();

        return response()->json([
            'message' => 'deleted successfully',
            'post_id' => $id
        ], 200);
    }

    public function comments($postId)
    {
        $post = Post::find($postId);

        if (!$post) {
            return response()->json(["message" => "Post not found"], 404);
        }

        return response()->json([
            'message' => 'comments',
            'results' => $post->comments()->orderBy('id', 'DESC')->get()
        ], 200);
    }

    public function commentStore(Request $request, $postId)
    {
        $post = Post::find($postId);

        if (!$post) {
            return response()->json(["message" => "Post not found"], 404);
        }

        $this->validate($request, [
            'author_name'  => 'required|max:100',
            'comment_body' => 'required',
        ]);

        $comment = $post->comments()->create($request->only('author_name', 'comment_body'));

        return response()->json($comment, 201);
    }

    public function commentDestroy($postId, $commentId)
    {
        $comment = Comment::where('post_id', $postId)->where('id', $commentId)->first();

        if (!$comment) {
            return response()->json(["message" => "Comment not found"], 404);
        }

        $comment->delete();

        return response()->json([
            'message'    => 'deleted successfully',
            'comment_id' => $commentId
        ], 200);
    }
}