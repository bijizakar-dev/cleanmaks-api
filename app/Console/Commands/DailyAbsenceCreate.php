<?php

namespace App\Console\Commands;

use App\Models\EmployeeAbsence;
use App\Models\EmployeeSchedule;
use App\Models\HariLibur;
use App\Models\Setting;
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

        $settingApp = Setting::find(1);

        $userEmployee = User::whereNotNull('employee_id')
                            ->where('status', '1')
                            ->get();

        $hariLibur = HariLibur::where('date', $today)
                            ->where('is_cuti', false)
                            ->first();

        if(!empty($userEmployee)) {
            foreach($userEmployee as $usr) {
                $existingAbsence = EmployeeAbsence::whereDate('date', $today)
                                ->where('employee_id', $usr->employee_id)
                                ->where('user_id', $usr->id)
                                ->where('schedule', 'LIKE', "%{$dayToday}%")
                                ->exists();

                $employeeSchedule = EmployeeSchedule::where('day', $dayToday)
                                ->where('date', $today)
                                ->where('employee_id', $usr->employee_id)
                                ->first();

                if (!$existingAbsence) {
                    $status = 'Belum Absensi';

                    if($employeeSchedule != null ) {
                        $dataSchedule = $employeeSchedule;
                    } else {
                        switch($dayToday) {
                            case 'Monday':
                                $typeSche= $settingApp->monday_type;
                                $timeStart = $settingApp->monday_in;
                                $timeEnd = $settingApp->monday_out;
                                $timeDiff = $settingApp->monday_total;
                                break;
                            case 'Tuesday':
                                $typeSche = $settingApp->tuesday_type;
                                $timeStart = $settingApp->tuesday_in;
                                $timeEnd = $settingApp->tuesday_out;
                                $timeDiff = $settingApp->tuesday_total;
                                break;
                            case 'Wednesday':
                                $typeSche = $settingApp->wednesday_type;
                                $timeStart = $settingApp->wednesday_in;
                                $timeEnd = $settingApp->wednesday_out;
                                $timeDiff = $settingApp->wednesday_total;
                                break;
                            case 'Thursday':
                                $typeSche = $settingApp->thursday_type;
                                $timeStart = $settingApp->thursday_in;
                                $timeEnd = $settingApp->thursday_out;
                                $timeDiff = $settingApp->thursday_total;
                                break;
                            case 'Friday':
                                $typeSche = $settingApp->friday_type;
                                $timeStart = $settingApp->friday_in;
                                $timeEnd = $settingApp->friday_out;
                                $timeDiff = $settingApp->friday_total;
                                break;
                            case 'Saturday':
                                $typeSche = $settingApp->saturday_type;
                                $timeStart = $settingApp->saturday_in;
                                $timeEnd = $settingApp->saturday_out;
                                $timeDiff = $settingApp->saturday_total;
                                break;
                            case 'Sunday':
                                $typeSche = $settingApp->sunday_type;
                                $timeStart = $settingApp->sunday_in;
                                $timeEnd = $settingApp->sunday_out;
                                $timeDiff = $settingApp->sunday_total;
                                break;
                        }

                        $dataSchedule = (object) [
                            "day" => $dayToday,
                            "time_start" => $timeStart,
                            "time_end" => $timeEnd,
                            "time_diff" => $timeDiff,
                            "status" => $typeSche,
                        ];

                        if($typeSche == 0) {
                            $status = 'Holiday';
                        }

                        if($hariLibur != null) {
                            $status = 'Holiday';
                            $dataSchedule->day = $hariLibur->name;
                        }
                    }

                    EmployeeAbsence::create([
                        'date' => $today,
                        'user_id' => $usr->id,
                        'employee_id' => $usr->employee_id,
                        'schedule' => json_encode($dataSchedule, true),
                        'status' => $status
                    ]);
                }
            }
            $this->info('Daily absences created successfully.');
        } else {
            $this->info('Daily absences not created cause 0 User Employee.');
        }

    }
}
