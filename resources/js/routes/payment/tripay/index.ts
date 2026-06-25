import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\TripayController::callback
* @see app/Http/Controllers/TripayController.php:59
* @route '/payment/tripay/callback'
*/
export const callback = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: callback.url(options),
    method: 'post',
})

callback.definition = {
    methods: ["post"],
    url: '/payment/tripay/callback',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TripayController::callback
* @see app/Http/Controllers/TripayController.php:59
* @route '/payment/tripay/callback'
*/
callback.url = (options?: RouteQueryOptions) => {
    return callback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::callback
* @see app/Http/Controllers/TripayController.php:59
* @route '/payment/tripay/callback'
*/
callback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: callback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::callback
* @see app/Http/Controllers/TripayController.php:59
* @route '/payment/tripay/callback'
*/
const callbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: callback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::callback
* @see app/Http/Controllers/TripayController.php:59
* @route '/payment/tripay/callback'
*/
callbackForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: callback.url(options),
    method: 'post',
})

callback.form = callbackForm

/**
* @see \App\Http\Controllers\TripayController::create
* @see app/Http/Controllers/TripayController.php:20
* @route '/payment/tripay/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

create.definition = {
    methods: ["post"],
    url: '/payment/tripay/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TripayController::create
* @see app/Http/Controllers/TripayController.php:20
* @route '/payment/tripay/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::create
* @see app/Http/Controllers/TripayController.php:20
* @route '/payment/tripay/create'
*/
create.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::create
* @see app/Http/Controllers/TripayController.php:20
* @route '/payment/tripay/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: create.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::create
* @see app/Http/Controllers/TripayController.php:20
* @route '/payment/tripay/create'
*/
createForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: create.url(options),
    method: 'post',
})

create.form = createForm

const tripay = {
    callback: Object.assign(callback, callback),
    create: Object.assign(create, create),
}

export default tripay