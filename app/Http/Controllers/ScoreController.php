<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Score;

class ScoreController extends Controller
{
    // ✔ Store score after user finishes game
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mode' => 'required|string',
            'score' => 'required|integer',
            'questions' => 'required|integer',
        ]);

        $validated['user_id'] = auth()->id();


        $score = Score::create($validated);

        return response()->json([
            'message' => 'Score saved successfully!',
            'data' => $score
        ], 200);
    }

    // ✔ Get all high scores (sorted)
    public function index()
    {
        $scores = Score::orderBy('score', 'desc')->get();

        return response()->json($scores);
    }

    // ✔ Get user-specific score history
    public function userScores($id)
    {
        $scores = Score::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($scores);
    }
}
