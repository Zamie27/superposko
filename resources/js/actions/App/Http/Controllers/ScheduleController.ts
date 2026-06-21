import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
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

/**
* @see \App\Http\Controllers\ScheduleController::storeRoster
* @see app/Http/Controllers/ScheduleController.php:76
* @route '/management/schedule/roster'
*/
export const storeRoster = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeRoster.url(options),
    method: 'post',
})

storeRoster.definition = {
    methods: ["post"],
    url: '/management/schedule/roster',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ScheduleController::storeRoster
* @see app/Http/Controllers/ScheduleController.php:76
* @route '/management/schedule/roster'
*/
storeRoster.url = (options?: RouteQueryOptions) => {
    return storeRoster.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ScheduleController::storeRoster
* @see app/Http/Controllers/ScheduleController.php:76
* @route '/management/schedule/roster'
*/
storeRoster.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeRoster.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScheduleController::storeRoster
* @see app/Http/Controllers/ScheduleController.php:76
* @route '/management/schedule/roster'
*/
const storeRosterForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeRoster.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScheduleController::storeRoster
* @see app/Http/Controllers/ScheduleController.php:76
* @route '/management/schedule/roster'
*/
storeRosterForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeRoster.url(options),
    method: 'post',
})

storeRoster.form = storeRosterForm

/**
* @see \App\Http\Controllers\ScheduleController::destroyRoster
* @see app/Http/Controllers/ScheduleController.php:116
* @route '/management/schedule/roster/{roster}'
*/
export const destroyRoster = (args: { roster: number | { id: number } } | [roster: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyRoster.url(args, options),
    method: 'delete',
})

destroyRoster.definition = {
    methods: ["delete"],
    url: '/management/schedule/roster/{roster}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ScheduleController::destroyRoster
* @see app/Http/Controllers/ScheduleController.php:116
* @route '/management/schedule/roster/{roster}'
*/
destroyRoster.url = (args: { roster: number | { id: number } } | [roster: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroyRoster.definition.url
            .replace('{roster}', parsedArgs.roster.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ScheduleController::destroyRoster
* @see app/Http/Controllers/ScheduleController.php:116
* @route '/management/schedule/roster/{roster}'
*/
destroyRoster.delete = (args: { roster: number | { id: number } } | [roster: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyRoster.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ScheduleController::destroyRoster
* @see app/Http/Controllers/ScheduleController.php:116
* @route '/management/schedule/roster/{roster}'
*/
const destroyRosterForm = (args: { roster: number | { id: number } } | [roster: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyRoster.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScheduleController::destroyRoster
* @see app/Http/Controllers/ScheduleController.php:116
* @route '/management/schedule/roster/{roster}'
*/
destroyRosterForm.delete = (args: { roster: number | { id: number } } | [roster: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyRoster.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyRoster.form = destroyRosterForm

/**
* @see \App\Http\Controllers\ScheduleController::storeEvent
* @see app/Http/Controllers/ScheduleController.php:141
* @route '/management/schedule/event'
*/
export const storeEvent = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeEvent.url(options),
    method: 'post',
})

storeEvent.definition = {
    methods: ["post"],
    url: '/management/schedule/event',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ScheduleController::storeEvent
* @see app/Http/Controllers/ScheduleController.php:141
* @route '/management/schedule/event'
*/
storeEvent.url = (options?: RouteQueryOptions) => {
    return storeEvent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ScheduleController::storeEvent
* @see app/Http/Controllers/ScheduleController.php:141
* @route '/management/schedule/event'
*/
storeEvent.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeEvent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScheduleController::storeEvent
* @see app/Http/Controllers/ScheduleController.php:141
* @route '/management/schedule/event'
*/
const storeEventForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeEvent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScheduleController::storeEvent
* @see app/Http/Controllers/ScheduleController.php:141
* @route '/management/schedule/event'
*/
storeEventForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeEvent.url(options),
    method: 'post',
})

storeEvent.form = storeEventForm

/**
* @see \App\Http\Controllers\ScheduleController::updateEvent
* @see app/Http/Controllers/ScheduleController.php:180
* @route '/management/schedule/event/{event}'
*/
export const updateEvent = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateEvent.url(args, options),
    method: 'put',
})

updateEvent.definition = {
    methods: ["put"],
    url: '/management/schedule/event/{event}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ScheduleController::updateEvent
* @see app/Http/Controllers/ScheduleController.php:180
* @route '/management/schedule/event/{event}'
*/
updateEvent.url = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { event: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return updateEvent.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ScheduleController::updateEvent
* @see app/Http/Controllers/ScheduleController.php:180
* @route '/management/schedule/event/{event}'
*/
updateEvent.put = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateEvent.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ScheduleController::updateEvent
* @see app/Http/Controllers/ScheduleController.php:180
* @route '/management/schedule/event/{event}'
*/
const updateEventForm = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateEvent.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScheduleController::updateEvent
* @see app/Http/Controllers/ScheduleController.php:180
* @route '/management/schedule/event/{event}'
*/
updateEventForm.put = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateEvent.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateEvent.form = updateEventForm

/**
* @see \App\Http\Controllers\ScheduleController::destroyEvent
* @see app/Http/Controllers/ScheduleController.php:217
* @route '/management/schedule/event/{event}'
*/
export const destroyEvent = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyEvent.url(args, options),
    method: 'delete',
})

destroyEvent.definition = {
    methods: ["delete"],
    url: '/management/schedule/event/{event}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ScheduleController::destroyEvent
* @see app/Http/Controllers/ScheduleController.php:217
* @route '/management/schedule/event/{event}'
*/
destroyEvent.url = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { event: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return destroyEvent.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ScheduleController::destroyEvent
* @see app/Http/Controllers/ScheduleController.php:217
* @route '/management/schedule/event/{event}'
*/
destroyEvent.delete = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyEvent.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ScheduleController::destroyEvent
* @see app/Http/Controllers/ScheduleController.php:217
* @route '/management/schedule/event/{event}'
*/
const destroyEventForm = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyEvent.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScheduleController::destroyEvent
* @see app/Http/Controllers/ScheduleController.php:217
* @route '/management/schedule/event/{event}'
*/
destroyEventForm.delete = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyEvent.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyEvent.form = destroyEventForm

const ScheduleController = { index, storeRoster, destroyRoster, storeEvent, updateEvent, destroyEvent }

export default ScheduleController