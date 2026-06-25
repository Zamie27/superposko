import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PaymentController::handleNotification
* @see app/Http/Controllers/PaymentController.php:165
* @route '/payment/notification'
*/
export const handleNotification = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: handleNotification.url(options),
    method: 'get',
})

handleNotification.definition = {
    methods: ["get","post","head"],
    url: '/payment/notification',
} satisfies RouteDefinition<["get","post","head"]>

/**
* @see \App\Http\Controllers\PaymentController::handleNotification
* @see app/Http/Controllers/PaymentController.php:165
* @route '/payment/notification'
*/
handleNotification.url = (options?: RouteQueryOptions) => {
    return handleNotification.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::handleNotification
* @see app/Http/Controllers/PaymentController.php:165
* @route '/payment/notification'
*/
handleNotification.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: handleNotification.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::handleNotification
* @see app/Http/Controllers/PaymentController.php:165
* @route '/payment/notification'
*/
handleNotification.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handleNotification.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::handleNotification
* @see app/Http/Controllers/PaymentController.php:165
* @route '/payment/notification'
*/
handleNotification.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: handleNotification.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PaymentController::handleNotification
* @see app/Http/Controllers/PaymentController.php:165
* @route '/payment/notification'
*/
const handleNotificationForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleNotification.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::handleNotification
* @see app/Http/Controllers/PaymentController.php:165
* @route '/payment/notification'
*/
handleNotificationForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleNotification.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::handleNotification
* @see app/Http/Controllers/PaymentController.php:165
* @route '/payment/notification'
*/
handleNotificationForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handleNotification.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::handleNotification
* @see app/Http/Controllers/PaymentController.php:165
* @route '/payment/notification'
*/
handleNotificationForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleNotification.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

handleNotification.form = handleNotificationForm

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:34
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
* @see app/Http/Controllers/PaymentController.php:34
* @route '/payment'
*/
showCheckoutPage.url = (options?: RouteQueryOptions) => {
    return showCheckoutPage.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:34
* @route '/payment'
*/
showCheckoutPage.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCheckoutPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:34
* @route '/payment'
*/
showCheckoutPage.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showCheckoutPage.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:34
* @route '/payment'
*/
const showCheckoutPageForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCheckoutPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:34
* @route '/payment'
*/
showCheckoutPageForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCheckoutPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showCheckoutPage
* @see app/Http/Controllers/PaymentController.php:34
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
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:61
* @route '/payment/token'
*/
const createSnapToken231e3cdb958ff8c0705143ef203cf96a = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createSnapToken231e3cdb958ff8c0705143ef203cf96a.url(options),
    method: 'post',
})

createSnapToken231e3cdb958ff8c0705143ef203cf96a.definition = {
    methods: ["post"],
    url: '/payment/token',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:61
* @route '/payment/token'
*/
createSnapToken231e3cdb958ff8c0705143ef203cf96a.url = (options?: RouteQueryOptions) => {
    return createSnapToken231e3cdb958ff8c0705143ef203cf96a.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:61
* @route '/payment/token'
*/
createSnapToken231e3cdb958ff8c0705143ef203cf96a.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createSnapToken231e3cdb958ff8c0705143ef203cf96a.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:61
* @route '/payment/token'
*/
const createSnapToken231e3cdb958ff8c0705143ef203cf96aForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createSnapToken231e3cdb958ff8c0705143ef203cf96a.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:61
* @route '/payment/token'
*/
createSnapToken231e3cdb958ff8c0705143ef203cf96aForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createSnapToken231e3cdb958ff8c0705143ef203cf96a.url(options),
    method: 'post',
})

createSnapToken231e3cdb958ff8c0705143ef203cf96a.form = createSnapToken231e3cdb958ff8c0705143ef203cf96aForm
/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:61
* @route '/admin/payment/test/token'
*/
const createSnapTokenf4cc32ff538dabf70948ddef53890123 = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createSnapTokenf4cc32ff538dabf70948ddef53890123.url(options),
    method: 'post',
})

createSnapTokenf4cc32ff538dabf70948ddef53890123.definition = {
    methods: ["post"],
    url: '/admin/payment/test/token',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:61
* @route '/admin/payment/test/token'
*/
createSnapTokenf4cc32ff538dabf70948ddef53890123.url = (options?: RouteQueryOptions) => {
    return createSnapTokenf4cc32ff538dabf70948ddef53890123.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:61
* @route '/admin/payment/test/token'
*/
createSnapTokenf4cc32ff538dabf70948ddef53890123.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createSnapTokenf4cc32ff538dabf70948ddef53890123.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:61
* @route '/admin/payment/test/token'
*/
const createSnapTokenf4cc32ff538dabf70948ddef53890123Form = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createSnapTokenf4cc32ff538dabf70948ddef53890123.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::createSnapToken
* @see app/Http/Controllers/PaymentController.php:61
* @route '/admin/payment/test/token'
*/
createSnapTokenf4cc32ff538dabf70948ddef53890123Form.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createSnapTokenf4cc32ff538dabf70948ddef53890123.url(options),
    method: 'post',
})

createSnapTokenf4cc32ff538dabf70948ddef53890123.form = createSnapTokenf4cc32ff538dabf70948ddef53890123Form

/**
* Multiple routes resolve to \App\Http\Controllers\PaymentController::createSnapToken, so this export is a
* dictionary keyed by URI rather than a callable. Call a specific route with `createSnapToken['<uri>'](...)`,
* or import the route by name from your generated `routes/` directory.
*/
export const createSnapToken = {
    '/payment/token': createSnapToken231e3cdb958ff8c0705143ef203cf96a,
    '/admin/payment/test/token': createSnapTokenf4cc32ff538dabf70948ddef53890123,
}

/**
* @see \App\Http\Controllers\PaymentController::handlePaymentSuccess
* @see app/Http/Controllers/PaymentController.php:110
* @route '/payment/success'
*/
export const handlePaymentSuccess = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handlePaymentSuccess.url(options),
    method: 'post',
})

handlePaymentSuccess.definition = {
    methods: ["post"],
    url: '/payment/success',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PaymentController::handlePaymentSuccess
* @see app/Http/Controllers/PaymentController.php:110
* @route '/payment/success'
*/
handlePaymentSuccess.url = (options?: RouteQueryOptions) => {
    return handlePaymentSuccess.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::handlePaymentSuccess
* @see app/Http/Controllers/PaymentController.php:110
* @route '/payment/success'
*/
handlePaymentSuccess.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handlePaymentSuccess.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::handlePaymentSuccess
* @see app/Http/Controllers/PaymentController.php:110
* @route '/payment/success'
*/
const handlePaymentSuccessForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handlePaymentSuccess.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::handlePaymentSuccess
* @see app/Http/Controllers/PaymentController.php:110
* @route '/payment/success'
*/
handlePaymentSuccessForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handlePaymentSuccess.url(options),
    method: 'post',
})

handlePaymentSuccess.form = handlePaymentSuccessForm

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:23
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
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
showTestPage.url = (options?: RouteQueryOptions) => {
    return showTestPage.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
showTestPage.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showTestPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
showTestPage.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showTestPage.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
const showTestPageForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showTestPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
showTestPageForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showTestPage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::showTestPage
* @see app/Http/Controllers/PaymentController.php:23
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

const PaymentController = { handleNotification, showCheckoutPage, createSnapToken, handlePaymentSuccess, showTestPage }

export default PaymentController