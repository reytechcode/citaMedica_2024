<?php

namespace App\Http\Requests;

use App\Interfaces\HorarioServiceInterface;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreAppointment extends FormRequest
{

    private $horarioService;

    public function __construct(HorarioServiceInterface $horarioServiceInterface)
    {
        $this->horarioService = $horarioServiceInterface;
    }

    public function rules()
    {
        return [
            'scheduled_time' => 'required',
            'type' => 'required',
            'description' => 'required',
            'doctor_id' => 'exists:users,id',
            'specialty_id' => 'exists:specialties,id'
        ];
    }

    public function messages()
    {
        return [
            'scheduled_time.required' => 'Debe seleccionar una hora para su cita.',
            'type.required' => 'Debe seleccionar el tipo de consulta.',
            'description.required' => 'Debe poner sus síntomas.'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $date = $this->input('scheduled_date');
            $doctorId = $this->input('doctor_id');
            $scheduled_time = $this->input('scheduled_time');
            if ($date && $doctorId && $scheduled_time) {
                $start = new Carbon($scheduled_time);
            }else {
                return;
            }

            if (!$this->horarioService->isAvailableInterval($date, $doctorId, $start)) {
                $validator->errors()->add(
                    'available_time', 'La hora seleccionada ya se encuentra reservada por otro paciente.'
                );
            }
        });
    }

}
