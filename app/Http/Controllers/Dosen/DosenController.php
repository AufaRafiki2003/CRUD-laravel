<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class DosenController extends Controller
{
    // Method Tampilkan data
    public function index(): View
    {
        $posts = Post::latest()->paginate(5);
        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'image' => 
            'required|image|mimes:jpeg,jpg,png|max:2048',
            'nama' => 'required|min:5',
            'nim' => 'required|min:8',
            'jurusan' => 'required|min:5',
            'alamat' => 'required|min:10'
        ]);
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        Post::create([
            'image' => $image->hashName(),
            'nama' => $request->nama,
            'nim' => $request->nim,
            'jurusan' => $request->jurusan,
            'alamat' => $request->alamat
        ]);
        return redirect()->route('posts.index')->with(['success' => 
        'Data Sukses Disimpan!']);
    }

    public function edit(string $id): View
    {
        $post = Post::findOrFail($id);
        return view('posts.edit', compact('post'));
    }

    // Method Proses Update Data
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'image' => 'image|mimes:jpeg,jpg,png|max:2048',
            'nama' => 'required',
            'nim' => 'required',
            'jurusan' => 'required',
            'alamat' => 'required'
        ]);
        $post = Post::findOrFail($id);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            Storage::delete('public/posts/'.$post->image);
            $post->update([
                'image' => $image->hashName(),
                'nama' => $request->nama,
                'nim' => $request->nim,
                'jurusan' => $request->jurusan,
                'alamat' => $request->alamat
            ]);
        } else {
            $post->update([
                'nama' => $request->nama,
                'nim' => $request->nim,
                'jurusan' => $request->jurusan,
                'alamat' => $request->alamat
            ]);
        }
        return redirect()->route('posts.index')->with(['success'
        => 'Data Berhasil Diubah!']);
    }

    public function destroy($id): RedirectResponse
    {
        $post = Post::findOrFail($id);
        Storage::delete('public/posts/'. $post->image);
        $post->delete();
        return redirect()->route('posts.index')->with(['success'
        => 'Data Berhasil Dihapus!']);
    }

    public function show(string $id): View
    {
        $post = Post::findOrFail($id);
        return view('posts.show', compact('post'));
    }   

}
