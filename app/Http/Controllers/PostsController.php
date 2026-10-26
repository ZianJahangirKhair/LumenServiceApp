<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    private function getAccept(Request $request)
    {
        $accept = strtolower(trim(explode(';', (string) $request->header('Accept'))[0]));

        return in_array($accept, ['application/json', 'application/xml']) ? $accept : null;
    }

    private function isJsonContentType(Request $request)
    {
        $type = strtolower(trim(explode(';', (string) $request->header('Content-Type'))[0]));

        return $type === 'application/json';
    }

    private function arrayToXml(array $data, \SimpleXMLElement $xml, $itemName)
    {
        foreach ($data as $key => $value) {
            $name = is_numeric($key) ? $itemName : $key;

            if (is_array($value)) {
                $child = $xml->addChild($name);
                $this->arrayToXml($value, $child, 'item');
            } else {
                $xml->addChild($name, htmlspecialchars((string) $value));
            }
        }
    }

    private function respond($accept, $data, $status = 200, $root = 'post', $itemName = 'post')
    {
        $data = json_decode(json_encode($data), true);

        if ($accept === 'application/xml') {
            $xml = new \SimpleXMLElement('<' . $root . '/>');
            $this->arrayToXml($data, $xml, $itemName);

            return response($xml->asXML(), $status)->header('Content-Type', 'application/xml');
        }

        return response()->json($data, $status);
    }

    private function notAcceptable()
    {
        return response('Not Acceptable!', 406);
    }

    private function unsupportedMediaType()
    {
        return response('Unsupported Media Type', 415);
    }

    public function store(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $contentTypeHeader = $request->header('Content-Type');

            if ($contentTypeHeader === 'application/json') {

                $this->validate($request, [
                    'title'   => 'required|max:100',
                    'content' => 'required',
                    'status'  => 'in:draft,published',
                    'user_id' => 'required|integer',
                ]);

                $input = $request->all();
                $post = Post::create($input)->fresh();

                if ($acceptHeader === 'application/json') {
                    return response()->json($post, 201);
                } else {
                    // response xml
                    $xml = new \SimpleXMLElement('<post/>');
                    $xml->addChild('id', $post->id);
                    $xml->addChild('title', htmlspecialchars($post->title));
                    $xml->addChild('status', $post->status);
                    $xml->addChild('content', htmlspecialchars($post->content));
                    $xml->addChild('user_id', $post->user_id);
                    $xml->addChild('created_at', $post->created_at);
                    $xml->addChild('updated_at', $post->updated_at);

                    return response($xml->asXML(), 201)
                        ->header('Content-Type', 'application/xml');
                }
            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function getPublishedPosts()
    {
        $posts = Post::where('status', 'published')->get();

        return response()->json([
            "message" => "Published posts",
            "results" => $posts
        ], 200);
    }

    public function show(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $post = Post::find($id);

            if (!$post) {
                if ($acceptHeader === 'application/json') {
                    return response()->json(['message' => 'Post not found'], 404);
                } else {
                    $xml = new \SimpleXMLElement('<error/>');
                    $xml->addChild('message', 'Post not found');

                    return response($xml->asXML(), 404)
                        ->header('Content-Type', 'application/xml');
                }
            }

            if ($acceptHeader === 'application/json') {
                return response()->json($post, 200);
            } else {
                $xml = new \SimpleXMLElement('<post/>');
                $xml->addChild('id', $post->id);
                $xml->addChild('title', htmlspecialchars($post->title));
                $xml->addChild('status', $post->status);
                $xml->addChild('content', htmlspecialchars($post->content));
                $xml->addChild('user_id', $post->user_id);
                $xml->addChild('created_at', $post->created_at);
                $xml->addChild('updated_at', $post->updated_at);

                return response($xml->asXML(), 200)
                    ->header('Content-Type', 'application/xml');
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function update(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $contentTypeHeader = $request->header('Content-Type');

            if ($contentTypeHeader === 'application/json') {

                $post = Post::find($id);

                if (!$post) {
                    if ($acceptHeader === 'application/json') {
                        return response()->json(['message' => 'Post not found'], 404);
                    } else {
                        $xml = new \SimpleXMLElement('<error/>');
                        $xml->addChild('message', 'Post not found');

                        return response($xml->asXML(), 404)
                            ->header('Content-Type', 'application/xml');
                    }
                }

                $this->validate($request, [
                    'title'   => 'max:100',
                    'status'  => 'in:draft,published',
                    'user_id' => 'integer',
                ]);

                $input = $request->all();
                $post->fill($input);
                $post->save();

                if ($acceptHeader === 'application/json') {
                    return response()->json($post, 200);
                } else {
                    $xml = new \SimpleXMLElement('<post/>');
                    $xml->addChild('id', $post->id);
                    $xml->addChild('title', htmlspecialchars($post->title));
                    $xml->addChild('status', $post->status);
                    $xml->addChild('content', htmlspecialchars($post->content));
                    $xml->addChild('user_id', $post->user_id);
                    $xml->addChild('created_at', $post->created_at);
                    $xml->addChild('updated_at', $post->updated_at);

                    return response($xml->asXML(), 200)
                        ->header('Content-Type', 'application/xml');
                }
            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function destroy(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $post = Post::find($id);

            if (!$post) {
                if ($acceptHeader === 'application/json') {
                    return response()->json(['message' => 'Post not found'], 404);
                } else {
                    $xml = new \SimpleXMLElement('<error/>');
                    $xml->addChild('message', 'Post not found');

                    return response($xml->asXML(), 404)
                        ->header('Content-Type', 'application/xml');
                }
            }

            $post->delete();

            if ($acceptHeader === 'application/json') {
                return response()->json([
                    'message' => 'deleted successfully',
                    'post_id' => $id
                ], 200);
            } else {
                $xml = new \SimpleXMLElement('<result/>');
                $xml->addChild('message', 'deleted successfully');
                $xml->addChild('post_id', $id);

                return response($xml->asXML(), 200)
                    ->header('Content-Type', 'application/xml');
            }
        } else {
            return response('Not Acceptable!', 406);
        }
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