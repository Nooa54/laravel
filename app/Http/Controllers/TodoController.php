<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return view('todo.index', [
            'todos' => $request->user()->todos()->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('todo.create', [
            'statuses' => Todo::STATUSES,
            'urgencies' => Todo::URGENCIES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->user()->todos()->create($this->validatedAttributes($request));

        return to_route('todo.index')->with('status', 'Tehtävä lisättiin listaan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $todo): View
    {
        return view('todo.show', [
            'todo' => $request->user()->todos()->findOrFail($todo),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, int $todo): View
    {
        return view('todo.edit', [
            'todo' => $request->user()->todos()->findOrFail($todo),
            'statuses' => Todo::STATUSES,
            'urgencies' => Todo::URGENCIES,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $todo): RedirectResponse
    {
        $todoRecord = $request->user()->todos()->findOrFail($todo);
        $todoRecord->update($this->validatedAttributes($request));

        return to_route('todo.show', $todoRecord)->with('status', 'Tehtävä päivitettiin.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $todo): RedirectResponse
    {
        $request->user()->todos()->findOrFail($todo)->delete();

        return to_route('todo.index')->with('status', 'Tehtävä poistettiin.');
    }

    /**
     * @return array{title: string, description: ?string, status: string, due_date: ?string, urgency: string}
     */
    private function validatedAttributes(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'string', 'in:'.implode(',', Todo::STATUSES)],
            'due_date' => ['nullable', 'date'],
            'urgency' => ['required', 'string', 'in:'.implode(',', Todo::URGENCIES)],
        ]);
    }
}
