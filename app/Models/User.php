<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cedula',
        'address',
        'phone',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'pivot'
    ];

    public static $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ];

    public static function createPatient(array $data) {
        return self::create(
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => 'paciente',
                'password' => Hash::make($data['password']),
            ]
        );
    }

    public function specialties(){
        return $this->belongsToMany(Specialty::class)->withTimestamps();
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopePatients($query){
        return $query->where('role', 'paciente');
    }
    public function scopeDoctors($query){
        return $query->where('role', 'doctor');
    }

    public function asDoctorAppointments(){
        return $this->hasMany(Appointment::class, 'doctor_id');
    }
    public function attendedAppointments(){
        return $this->asDoctorAppointments()->where('status', 'Atendida');
    }
    public function cancellAppointments(){
        return $this->asDoctorAppointments()->where('status', 'Cancelada');
    }

    public function asPatientAppointments(){
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sendFCM($message) {
        return fcm()
        ->to([
            $this->device_token
        ]) 
        ->priority('high')
        ->timeToLive(0)
        ->notification([
            'title' => config('app.name'),
            'body' => $message,
        ])
        ->send();
    }

}
