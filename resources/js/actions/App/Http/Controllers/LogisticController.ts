import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LogisticController::index
* @see app/Http/Controllers/LogisticController.php:19
* @route '/management/logistic'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/management/logistic',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LogisticController::index
* @see app/Http/Controllers/LogisticController.php:19
* @route '/management/logistic'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogisticController::index
* @see app/Http/Controllers/LogisticController.php:19
* @route '/management/logistic'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LogisticController::index
* @see app/Http/Controllers/LogisticController.php:19
* @route '/management/logistic'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LogisticController::index
* @see app/Http/Controllers/LogisticController.php:19
* @route '/management/logistic'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LogisticController::index
* @see app/Http/Controllers/LogisticController.php:19
* @route '/management/logistic'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LogisticController::index
* @see app/Http/Controllers/LogisticController.php:19
* @route '/management/logistic'
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
* @see \App\Http\Controllers\LogisticController::store
* @see app/Http/Controllers/LogisticController.php:37
* @route '/management/logistic'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/management/logistic',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LogisticController::store
* @see app/Http/Controllers/LogisticController.php:37
* @route '/management/logistic'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogisticController::store
* @see app/Http/Controllers/LogisticController.php:37
* @route '/management/logistic'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogisticController::store
* @see app/Http/Controllers/LogisticController.php:37
* @route '/management/logistic'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogisticController::store
* @see app/Http/Controllers/LogisticController.php:37
* @route '/management/logistic'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\LogisticController::update
* @see app/Http/Controllers/LogisticController.php:75
* @route '/management/logistic/{logistic}'
*/
export const update = (args: { logistic: number | { id: number } } | [logistic: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/management/logistic/{logistic}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\LogisticController::update
* @see app/Http/Controllers/LogisticController.php:75
* @route '/management/logistic/{logistic}'
*/
update.url = (args: { logistic: number | { id: number } } | [logistic: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { logistic: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { logistic: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            logistic: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        logistic: typeof args.logistic === 'object'
        ? args.logistic.id
        : args.logistic,
    }

    return update.definition.url
            .replace('{logistic}', parsedArgs.logistic.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogisticController::update
* @see app/Http/Controllers/LogisticController.php:75
* @route '/management/logistic/{logistic}'
*/
update.put = (args: { logistic: number | { id: number } } | [logistic: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\LogisticController::update
* @see app/Http/Controllers/LogisticController.php:75
* @route '/management/logistic/{logistic}'
*/
const updateForm = (args: { logistic: number | { id: number } } | [logistic: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogisticController::update
* @see app/Http/Controllers/LogisticController.php:75
* @route '/management/logistic/{logistic}'
*/
updateForm.put = (args: { logistic: number | { id: number } } | [logistic: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\LogisticController::destroy
* @see app/Http/Controllers/LogisticController.php:112
* @route '/management/logistic/{logistic}'
*/
export const destroy = (args: { logistic: number | { id: number } } | [logistic: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/management/logistic/{logistic}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LogisticController::destroy
* @see app/Http/Controllers/LogisticController.php:112
* @route '/management/logistic/{logistic}'
*/
destroy.url = (args: { logistic: number | { id: number } } | [logistic: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { logistic: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { logistic: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            logistic: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        logistic: typeof args.logistic === 'object'
        ? args.logistic.id
        : args.logistic,
    }

    return destroy.definition.url
            .replace('{logistic}', parsedArgs.logistic.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogisticController::destroy
* @see app/Http/Controllers/LogisticController.php:112
* @route '/management/logistic/{logistic}'
*/
destroy.delete = (args: { logistic: number | { id: number } } | [logistic: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\LogisticController::destroy
* @see app/Http/Controllers/LogisticController.php:112
* @route '/management/logistic/{logistic}'
*/
const destroyForm = (args: { logistic: number | { id: number } } | [logistic: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogisticController::destroy
* @see app/Http/Controllers/LogisticController.php:112
* @route '/management/logistic/{logistic}'
*/
destroyForm.delete = (args: { logistic: number | { id: number } } | [logistic: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\LogisticController::barangKeluar
* @see app/Http/Controllers/LogisticController.php:136
* @route '/management/logistic/barang-keluar'
*/
export const barangKeluar = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: barangKeluar.url(options),
    method: 'post',
})

barangKeluar.definition = {
    methods: ["post"],
    url: '/management/logistic/barang-keluar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LogisticController::barangKeluar
* @see app/Http/Controllers/LogisticController.php:136
* @route '/management/logistic/barang-keluar'
*/
barangKeluar.url = (options?: RouteQueryOptions) => {
    return barangKeluar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogisticController::barangKeluar
* @see app/Http/Controllers/LogisticController.php:136
* @route '/management/logistic/barang-keluar'
*/
barangKeluar.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: barangKeluar.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogisticController::barangKeluar
* @see app/Http/Controllers/LogisticController.php:136
* @route '/management/logistic/barang-keluar'
*/
const barangKeluarForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: barangKeluar.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogisticController::barangKeluar
* @see app/Http/Controllers/LogisticController.php:136
* @route '/management/logistic/barang-keluar'
*/
barangKeluarForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: barangKeluar.url(options),
    method: 'post',
})

barangKeluar.form = barangKeluarForm

const LogisticController = { index, store, update, destroy, barangKeluar }

export default LogisticController