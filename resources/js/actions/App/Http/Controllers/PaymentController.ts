import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:19
* @route '/host/payment/test'
*/
export const showTestPage = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showTestPage.url(options),
    method: 'get',
})

showTestPage.definition = {
    methods: ["get","head"],
    url: '/host/payment/test',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:19
* @route '/host/payment/test'
*/
showTestPage.url = (options?: RouteQueryOptions) => {
    return showTestPage.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:19
* @route '/host/payment/test'
*/
showTestPage.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showTestPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:19
* @route '/host/payment/test'
*/
showTestPage.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showTestPage.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:19
* @route '/host/payment/test'
*/
const showTestPageForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showTestPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:19
* @route '/host/payment/test'
*/
showTestPageForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showTestPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:19
* @route '/host/payment/test'
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

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:30
* @route '/host/payment/test/token'
*/
export const createSnapToken = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createSnapToken.url(options),
    method: 'post',
})

createSnapToken.definition = {
    methods: ["post"],
    url: '/host/payment/test/token',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:30
* @route '/host/payment/test/token'
*/
createSnapToken.url = (options?: RouteQueryOptions) => {
    return createSnapToken.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:30
* @route '/host/payment/test/token'
*/
createSnapToken.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createSnapToken.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:30
* @route '/host/payment/test/token'
*/
const createSnapTokenForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createSnapToken.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:30
* @route '/host/payment/test/token'
*/
createSnapTokenForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createSnapToken.url(options),
    method: 'post',
})

createSnapToken.form = createSnapTokenForm

const PaymentController = { showTestPage, createSnapToken }

export default PaymentController