<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsCommentController extends Controller
{
    public function store(Request $request, News $news): JsonResponse
    {
        if ($news->status !== 'published') {
            return response()->json(['success' => false, 'message' => 'Artikel tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'nullable|email|max:200',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        $validated['news_id']    = $news->id;
        $validated['is_approved'] = true;

        $comment = NewsComment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dikirim.',
            'comment' => [
                'id'         => $comment->id,
                'name'       => $comment->name,
                'comment'    => $comment->comment,
                'created_at' => $comment->created_at->diffForHumans(),
            ],
        ]);
    }
}
