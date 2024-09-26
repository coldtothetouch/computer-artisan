<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Reviews', [
            'reviews' => Review::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'body' => 'required|string|max:255',
            'avatar' => 'required|file|image',
            'author_name' => 'required|string',
        ]);

        /** @var UploadedFile $avatar */
        $avatar = $data['avatar'];

        Review::query()->create([
            'body' => $data['body'],
            'author_name' => $data['author_name'],
            'avatar_path' => $avatar->store('reviews')
        ]);

        return back();
    }

    public function update(Request $request, Review $review)
    {
        $data = $request->validate([
            'body' => 'required|string|max:255',
            'avatar' => 'nullable|file|image',
            'avatar_path' => 'required_without:avatar|string',
            'author_name' => 'required|string',
        ]);

        /** @var UploadedFile $avatar */
        $avatar = $data['avatar'];

        if ($avatar) {
            $review->update([
                'body' => $data['body'],
                'author_name' => $data['author_name'],
                'avatar_path' => $avatar->store('reviews')
            ]);
        } else {
            $review->update([
                'body' => $data['body'],
                'author_name' => $data['author_name'],
            ]);
        }

        return back();
    }

    public function destroy(Review $review)
    {
        $review->delete();
        Storage::disk('public')->delete($review->avatar_path);
    }
}
