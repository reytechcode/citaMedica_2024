@extends('layouts.panel')

@section('content')

        <div class="card shadow">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Cancelar cita</h3>
                    </div>

                    <div class="col text-right">
                        <a href="{{ url('/miscitas') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-chevron-left"></i>
                            Regresar</a>
                    </div>
                    
                </div>
            </div>
            <div class="card-body">
                @if(session('notification'))
                    <div class="alert alert-success" role="alert">
                         {{ session('notification') }}
                    </div>
                @endif

                    @if($role == 'paciente')
                        <p>Se cancelará tú cita reservada con el médico <b>{{ $appointment->doctor->name }}</b>
                            (especialidad <b>{{ $appointment->specialty->name }}</b> ) 
                            para el día <b>{{ $appointment->scheduled_date }}</b>. 
                        </p>
                    @elseif($role == 'doctor')
                    <p>Se cancelará la cita médica del paciente <b>{{ $appointment->patient->name }}</b>
                        (especialidad <b>{{ $appointment->specialty->name }}</b> ) 
                        para el día <b>{{ $appointment->scheduled_date }}</b>
                        , la hora <b>{{ $appointment->scheduled_time_12 }}</b>.
                     </p>
                    @else
                    <p>Se cancelará la cita médica del paciente <b>{{ $appointment->patient->name }}</b>
                        que será atendido por el Doctor <b>{{ $appointment->doctor->name }}</b>
                        (especialidad <b>{{ $appointment->specialty->name }}</b> ) 
                        para el día <b>{{ $appointment->scheduled_date }}</b>
                        , la hora <b>{{ $appointment->scheduled_time_12 }}</b>. 
                    </p>
                    @endif

                <form action="{{ url('/miscitas/'.$appointment->id.'/cancel') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="justification">Ponga los motivos de la cancelación:</label>
                        <textarea name="justification" id="justification" rows="3" class="form-control" required></textarea>
                    </div>

                    <button class="btn btn-danger" type="submit">Cancelar cita</button>

                </form>

            </div>

        </div>
    
@endsection