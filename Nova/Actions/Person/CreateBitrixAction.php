<?php

namespace App\Nova\Actions\Person;

use App\Http\Controllers\Bitrix\UserController;
use App\Http\Controllers\bitrixController;
use App\Http\Controllers\ConnectController;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class CreateBitrixAction extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model){
                $request = new Request();
                $request->merge([
                    'id' => $model->id,
                ]);
                $result = resolve(UserController::class)->add((new Request())->merge([
                    'id' => $model->id,
                    'bitrix' => $fields->which_bitrix,
                ]));
                if ($result === true){
                    return Action::message("Created bitrix for User");
                }
                else {
                    return Action::danger("Failed creating Bitrix" . $result->getMessage());
                }
            }
        return Action::message('Creating bitrix being queued');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Which Bitrix')
                ->options([
                    1 => 'Like',
                    3 => 'Names',
                ])
                ->rules('required'),
        ];
    }
}
