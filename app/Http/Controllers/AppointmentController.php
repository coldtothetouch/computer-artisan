<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatusEnum;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(): Response
    {
        $appointments = AppointmentResource::collection(Appointment::query()->oldest('date')->orderBy('time')->get());
        $appointmentStatuses = AppointmentStatusEnum::toArray();

        return Inertia::render('Admin/Appointments', compact(['appointments', 'appointmentStatuses']));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone_number' => 'required|string',
            'client_name' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string|max:5',
            'service' => 'nullable|string',
        ]);

        if (Appointment::query()->where('date', $data['date'])->where('time', $data['time'])->exists()) {
            return back()->withErrors(['time' => 'Это время уже забронировано']);
        }

        $data['date'] = Carbon::createFromFormat('Y-m-d', $data['date'])->format('Y-m-d');

        Appointment::query()->create($data);

        return back();
    }

    public function update(Request $request, Appointment $appointment): void
    {
        $data = $request->validate([
            'status' => [
                'required', 'string',
                Rule::enum(AppointmentStatusEnum::class)
            ],
        ]);

        $appointment->update($data);
    }
}
