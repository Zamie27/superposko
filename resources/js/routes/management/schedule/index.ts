import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
import roster from './roster'
import event from './event'
/**
* @see \App\Http\Controllers\ScheduleController::index
* @see app/Http/Controllers/ScheduleController.php:21
* @route '/management/schedule'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/management/schedule',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ScheduleController::index
* @see app/Http/Controllers/ScheduleController.php:21
* @route '/management/schedule'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ScheduleController::index
* @see app/Http/Controllers/ScheduleController.php:21
* @route '/management/schedule'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ScheduleController::index
* @see app/Http/Controllers/ScheduleController.php:21
* @route '/management/schedule'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ScheduleController::index
* @see app/Http/Controllers/ScheduleController.php:21
* @route '/management/schedule'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ScheduleController::index
* @see app/Http/Controllers/ScheduleController.php:21
* @route '/management/schedule'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ScheduleController::index
* @see app/Http/Controllers/ScheduleController.php:21
* @route '/management/schedule'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const schedule = {
    index: Object.assign(index, index),
    roster: Object.assign(roster, roster),
    event: Object.assign(event, event),
}

export default schedule