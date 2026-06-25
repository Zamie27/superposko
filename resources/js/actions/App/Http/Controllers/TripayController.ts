import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TripayController::handleCallback
* @see app/Http/Controllers/TripayController.php:162
* @route '/payment/tripay/callback'
*/
export const handleCallback = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handleCallback.url(options),
    method: 'post',
})

handleCallback.definition = {
    methods: ["post"],
    url: '/payment/tripay/callback',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TripayController::handleCallback
* @see app/Http/Controllers/TripayController.php:162
* @route '/payment/tripay/callback'
*/
handleCallback.url = (options?: RouteQueryOptions) => {
    return handleCallback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::handleCallback
* @see app/Http/Controllers/TripayController.php:162
* @route '/payment/tripay/callback'
*/
handleCallback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handleCallback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::handleCallback
* @see app/Http/Controllers/TripayController.php:162
* @route '/payment/tripay/callback'
*/
const handleCallbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handleCallback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::handleCallback
* @see app/Http/Controllers/TripayController.php:162
* @route '/payment/tripay/callback'
*/
handleCallbackForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handleCallback.url(options),
    method: 'post',
})

handleCallback.form = handleCallbackForm

/**
* @see \App\Http\Controllers\TripayController::handleReturn
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
export const handleReturn = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: handleReturn.url(options),
    method: 'get',
})

handleReturn.definition = {
    methods: ["get","head"],
    url: '/payment/tripay/return',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TripayController::handleReturn
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
handleReturn.url = (options?: RouteQueryOptions) => {
    return handleReturn.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::handleReturn
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
handleReturn.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: handleReturn.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TripayController::handleReturn
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
handleReturn.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: handleReturn.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TripayController::handleReturn
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
const handleReturnForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleReturn.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TripayController::handleReturn
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
handleReturnForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleReturn.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TripayController::handleReturn
* @see app/Http/Controllers/TripayController.php:278
* @route '/payment/tripay/return'
*/
handleReturnForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleReturn.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

handleReturn.form = handleReturnForm

/**
* @see \App\Http\Controllers\TripayController::createPayment
* @see app/Http/Controllers/TripayController.php:21
* @route '/payment/tripay/create'
*/
export const createPayment = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createPayment.url(options),
    method: 'post',
})

createPayment.definition = {
    methods: ["post"],
    url: '/payment/tripay/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TripayController::createPayment
* @see app/Http/Controllers/TripayController.php:21
* @route '/payment/tripay/create'
*/
createPayment.url = (options?: RouteQueryOptions) => {
    return createPayment.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::createPayment
* @see app/Http/Controllers/TripayController.php:21
* @route '/payment/tripay/create'
*/
createPayment.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createPayment.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::createPayment
* @see app/Http/Controllers/TripayController.php:21
* @route '/payment/tripay/create'
*/
const createPaymentForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createPayment.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::createPayment
* @see app/Http/Controllers/TripayController.php:21
* @route '/payment/tripay/create'
*/
createPaymentForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createPayment.url(options),
    method: 'post',
})

createPayment.form = createPaymentForm

/**
* @see \App\Http\Controllers\TripayController::cancelCurrentPayment
* @see app/Http/Controllers/TripayController.php:134
* @route '/payment/tripay/cancel'
*/
export const cancelCurrentPayment = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancelCurrentPayment.url(options),
    method: 'post',
})

cancelCurrentPayment.definition = {
    methods: ["post"],
    url: '/payment/tripay/cancel',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TripayController::cancelCurrentPayment
* @see app/Http/Controllers/TripayController.php:134
* @route '/payment/tripay/cancel'
*/
cancelCurrentPayment.url = (options?: RouteQueryOptions) => {
    return cancelCurrentPayment.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::cancelCurrentPayment
* @see app/Http/Controllers/TripayController.php:134
* @route '/payment/tripay/cancel'
*/
cancelCurrentPayment.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancelCurrentPayment.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::cancelCurrentPayment
* @see app/Http/Controllers/TripayController.php:134
* @route '/payment/tripay/cancel'
*/
const cancelCurrentPaymentForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancelCurrentPayment.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::cancelCurrentPayment
* @see app/Http/Controllers/TripayController.php:134
* @route '/payment/tripay/cancel'
*/
cancelCurrentPaymentForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancelCurrentPayment.url(options),
    method: 'post',
})

cancelCurrentPayment.form = cancelCurrentPaymentForm

const TripayController = { handleCallback, handleReturn, createPayment, cancelCurrentPayment }

export default TripayController