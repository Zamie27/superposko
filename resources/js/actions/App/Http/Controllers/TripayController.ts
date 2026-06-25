import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TripayController::handleCallback
* @see app/Http/Controllers/TripayController.php:59
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
* @see app/Http/Controllers/TripayController.php:59
* @route '/payment/tripay/callback'
*/
handleCallback.url = (options?: RouteQueryOptions) => {
    return handleCallback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::handleCallback
* @see app/Http/Controllers/TripayController.php:59
* @route '/payment/tripay/callback'
*/
handleCallback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handleCallback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::handleCallback
* @see app/Http/Controllers/TripayController.php:59
* @route '/payment/tripay/callback'
*/
const handleCallbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handleCallback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::handleCallback
* @see app/Http/Controllers/TripayController.php:59
* @route '/payment/tripay/callback'
*/
handleCallbackForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handleCallback.url(options),
    method: 'post',
})

handleCallback.form = handleCallbackForm

/**
* @see \App\Http\Controllers\TripayController::createPayment
* @see app/Http/Controllers/TripayController.php:20
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
* @see app/Http/Controllers/TripayController.php:20
* @route '/payment/tripay/create'
*/
createPayment.url = (options?: RouteQueryOptions) => {
    return createPayment.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TripayController::createPayment
* @see app/Http/Controllers/TripayController.php:20
* @route '/payment/tripay/create'
*/
createPayment.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createPayment.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::createPayment
* @see app/Http/Controllers/TripayController.php:20
* @route '/payment/tripay/create'
*/
const createPaymentForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createPayment.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TripayController::createPayment
* @see app/Http/Controllers/TripayController.php:20
* @route '/payment/tripay/create'
*/
createPaymentForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createPayment.url(options),
    method: 'post',
})

createPayment.form = createPaymentForm

const TripayController = { handleCallback, createPayment }

export default TripayController