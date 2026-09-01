<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GameScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    /**
     * Catalog of playable games: slug => [title, tagline, description, subject, icon, color, max_score].
     * `max_score` bounds what the client is allowed to report for a single round.
     */
    public const GAMES = [
        'math-blitz' => [
            'title' => 'Math Blitz',
            'tagline' => '60-second arithmetic sprint',
            'description' => 'Solve as many addition, subtraction and multiplication problems as you can before the clock runs out.',
            'subject' => 'Math',
            'icon' => '🧮',
            'color' => 'indigo',
            'max_score' => 300,
        ],
        'word-scramble' => [
            'title' => 'Word Scramble',
            'tagline' => 'Unscramble the vocabulary word',
            'description' => 'Race the clock to unscramble school-subject vocabulary words, using hints when you get stuck.',
            'subject' => 'English',
            'icon' => '🔤',
            'color' => 'emerald',
            'max_score' => 900,
        ],
        'memory-match' => [
            'title' => 'Memory Match',
            'tagline' => 'Flip cards, match the pairs',
            'description' => 'Train your memory by matching science, math and geography terms in as few moves as possible.',
            'subject' => 'General',
            'icon' => '🧠',
            'color' => 'amber',
            'max_score' => 1000,
        ],
        'quiz-dash' => [
            'title' => 'Quiz Dash',
            'tagline' => 'Beat the buzzer trivia round',
            'description' => 'Answer quick-fire multiple choice questions across math, science and general knowledge.',
            'subject' => 'Mixed',
            'icon' => '❓',
            'color' => 'rose',
            'max_score' => 600,
        ],
    ];

    public function index(Request $request): View
    {
        $scores = $request->user()->gameScores()->get()->keyBy('game');

        return view('student.games.index', [
            'games' => self::GAMES,
            'scores' => $scores,
        ]);
    }

    public function show(Request $request, string $game): View|RedirectResponse
    {
        if (! array_key_exists($game, self::GAMES)) {
            return redirect()->route('student.games.index')->with('error', 'That game could not be found.');
        }

        $score = $request->user()->gameScores()->where('game', $game)->first();

        return view('student.games.show', [
            'slug' => $game,
            'meta' => self::GAMES[$game],
            'bestScore' => $score->best_score ?? 0,
        ]);
    }

    public function storeScore(Request $request, string $game): JsonResponse
    {
        if (! array_key_exists($game, self::GAMES)) {
            abort(404);
        }

        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:'.self::GAMES[$game]['max_score']],
        ]);

        $record = GameScore::firstOrNew([
            'user_id' => $request->user()->id,
            'game' => $game,
        ]);
        $record->plays = ($record->plays ?? 0) + 1;
        $record->best_score = max($record->best_score ?? 0, $validated['score']);
        $record->save();

        return response()->json([
            'best_score' => $record->best_score,
            'is_new_best' => $record->best_score === $validated['score'] && $record->plays > 0,
        ]);
    }
}
