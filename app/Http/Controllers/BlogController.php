<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    // ✅ GET ALL BLOGS
    public function index()
    {
        // dd('hello');
        $blogs = Blog::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $blogs,
        ]);
    }

    // ✅ STORE BLOG
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'category' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blogs', 'public');
        }

        $blog = Blog::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'category' => $request->category,
            'image' => $imagePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Blog created successfully',
            'data' => $blog,
        ]);
    }

    // ✅ SHOW SINGLE BLOG
    public function show($id)
    {
        // dd("helo");
        $blog = Blog::find($id);

        if (! $blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found',
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $blog,
        ]);
    }

    // ✅ UPDATE BLOG
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);

        if (! $blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        if ($request->hasFile('image')) {

            // delete old image
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }

            $blog->image = $request->file('image')->store('blogs', 'public');
        }

        $blog->title = $request->title;
        $blog->subtitle = $request->subtitle;
        $blog->description = $request->description;
        $blog->category = $request->category;
        $blog->save();

        return response()->json([
            'status' => true,
            'message' => 'Blog updated successfully',
            'data' => $blog,
        ]);
    }

    // ✅ DELETE BLOG
    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (! $blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found',
            ]);
        }

        // delete image
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return response()->json([
            'status' => true,
            'message' => 'Blog deleted successfully',
        ]);
    }
}
