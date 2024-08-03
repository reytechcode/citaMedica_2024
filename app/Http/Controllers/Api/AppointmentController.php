<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointment;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index() {
        $user = Auth::guard('api')->user();
        $appointments = $user->asPatientAppointments()
            ->with(['specialty' => function ($query) {
                $query->select('id', 'name');
            }
            , 'doctor' => function ($query) {
                $query->select('id', 'name');
            }
            ])
            ->get([
                "id",
                "scheduled_date",
                "scheduled_time",
                "type",
                "description",
                "doctor_id",
                "specialty_id",
                "created_at",
                "status"
            ]);
        return $appointments;
    }

    public function store(StoreAppointment $request) {
        $patientId = Auth::guard('api')->id();
        $appointment = Appointment::createForPatient($request, $patientId);

        if($appointment)
            $success = true;
        else
            $success = false; 
        
        return compact('success');
    }
}
