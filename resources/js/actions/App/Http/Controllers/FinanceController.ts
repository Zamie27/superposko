import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\FinanceController::index
* @see app/Http/Controllers/FinanceController.php:21
* @route '/finance'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/finance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\FinanceController::index
* @see app/Http/Controllers/FinanceController.php:21
* @route '/finance'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\FinanceController::index
* @see app/Http/Controllers/FinanceController.php:21
* @route '/finance'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FinanceController::index
* @see app/Http/Controllers/FinanceController.php:21
* @route '/finance'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\FinanceController::index
* @see app/Http/Controllers/FinanceController.php:21
* @route '/finance'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FinanceController::index
* @see app/Http/Controllers/FinanceController.php:21
* @route '/finance'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FinanceController::index
* @see app/Http/Controllers/FinanceController.php:21
* @route '/finance'
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
* @see \App\Http\Controllers\FinanceController::store
* @see app/Http/Controllers/FinanceController.php:84
* @route '/finance'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/finance',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\FinanceController::store
* @see app/Http/Controllers/FinanceController.php:84
* @route '/finance'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\FinanceController::store
* @see app/Http/Controllers/FinanceController.php:84
* @route '/finance'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\FinanceController::store
* @see app/Http/Controllers/FinanceController.php:84
* @route '/finance'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\FinanceController::store
* @see app/Http/Controllers/FinanceController.php:84
* @route '/finance'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\FinanceController::update
* @see app/Http/Controllers/FinanceController.php:142
* @route '/finance/{finance}'
*/
export const update = (args: { finance: number | { id: number } } | [finance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/finance/{finance}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\FinanceController::update
* @see app/Http/Controllers/FinanceController.php:142
* @route '/finance/{finance}'
*/
update.url = (args: { finance: number | { id: number } } | [finance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { finance: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { finance: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            finance: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        finance: typeof args.finance === 'object'
        ? args.finance.id
        : args.finance,
    }

    return update.definition.url
            .replace('{finance}', parsedArgs.finance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\FinanceController::update
* @see app/Http/Controllers/FinanceController.php:142
* @route '/finance/{finance}'
*/
update.post = (args: { finance: number | { id: number } } | [finance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\FinanceController::update
* @see app/Http/Controllers/FinanceController.php:142
* @route '/finance/{finance}'
*/
const updateForm = (args: { finance: number | { id: number } } | [finance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\FinanceController::update
* @see app/Http/Controllers/FinanceController.php:142
* @route '/finance/{finance}'
*/
updateForm.post = (args: { finance: number | { id: number } } | [finance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, options),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\FinanceController::destroy
* @see app/Http/Controllers/FinanceController.php:201
* @route '/finance/{finance}'
*/
export const destroy = (args: { finance: number | { id: number } } | [finance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/finance/{finance}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\FinanceController::destroy
* @see app/Http/Controllers/FinanceController.php:201
* @route '/finance/{finance}'
*/
destroy.url = (args: { finance: number | { id: number } } | [finance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { finance: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { finance: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            finance: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        finance: typeof args.finance === 'object'
        ? args.finance.id
        : args.finance,
    }

    return destroy.definition.url
            .replace('{finance}', parsedArgs.finance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\FinanceController::destroy
* @see app/Http/Controllers/FinanceController.php:201
* @route '/finance/{finance}'
*/
destroy.delete = (args: { finance: number | { id: number } } | [finance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\FinanceController::destroy
* @see app/Http/Controllers/FinanceController.php:201
* @route '/finance/{finance}'
*/
const destroyForm = (args: { finance: number | { id: number } } | [finance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\FinanceController::destroy
* @see app/Http/Controllers/FinanceController.php:201
* @route '/finance/{finance}'
*/
destroyForm.delete = (args: { finance: number | { id: number } } | [finance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const FinanceController = { index, store, update, destroy }

export default FinanceController