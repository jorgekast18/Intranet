<?php

namespace App\Filament\Personal\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PersonalWidgetStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Holidays', $this->getPendingHolidays(Auth::user())),
            Stat::make('approved Holidays', $this->getApprovedHolidays(Auth::user())),
            Stat::make('Total Hours', $this->getTotalHours(Auth::user())),
            //
        ];
    }

    protected function getPendingHolidays(User $user): ?string
    {
        $totalPendingHolidays = Holiday::where('user_id', $user->id)->where('type', 'pending')->count();
        return $totalPendingHolidays;
    }

    protected function getApprovedHolidays(User $user): ?string
    {
        $totalPendingHolidays = Holiday::where('user_id', $user->id)->where('type', 'approved')->count();
        return $totalPendingHolidays;
    }

    protected function getTotalHours(User $user)
    {
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'work')->get();

        $sumHours = 0;
        foreach ($timesheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in);
            $endTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $endTime->diffInSeconds($startTime);
            $sumHours += $totalDuration;
        }

        $totalTimeFormated = gmdate("H:i:s", $sumHours);
        return $totalTimeFormated;
    }
}
