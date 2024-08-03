<?php

namespace Database\Seeders;

use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;

class SpecialtiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specialties = [
            'Neurología',
            'Quirúrgica',
            'Pediatría',
            'Cardiología',
            'Urología',
            'Medicina forense',
            'Dermatología'
        ];
        foreach ($specialties as $specialtyName) {
            $specialty = Specialty::create([
                'name' => $specialtyName
            ]);
            $specialty->users()->saveMany(
                User::factory(4)->state(['role' => 'doctor'])->make()
            );
        }
        User::find(3)->specialties()->save($specialty);
    }
}
