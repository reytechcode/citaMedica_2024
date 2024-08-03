<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $seconds = 120;
        $valuesByDay = Cache::remember('appointments_by_day', $seconds, function () {
            $appointmentsByDay = Appointment::select([

                DB::raw('DAYOFWEEK(scheduled_date) as day'),
                DB::raw('COUNT(*) as count')
            ])
            ->groupBy(DB::raw('DAYOFWEEK(scheduled_date)'))
            ->where('status', 'Confirmada')
            ->get(['day', 'count'])
            ->mapWithKeys(function ($item) {
                return [$item['day'] => $item['count']];
            })->toArray();
    
            $resultsByDay = [];
            for ($i=1; $i<=7; ++$i){
                if (array_key_exists($i, $appointmentsByDay))
                    $resultsByDay[] = $appointmentsByDay[$i];
                else
                    $resultsByDay[] = 0;
            }

            return $resultsByDay;
        });


        

        return view('home', compact('valuesByDay'));
    }
}
