import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\LogbookController::store
* @see app/Http/Controllers/LogbookController.php:187
* @route '/logbook/daily'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/logbook/daily',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LogbookController::store
* @see app/Http/Controllers/LogbookController.php:187
* @route '/logbook/daily'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::store
* @see app/Http/Controllers/LogbookController.php:187
* @route '/logbook/daily'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::store
* @see app/Http/Controllers/LogbookController.php:187
* @route '/logbook/daily'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::store
* @see app/Http/Controllers/LogbookController.php:187
* @route '/logbook/daily'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\LogbookController::update
* @see app/Http/Controllers/LogbookController.php:227
* @route '/logbook/daily/{logbook}'
*/
export const update = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/logbook/daily/{logbook}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\LogbookController::update
* @see app/Http/Controllers/LogbookController.php:227
* @route '/logbook/daily/{logbook}'
*/
update.url = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { logbook: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { logbook: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            logbook: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        logbook: typeof args.logbook === 'object'
        ? args.logbook.id
        : args.logbook,
    }

    return update.definition.url
            .replace('{logbook}', parsedArgs.logbook.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::update
* @see app/Http/Controllers/LogbookController.php:227
* @route '/logbook/daily/{logbook}'
*/
update.put = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\LogbookController::update
* @see app/Http/Controllers/LogbookController.php:227
* @route '/logbook/daily/{logbook}'
*/
const updateForm = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::update
* @see app/Http/Controllers/LogbookController.php:227
* @route '/logbook/daily/{logbook}'
*/
updateForm.put = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\LogbookController::destroy
* @see app/Http/Controllers/LogbookController.php:275
* @route '/logbook/daily/{logbook}'
*/
export const destroy = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/logbook/daily/{logbook}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LogbookController::destroy
* @see app/Http/Controllers/LogbookController.php:275
* @route '/logbook/daily/{logbook}'
*/
destroy.url = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { logbook: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { logbook: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            logbook: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        logbook: typeof args.logbook === 'object'
        ? args.logbook.id
        : args.logbook,
    }

    return destroy.definition.url
            .replace('{logbook}', parsedArgs.logbook.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::destroy
* @see app/Http/Controllers/LogbookController.php:275
* @route '/logbook/daily/{logbook}'
*/
destroy.delete = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\LogbookController::destroy
* @see app/Http/Controllers/LogbookController.php:275
* @route '/logbook/daily/{logbook}'
*/
const destroyForm = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::destroy
* @see app/Http/Controllers/LogbookController.php:275
* @route '/logbook/daily/{logbook}'
*/
destroyForm.delete = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const daily = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default daily