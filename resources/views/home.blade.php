@extends('layouts.panel')

@section('content')
<div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">{{ __('Bienvenidos!') }}</div>

                <div class="card-body">
                    {{ __('Cuidamos tu salud en tiempos difíciles.') }}
                </div>
            </div>
                    @if (session('notification'))
                        <div class="alert alert-success mt-4" role="alert">
                            {{ session('notification') }}
                        </div>
                    @endif
        </div>

        @if (auth()->user()->role == 'admin')
        
        <div class="col-xl-7">
          <div class="card shadow">
            <div class="card-header bg-transparent">
              <div class="row align-items-center">
                <div class="col">
                  <h6 class="text-uppercase text-muted ls-1 mb-1">Total de citas</h6>
                  <h2 class="mb-0">Según el día de la semana</h2>
                </div>
              </div>
            </div>
            <div class="card-body">
              <!-- Chart -->
              <div class="chart">
                <canvas id="chart-orders" class="chart-canvas"></canvas>
              </div>
            </div>
          </div>
        </div>
        @endif

      </div>
      
@endsection

@section('scripts')
      <script>
        const valuesByDay = @json($valuesByDay);
      </script>
      <script src="{{ asset('js/charts/home.js') }}"></script>
@endsection
