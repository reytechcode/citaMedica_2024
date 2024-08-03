<?php
    use Illuminate\Support\Str;
?>

@extends('layouts.panel')

@section('content')

    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Editar perfil</h3>
                </div>
                
            </div>
        </div>

        <div class="card-body">

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Por favor!!</strong> {{ $error }}
                    </div>
                @endforeach
            @endif

            @if (session('notification'))
                <div class="alert alert-success" role="alert">
                    {{ session('notification') }}
                </div>
            @endif

            <form action="{{ url('/profile') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre completo</label>
                    <input name="name" id="name" 
                    value="{{ old('name', $user->name) }}"
                    type="text" class="form-control"
                    required></input>
                </div>
                <div class="form-group">
                    <label for="phone">Número de teléfono</label>
                    <input name="phone" id="phone" 
                    value="{{ old('phone', $user->phone) }}"
                    type="text" class="form-control"
                    required></input>
                </div>
                <div class="form-group">
                    <label for="address">Dirección de domicilio</label>
                    <input name="address" id="address" 
                    value="{{ old('address', $user->address) }}"
                    type="text" class="form-control"
                    required></input>
                </div>

                <button type="submit" class="btn btn-sm btn-primary">Guardar Cambios</button>
            </form>
        </div>

    </div>

@endsection
