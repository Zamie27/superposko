import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Payment\SubscriptionRequestController::store
* @see app/Http/Controllers/Payment/SubscriptionRequestController.php:17
* @route '/payment/qris'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/payment/qris',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Payment\SubscriptionRequestController::store
* @see app/Http/Controllers/Payment/SubscriptionRequestController.php:17
* @route '/payment/qris'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Payment\SubscriptionRequestController::store
* @see app/Http/Controllers/Payment/SubscriptionRequestController.php:17
* @route '/payment/qris'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Payment\SubscriptionRequestController::store
* @see app/Http/Controllers/Payment/SubscriptionRequestController.php:17
* @route '/payment/qris'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Payment\SubscriptionRequestController::store
* @see app/Http/Controllers/Payment/SubscriptionRequestController.php:17
* @route '/payment/qris'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const qris = {
    store: Object.assign(store, store),
}

export default qris