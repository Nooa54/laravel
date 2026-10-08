<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Review;

class FeedbackController extends Controller
{
    public function index()
    {
        return view('admin.feedback.index', [
            'feedback' => Feedback::latest()->get(),
            'reviews'  => Review::with('room')->latest()->get(),
        ]);
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return back()->with('status', 'Palaute poistettu.');
    }

    public function destroyReview(Review $review)
    {
        $review->delete();
        return back()->with('status', 'Arvostelu poistettu.');
    }
}
