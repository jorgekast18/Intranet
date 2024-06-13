<?php

namespace App\Filament\Personal\Resources\TimeSheetResource\Pages;

use App\Filament\Personal\Resources\TimeSheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTimeSheets extends ListRecords
{
    protected static string $resource = TimeSheetResource::class;

    protected function getHeaderActions(): array
    {
        $lastTimesheets = Timesheet::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
        if($lastTimesheets == null){
            return [
                Action::make('inwork')
                    ->label('In Work')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function () {
                        $user = Auth::user();
                        $timesheet = new Timesheet();
                        $timesheet->user_id = $user->id;
                        $timesheet->calendar_id = 1;
                        $timesheet->day_in = Carbon::now();
                        $timesheet->type = 'work';
                        $timesheet->save();
                    }),
                Actions\CreateAction::make(),
            ];
        }
        return [
            Action::make('inwork')
                ->label('In Work')
                ->color('success')
                ->requiresConfirmation()
                ->visible(!$lastTimesheets->day_out == null)
                ->disabled( $lastTimesheets->day_out == null)
                ->action(function () {
                    $user = Auth::user();
                    $timesheet = new Timesheet();
                    $timesheet->user_id = $user->id;
                    $timesheet->calendar_id = 1;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();

                    Notification::make()
                        ->title('Your work has been started')
                        ->success()
                        ->send();
                }),
            Action::make('stopWork')
                ->label('Stop Work')
                ->color('success')
                ->requiresConfirmation()
                ->visible($lastTimesheets->day_out == null)
                ->disabled( !$lastTimesheets->day_out == null)
                ->action(function () use($lastTimesheets) {
                    $lastTimesheets->day_out = Carbon::now();
                    $lastTimesheets->save();

                    Notification::make()
                        ->title('Your work has been stopped')
                        ->success()
                        ->send();
                }),
            Action::make('inpause')
                ->label('In Pause')
                ->color('info')
                ->requiresConfirmation()
                ->visible($lastTimesheets->day_out == null && $lastTimesheets->type == 'work')
                ->disabled( !$lastTimesheets->day_out == null)
                ->action(function () use($lastTimesheets) {
                    $lastTimesheets->day_out = Carbon::now();
                    $lastTimesheets->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'pause';
                    $timesheet->save();
                    Notification::make()
                        ->title('Your pause has been started')
                        ->success()
                        ->send();
                }),
            Action::make('stopPause')
                ->label('Stop Pause')
                ->color('info')
                ->requiresConfirmation()
                ->visible($lastTimesheets->day_out == null && $lastTimesheets->type == 'pause')
                ->disabled( !$lastTimesheets->day_out == null)
                ->action(function () use($lastTimesheets) {
                    $lastTimesheets->day_out = Carbon::now();
                    $lastTimesheets->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();

                    Notification::make()
                        ->title('Your pause has been stopped')
                        ->success()
                        ->send();
                }),
        ];
    }
}
