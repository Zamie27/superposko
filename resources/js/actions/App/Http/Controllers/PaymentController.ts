import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
export const showCheckoutPage = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCheckoutPage.url(options),
    method: 'get',
})

showCheckoutPage.definition = {
    methods: ["get","head"],
    url: '/payment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
showCheckoutPage.url = (options?: RouteQueryOptions) => {
    return showCheckoutPage.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
showCheckoutPage.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCheckoutPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
showCheckoutPage.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showCheckoutPage.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
const showCheckoutPageForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCheckoutPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
showCheckoutPageForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCheckoutPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:17
* @route '/payment'
*/
showCheckoutPageForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCheckoutPage.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showCheckoutPage.form = showCheckoutPageForm

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:71
* @route '/admin/payment/test'
*/
export const showTestPage = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showTestPage.url(options),
    method: 'get',
})

showTestPage.definition = {
    methods: ["get","head"],
    url: '/admin/payment/test',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:71
* @route '/admin/payment/test'
*/
showTestPage.url = (options?: RouteQueryOptions) => {
    return showTestPage.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:71
* @route '/admin/payment/test'
*/
showTestPage.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showTestPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:71
* @route '/admin/payment/test'
*/
showTestPage.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showTestPage.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:71
* @route '/admin/payment/test'
*/
const showTestPageForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showTestPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:71
* @route '/admin/payment/test'
*/
showTestPageForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showTestPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:71
* @route '/admin/payment/test'
*/
showTestPageForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showTestPage.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showTestPage.form = showTestPageForm

const PaymentController = { showCheckoutPage, showTestPage }

export default PaymentController