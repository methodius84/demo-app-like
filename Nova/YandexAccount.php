<?php

namespace App\Nova;

use App\Nova\Actions\Yandex\ActivateAccountAction;
use App\Nova\Actions\Yandex\FireAccountAction;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class YandexAccount extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\YandexAccount>
     */
    public static $model = \App\Models\YandexAccount::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'email';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'email',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make('Platform ID')->sortable(),
            Text::make('Email'),
            Text::make('Last name','last_name'),
            Boolean::make('Active', 'is_active')->hideWhenCreating(),
            BelongsTo::make('Person', 'person'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new ActivateAccountAction())->canSee(function ($request){
                return $request->user()->hasPermission('manage-persons');
            }),
            // TODO разобраться, почему не работает коллбэк с авторизацией
            (new FireAccountAction())
                ->canSee(function ($request){
//                    dd($this);
                    return $request->user()->hasPermission('manage-persons');
              }),
        ];
    }

    public function title()
    {
        return $this->first_name .' '.$this->last_name;
    }
}
