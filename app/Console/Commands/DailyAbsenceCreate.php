<?php

namespace App\Console\Commands;

use App\Models\EmployeeAbsence;
use App\Models\EmployeeSchedule;
use App\Models\HariLibur;
use App\Models\User;
use Illuminate\Console\Command;

class DailyAbsenceCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absence:daily-create';
    protected $description = 'Create new Absence in table employee_absences every day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        $dayToday = date('l', strtotime($today));

        $employeeSchedule = EmployeeSchedule::where('day', $dayToday)->get();
        $hariLibur = HariLibur::where('date', $today)
                        ->where('is_cuti', false)
                        ->first();
        if(!empty($employeeSchedule)) {
            foreach($employeeSchedule as $sche) {
                $user = User::where('employee_id', $sche->employee_id)->first();
                if(!empty($user)) {
                    $existingAbsence = EmployeeAbsence::whereDate('date', $today)
                                    ->where('employee_id', $sche->employee_id)
                                    ->where('user_id', $user->id)
                                    ->where('schedule', 'LIKE', "%{$dayToday}%")
                                    ->exists();

                    if (!$existingAbsence) {
                        $status = 'Belum Absensi';

                        if($hariLibur != null) {
                            $status = $hariLibur->name;
                        }

                        EmployeeAbsence::create([
                            'date' => $today,
                            'user_id' => (!empty($user)) ? $user->id : '',
                            'employee_id' => $sche->employee_id,
                            'schedule' => json_encode($sche->toJson(), true),
                            'status' => $status
                        ]);
                    }
                }
            }
            $this->info('Daily absences created successfully.');
        } else {
            $this->info('Daily absences not created cause 0 schedule.');
        }

    }
}
