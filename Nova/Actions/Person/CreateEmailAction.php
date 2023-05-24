<?php

namespace App\Nova\Actions\Person;


use App\Http\Controllers\ConnectController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class CreateEmailAction extends Action
{
    use InteractsWithQueue;
//    use Queueable;

    public $name = 'Create e-mail';

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
            if($model->email === null){
                $request = new Request();
                $request->merge([
                    'id' => $model->id,
                ]);
                $result = (new ConnectController())->addConnect($request);
                if ($result === null){
                    return Action::danger('Unable to create email. Check telegram log');
                }

                return Action::message("Created email with credentials!\n login: ".$result['email'].'; password: '.$result['password']);
            }
            else return Action::danger('User has an email');
        }

        return Action::message('Email creating is queued');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
