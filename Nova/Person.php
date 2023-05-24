<?php

namespace App\Nova;

use App\Nova\Actions\Person\CreateBitrixAction;
use App\Nova\Actions\Person\CreateEmailAction;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use function Clue\StreamFilter\fun;

class Person extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Person>
     */
    public static $model = \App\Models\Person::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'last_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'email', 'last_name'
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
            ID::make()->sortable(),

            Text::make('Last name', 'last_name')
                ->rules('required', 'max:255')->help('Фамилия'),
            Text::make('First name', 'first_name')
                ->rules('required', 'max:255')->help('Имя'),
            Text::make('Middle name', 'second_name')
                ->rules('max:255')
                ->nullable()
                ->hideFromIndex()
                ->help('Отчество'),
            Text::make('Corp email', 'email')
                ->rules('email', 'max:254')
                ->hideWhenCreating()
                ->help('Корпоративная почта сотрудника'),
            Text::make('Personal email', 'personal_email')
                ->rules('required', 'email', 'max:254')
                ->hideFromIndex()
                ->help('Личная почта сотрудника'),
            Text::make('Phone', 'phone')
                ->rules('required')
                ->help('Телефон'),
            Text::make('Telegram')->hideWhenCreating(),
            Text::make('Position')
                ->help('Должность'),
            Select::make('Type')
                ->options([
                'ТД' => 'Трудовой договор',
                'СМЗ' => 'Самозанятый',
                'ИП' => 'ИП',
                'ГПХ' => 'ГПХ',
            ])
                ->hideFromIndex()
                ->help('Тип договора'),
            BelongsTo::make('Organization', 'organization', 'App\Nova\Organization')->required()->onlyOnDetail(),

            Boolean::make('Active')
                ->default(function (){
                return true;
            })
                ->help('Активен?'),

            HasOne::make('Email', 'mailbox')->hideFromIndex()->hideWhenCreating()->sortable(),
            BelongsToMany::make('Department', 'departments')
                ->hideFromIndex()
                ->searchable(),
            HasOne::make('Bitrix Account', 'bitrixAccount')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            HasOne::make('Yandex Account', 'yandex')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            HasOne::make('LMS User', 'lmsUser')
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            DateTime::make('Created', 'created_at')
                ->hideWhenCreating()
                ->hideFromIndex()
                ->readonly(),
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
            (new CreateEmailAction())->canSee( function ($request){
                return $request->user()->hasPermission('manage-persons');
            }),

            (new CreateBitrixAction())
//                ->canSee(function ($request){
//                return !$this->has('bitrixAccount') || !$this->has('namesBitrixAccount');
//            }),
        ];
    }

    public function title()
    {
        return $this->first_name .' '.$this->last_name;
    }
}
