import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ReportController::create
* @see app/Http/Controllers/ReportController.php:16
* @route '/laporan/buat'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/laporan/buat',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::create
* @see app/Http/Controllers/ReportController.php:16
* @route '/laporan/buat'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::create
* @see app/Http/Controllers/ReportController.php:16
* @route '/laporan/buat'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::create
* @see app/Http/Controllers/ReportController.php:16
* @route '/laporan/buat'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ReportController::create
* @see app/Http/Controllers/ReportController.php:16
* @route '/laporan/buat'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::create
* @see app/Http/Controllers/ReportController.php:16
* @route '/laporan/buat'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::create
* @see app/Http/Controllers/ReportController.php:16
* @route '/laporan/buat'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\ReportController::store
* @see app/Http/Controllers/ReportController.php:29
* @route '/laporan/buat'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/laporan/buat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReportController::store
* @see app/Http/Controllers/ReportController.php:29
* @route '/laporan/buat'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::store
* @see app/Http/Controllers/ReportController.php:29
* @route '/laporan/buat'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ReportController::store
* @see app/Http/Controllers/ReportController.php:29
* @route '/laporan/buat'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ReportController::store
* @see app/Http/Controllers/ReportController.php:29
* @route '/laporan/buat'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const ReportController = { create, store }

export default ReportController