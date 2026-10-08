<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        return view('feedback.index', [
            'categories' => Feedback::CATEGORIES,
            'selected'   => $request->query('category', 'majoitus'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:80'],
            'email'    => ['nullable', 'email', 'max:120'],
            'category' => ['required', Rule::in(array_keys(Feedback::CATEGORIES))],
            'rating'   => ['nullable', 'integer', 'between:1,5'],
            'message'  => ['required', 'string', 'min:5', 'max:3000'],
        ]);

        Feedback::create($data);

        return redirect()->route('feedback.index')->with('status', 'Kiitos palautteesta! Luemme sen mielellämme.');
    }
}
