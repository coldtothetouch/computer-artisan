<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Time;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke()
    {
        $times = Time::all();
        $reviews = Review::all();
        $services = Service::all();
        $appointments = Appointment::all();
        $categories = ServiceCategory::query()->with('services')->has('services')->get();

        return Inertia::render('Index2', compact(['times', 'categories', 'reviews', 'appointments', 'services']));
    }
}
