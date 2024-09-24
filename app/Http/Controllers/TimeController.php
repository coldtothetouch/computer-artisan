<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Time;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TimeController extends Controller
{
    public function index(): Response
    {
        $times = Time::all();

        return Inertia::render('Admin/Time', compact(['times']));
    }

    public function store(Request $request): \Illuminate\Http\Response
    {
        $data = $request->validate([
            'times' => 'required|array',
            'times.*' => 'string|max:5|unique:times,time'
        ]);

        $times = [];

        foreach ($data['times'] as $time) {
            $times[] = Time::query()->create([
                'time' => $time
            ]);
        }

        return response([
            'times' => $times,
        ]);
    }

    public function update(Request $request): \Illuminate\Http\Response
    {
        $data = $request->validate([
            'times' => 'required|array',
            'times.*' => 'string|max:5'
        ]);

        Time::query()->delete();

        $times = [];

        foreach ($data['times'] as $time) {
            $times[] = Time::query()->create([
                'time' => $time
            ]);
        }

        return response([
            'times' => $times,
        ]);
    }

    public function delete(Time $time): void
    {
        $time->delete();
    }
}
