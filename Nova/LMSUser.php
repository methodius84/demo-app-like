<?php

namespace App\Nova;

use App\Nova\Actions\LMS\FireUserAction;
use App\Nova\Actions\LMS\GetUserInfoAction;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class LMSUser extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\LMSUser>
     */
    public static $model = \App\Models\LMSUser::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'person_id'
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
            ID::make('ID')->sortable(),
            Text::make('Platform ID')->sortable()->hideFromIndex()->hideWhenCreating()->readonly(),

            MultiSelect::make('Roles')->options([
                'ROLE_USER' => 'user',
                'ROLE_ADMIN' => 'admin',
                'ROLE_CONTENT_MANAGER' => 'content-manager',
                'ROLE_SENIOR_CONTENT_MANAGER' => 'senior content-manager',
                'ROLE_SERVICE_MANAGER' => 'service-manager',
                'ROLE_MECHANISM_MANAGER' => 'mechanism-manager',
                'ROLE_SENIOR_CAPTAIN' => 'senior captain',
                'ROLE_CAPTAIN' => 'captain',
                'ROLE_TRAINER' => 'trainer',
                'ROLE_SUPPORT_FIRST_LINE' => 'support-first-line',
                'ROLE_SUPPORT_SECOND_LINE' => 'support-second-line',
                'ROLE_TINDER_SUPPORT' => 'tinder-support',
                'ROLE_QA' => 'qa',
                'ROLE_SALES_MANAGER' => 'sales',
            ])->displayUsingLabels(),

            DateTime::make('Created at')->readonly(),

            BelongsTo::make('Person', 'person')->showOnIndex()->sortable()->searchable()->rules('required', 'unique:lms_users,person_id'),
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
            FireUserAction::make(),
            GetUserInfoAction::make(),
        ];
    }
}
