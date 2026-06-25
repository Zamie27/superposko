import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import tripay from './tripay'
import qris from './qris'
/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/payment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
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

const payment = {
    tripay: Object.assign(tripay, tripay),
    index: Object.assign(index, index),
    qris: Object.assign(qris, qris),
}

export default payment