<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'phone_number' => 'required|string',
            'client_name' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string|max:5'
        ]);

        $data['date'] = Carbon::createFromFormat('Y-m-d', $data['date'])->format('Y-m-d');

        Appointment::query()->create($data);
    }
}
