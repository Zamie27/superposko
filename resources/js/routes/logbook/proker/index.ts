import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\LogbookController::store
* @see app/Http/Controllers/LogbookController.php:86
* @route '/logbook/proker'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/logbook/proker',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LogbookController::store
* @see app/Http/Controllers/LogbookController.php:86
* @route '/logbook/proker'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::store
* @see app/Http/Controllers/LogbookController.php:86
* @route '/logbook/proker'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::store
* @see app/Http/Controllers/LogbookController.php:86
* @route '/logbook/proker'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::store
* @see app/Http/Controllers/LogbookController.php:86
* @route '/logbook/proker'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\LogbookController::update
* @see app/Http/Controllers/LogbookController.php:133
* @route '/logbook/proker/{proker}'
*/
export const update = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/logbook/proker/{proker}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\LogbookController::update
* @see app/Http/Controllers/LogbookController.php:133
* @route '/logbook/proker/{proker}'
*/
update.url = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { proker: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { proker: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            proker: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proker: typeof args.proker === 'object'
        ? args.proker.id
        : args.proker,
    }

    return update.definition.url
            .replace('{proker}', parsedArgs.proker.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::update
* @see app/Http/Controllers/LogbookController.php:133
* @route '/logbook/proker/{proker}'
*/
update.put = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\LogbookController::update
* @see app/Http/Controllers/LogbookController.php:133
* @route '/logbook/proker/{proker}'
*/
const updateForm = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/LogbookController.php:133
* @route '/logbook/proker/{proker}'
*/
updateForm.put = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/LogbookController.php:181
* @route '/logbook/proker/{proker}'
*/
export const destroy = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/logbook/proker/{proker}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LogbookController::destroy
* @see app/Http/Controllers/LogbookController.php:181
* @route '/logbook/proker/{proker}'
*/
destroy.url = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { proker: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { proker: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            proker: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proker: typeof args.proker === 'object'
        ? args.proker.id
        : args.proker,
    }

    return destroy.definition.url
            .replace('{proker}', parsedArgs.proker.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::destroy
* @see app/Http/Controllers/LogbookController.php:181
* @route '/logbook/proker/{proker}'
*/
destroy.delete = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\LogbookController::destroy
* @see app/Http/Controllers/LogbookController.php:181
* @route '/logbook/proker/{proker}'
*/
const destroyForm = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/LogbookController.php:181
* @route '/logbook/proker/{proker}'
*/
destroyForm.delete = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const proker = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default proker