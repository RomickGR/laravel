<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Nullable;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('category','tags')->paginate(2);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories = Category::pluck('title','id')->all();
        $tags = Tag::pluck('title','id')->all();
        return view('admin.posts.create', compact('categories','tags'));
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
            'title'=>'required',
            'description'=>'required',
            'content'=>'required',
            'category_id'=>'required|integer',
            'thumbnail' => 'nullable|image'
        ]);

        //Category::create($request->all());

        $data = $request->all();

        $data['thumbnail'] = Post::uploadImage($request);

        $post = Post::create($data);
        $post->tags()->sync($request->tags);

        $request->session()->flash('success','Статья добавлена');
        return redirect()->route('posts.index');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        //$category = Category::find($id);
        $categories = Category::pluck('title','id')->all();
        $tags = Tag::pluck('title','id')->all();
        return view('admin.posts.edit', compact('categories','tags', 'post'));

        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=>'required',
            'description'=>'required',
            'content'=>'required',
            'category_id'=>'required|integer',
            'thumbnail' => 'nullable|image'
        ]);
       // $category = Category::find($id);
        // $category->update($request->all());

        $post = Post::find($id);
        $data = $request->all();
        if ($file = Post::uploadImage($request, $post->thumbnail)){
            $data['thumbnail'] = $file;
        }
        //$data['thumbnail'] = Post::uploadImage($request, $post->thumbnail);
        //$data['thumbnail'] = Post::uploadImage($request, $post->thumbnail);

        $post->update($data);
        $post->tags()->sync($request->tags);

        return redirect()->route('posts.index')->with('success','Изменения сохранены');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->tags()->sync([]);
        Storage::delete($post->thumbnail);
        $post->delete();

        return redirect()->route('posts.index')->with('success','Статья удалена');
    }
}
