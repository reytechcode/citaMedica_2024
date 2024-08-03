<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointment;
use App\Interfaces\HorarioServiceInterface;
use App\Models\Appointment;
use App\Models\CancelledAppointment;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{

    public function index(){

        $role = auth()->user()->role;

        if($role == 'admin'){
            //Admin
            $confirmedAppointments = Appointment::all()
            ->where('status', 'Confirmada');
            $pendingAppointments = Appointment::all()
            ->where('status', 'Reservada');
            $oldAppointments = Appointment::all()
            ->whereIn('status', ['Atendida','Cancelada']);
        }elseif($role == 'doctor'){
            //Doctor
            $confirmedAppointments = Appointment::all()
            ->where('status', 'Confirmada')
            ->where('doctor_id', auth()->id());
            $pendingAppointments = Appointment::all()
            ->where('status', 'Reservada')
            ->where('doctor_id', auth()->id());
            $oldAppointments = Appointment::all()
            ->whereIn('status', ['Atendida','Cancelada'])
            ->where('doctor_id', auth()->id());
        }elseif($role == 'paciente'){
            //Pacientes
            $confirmedAppointments = Appointment::all()
            ->where('status', 'Confirmada')
            ->where('patient_id', auth()->id());
            $pendingAppointments = Appointment::all()
            ->where('status', 'Reservada')
            ->where('patient_id', auth()->id());
            $oldAppointments = Appointment::all()
            ->whereIn('status', ['Atendida','Cancelada'])
            ->where('patient_id', auth()->id());
        }

        
        return view('appointments.index', 
        compact('confirmedAppointments', 'pendingAppointments', 'oldAppointments', 'role') );
    }

    public function create(HorarioServiceInterface $horarioServiceInterface) {
        $specialties = Specialty::all();

        $specialtyId = old('specialty_id');
        if ($specialtyId) {
            $specialty = Specialty::find($specialtyId);
            $doctors = $specialty->users;
        } else {
            $doctors = collect();
        }

        $date = old('scheduled_date');
        $doctorId = old('doctor_id');
        if ($date && $doctorId) {
            $intervals = $horarioServiceInterface->getAvailableIntervals($date, $doctorId);
        }else {
            $intervals = null;
        }

        return view('appointments.create', compact('specialties', 'doctors', 'intervals'));
    }

    public function store(StoreAppointment $request, HorarioServiceInterface $horarioServiceInterface) {

        $created = Appointment::createForPatient($request, auth()->id());

        if($created)
            $notification = 'La cita se ha realizado correctamente.';
        else
            $notification = 'Error al resgistrar la cita médica.';

        return redirect('/miscitas')->with(compact('notification'));
    }

    public function cancel(Appointment $appointment, Request $request) {

        if($request->has('justification')){
            $cancellation = new CancelledAppointment();
            $cancellation->justification = $request->input('justification');
            $cancellation->cancelled_by_id = auth()->id();

            $saved = $appointment->cancellation()->save($cancellation);
            $nameDoctor = $appointment->doctor->name;
            $dateAppointment = $appointment->scheduled_date;
            $timeAppointment = $appointment->scheduled_time_12;

            if ($saved)
                $appointment->patient->sendFCM("Su cita médica con el médico: $nameDoctor, para la fecha: $dateAppointment a las $timeAppointment fue cancelada.");
        }

        $appointment->status = 'Cancelada';
        $appointment->save();
        $notification = 'La cita se ha cancelado correctamente.';

        return redirect('/miscitas')->with(compact('notification'));
    }

    public function confirm(Appointment $appointment) {

        $appointment->status = 'Confirmada';
        $saved = $appointment->save();
        $nameDoctor = $appointment->doctor->name;
        $dateAppointment = $appointment->scheduled_date;
        $timeAppointment = $appointment->scheduled_time_12;

        if ($saved)
            $appointment->patient->sendFCM("Su cita médica con el médico: $nameDoctor, para la fecha: $dateAppointment a las $timeAppointment fue confirmada.");

        $notification = 'La cita se ha confirmado correctamente.';

        return redirect('/miscitas')->with(compact('notification'));
    }

    public function formCancel(Appointment $appointment) {
        if($appointment->status == 'Confirmada' || 'Reservada'){
            $role = auth()->user()->role;
            return view('appointments.cancel', compact('appointment', 'role'));
        }
        return redirect('/miscitas');
        
    }

    public function show(Appointment $appointment){
        $role = auth()->user()->role;
        return view('appointments.show', compact('appointment', 'role'));
    }
}