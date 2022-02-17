<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Exports\PostsExport;
use Maatwebsite\Excel\Facades\Excel;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function takeAndSort(Request $request)
    {
        if ($request->get('sort') === 'new'){
            return Post::latest()->paginate(5);
        } else { return Post::oldest()->paginate(5);}
    }

    public function postsExport()
    {
        return Excel::download(new PostsExport, 'posts.xlsx');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
             'body' => 'required'
        ]);


        $name = $request->get('name');
        if($name === null){
            $name = 'Гость';
        } else {$name = $request->get('name');}

        $post = new Post([
            "title" => $request->get('title'),
            "body" => $request->get('body'),
            "name" => $name
        ]);
        $post->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Post::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $post = Post::find($id);
        $post->title = $request->get('title');
        $post->body = $request->get('body');
        $post->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Post::find($id)->delete();
    }
}
