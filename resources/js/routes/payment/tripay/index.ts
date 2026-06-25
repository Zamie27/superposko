import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\TripayController::callback
* @see app/Http/Controllers/TripayController.php:162
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
* @see app/Http/Controllers/TripayController.php:162
* @route '/payment/tripay/callback'
*/
callback.url = (options?: RouteQueryOptions) => {
    return callback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::callback
* @see app/Http/Controllers/TripayController.php:162
* @route '/payment/tripay/callback'
*/
callback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: callback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::callback
* @see app/Http/Controllers/TripayController.php:162
* @route '/payment/tripay/callback'
*/
const callbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: callback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::callback
* @see app/Http/Controllers/TripayController.php:162
* @route '/payment/tripay/callback'
*/
callbackForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: callback.url(options),
    method: 'post',
})

callback.form = callbackForm

/**
* @see \App\Http\Controllers\TripayController::returnMethod
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
export const returnMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: returnMethod.url(options),
    method: 'get',
})

returnMethod.definition = {
    methods: ["get","head"],
    url: '/payment/tripay/return',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TripayController::returnMethod
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
returnMethod.url = (options?: RouteQueryOptions) => {
    return returnMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::returnMethod
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
returnMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: returnMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TripayController::returnMethod
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
returnMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: returnMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TripayController::returnMethod
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
const returnMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: returnMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TripayController::returnMethod
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
returnMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: returnMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TripayController::returnMethod
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
returnMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: returnMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

returnMethod.form = returnMethodForm

/**
* @see \App\Http\Controllers\TripayController::create
* @see app/Http/Controllers/TripayController.php:21
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
* @see app/Http/Controllers/TripayController.php:21
* @route '/payment/tripay/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::create
* @see app/Http/Controllers/TripayController.php:21
* @route '/payment/tripay/create'
*/
create.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::create
* @see app/Http/Controllers/TripayController.php:21
* @route '/payment/tripay/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: create.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::create
* @see app/Http/Controllers/TripayController.php:21
* @route '/payment/tripay/create'
*/
createForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: create.url(options),
    method: 'post',
})

create.form = createForm

/**
* @see \App\Http\Controllers\TripayController::cancel
* @see app/Http/Controllers/TripayController.php:134
* @route '/payment/tripay/cancel'
*/
export const cancel = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(options),
    method: 'post',
})

cancel.definition = {
    methods: ["post"],
    url: '/payment/tripay/cancel',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TripayController::cancel
* @see app/Http/Controllers/TripayController.php:134
* @route '/payment/tripay/cancel'
*/
cancel.url = (options?: RouteQueryOptions) => {
    return cancel.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::cancel
* @see app/Http/Controllers/TripayController.php:134
* @route '/payment/tripay/cancel'
*/
cancel.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::cancel
* @see app/Http/Controllers/TripayController.php:134
* @route '/payment/tripay/cancel'
*/
const cancelForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::cancel
* @see app/Http/Controllers/TripayController.php:134
* @route '/payment/tripay/cancel'
*/
cancelForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(options),
    method: 'post',
})

cancel.form = cancelForm

const tripay = {
    callback: Object.assign(callback, callback),
    return: Object.assign(returnMethod, returnMethod),
    create: Object.assign(create, create),
    cancel: Object.assign(cancel, cancel),
}

export default tripay