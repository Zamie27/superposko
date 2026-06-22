import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ScheduleController::store
* @see app/Http/Controllers/ScheduleController.php:76
* @route '/management/schedule/roster'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/management/schedule/roster',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ScheduleController::store
* @see app/Http/Controllers/ScheduleController.php:76
* @route '/management/schedule/roster'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ScheduleController::store
* @see app/Http/Controllers/ScheduleController.php:76
* @route '/management/schedule/roster'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScheduleController::store
* @see app/Http/Controllers/ScheduleController.php:76
* @route '/management/schedule/roster'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScheduleController::store
* @see app/Http/Controllers/ScheduleController.php:76
* @route '/management/schedule/roster'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ScheduleController::destroy
* @see app/Http/Controllers/ScheduleController.php:126
* @route '/management/schedule/roster/{roster}'
*/
export const destroy = (args: { roster: number | { id: number } } | [roster: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/management/schedule/roster/{roster}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ScheduleController::destroy
* @see app/Http/Controllers/ScheduleController.php:126
* @route '/management/schedule/roster/{roster}'
*/
destroy.url = (args: { roster: number | { id: number } } | [roster: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { roster: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { roster: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            roster: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        roster: typeof args.roster === 'object'
        ? args.roster.id
        : args.roster,
    }

    return destroy.definition.url
            .replace('{roster}', parsedArgs.roster.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ScheduleController::destroy
* @see app/Http/Controllers/ScheduleController.php:126
* @route '/management/schedule/roster/{roster}'
*/
destroy.delete = (args: { roster: number | { id: number } } | [roster: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ScheduleController::destroy
* @see app/Http/Controllers/ScheduleController.php:126
* @route '/management/schedule/roster/{roster}'
*/
const destroyForm = (args: { roster: number | { id: number } } | [roster: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScheduleController::destroy
* @see app/Http/Controllers/ScheduleController.php:126
* @route '/management/schedule/roster/{roster}'
*/
destroyForm.delete = (args: { roster: number | { id: number } } | [roster: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const roster = {
    store: Object.assign(store, store),
    destroy: Object.assign(destroy, destroy),
}

export default roster