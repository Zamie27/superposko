import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PaymentController::notification
* @see app/Http/Controllers/PaymentController.php:153
* @route '/payment/notification'
*/
export const notification = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: notification.url(options),
    method: 'post',
})

notification.definition = {
    methods: ["post"],
    url: '/payment/notification',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PaymentController::notification
* @see app/Http/Controllers/PaymentController.php:153
* @route '/payment/notification'
*/
notification.url = (options?: RouteQueryOptions) => {
    return notification.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::notification
* @see app/Http/Controllers/PaymentController.php:153
* @route '/payment/notification'
*/
notification.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: notification.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::notification
* @see app/Http/Controllers/PaymentController.php:153
* @route '/payment/notification'
*/
const notificationForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: notification.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::notification
* @see app/Http/Controllers/PaymentController.php:153
* @route '/payment/notification'
*/
notificationForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: notification.url(options),
    method: 'post',
})

notification.form = notificationForm

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:32
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
* @see app/Http/Controllers/PaymentController.php:32
* @route '/payment'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:32
* @route '/payment'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:32
* @route '/payment'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:32
* @route '/payment'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:32
* @route '/payment'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::index
* @see app/Http/Controllers/PaymentController.php:32
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

/**
* @see \App\Http\Controllers\PaymentController::token_user
* @see app/Http/Controllers/PaymentController.php:49
* @route '/payment/token'
*/
export const token_user = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: token_user.url(options),
    method: 'post',
})

token_user.definition = {
    methods: ["post"],
    url: '/payment/token',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PaymentController::token_user
* @see app/Http/Controllers/PaymentController.php:49
* @route '/payment/token'
*/
token_user.url = (options?: RouteQueryOptions) => {
    return token_user.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::token_user
* @see app/Http/Controllers/PaymentController.php:49
* @route '/payment/token'
*/
token_user.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: token_user.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::token_user
* @see app/Http/Controllers/PaymentController.php:49
* @route '/payment/token'
*/
const token_userForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: token_user.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::token_user
* @see app/Http/Controllers/PaymentController.php:49
* @route '/payment/token'
*/
token_userForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: token_user.url(options),
    method: 'post',
})

token_user.form = token_userForm

/**
* @see \App\Http\Controllers\PaymentController::success
* @see app/Http/Controllers/PaymentController.php:98
* @route '/payment/success'
*/
export const success = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: success.url(options),
    method: 'post',
})

success.definition = {
    methods: ["post"],
    url: '/payment/success',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PaymentController::success
* @see app/Http/Controllers/PaymentController.php:98
* @route '/payment/success'
*/
success.url = (options?: RouteQueryOptions) => {
    return success.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::success
* @see app/Http/Controllers/PaymentController.php:98
* @route '/payment/success'
*/
success.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: success.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::success
* @see app/Http/Controllers/PaymentController.php:98
* @route '/payment/success'
*/
const successForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: success.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::success
* @see app/Http/Controllers/PaymentController.php:98
* @route '/payment/success'
*/
successForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: success.url(options),
    method: 'post',
})

success.form = successForm

const payment = {
    notification: Object.assign(notification, notification),
    index: Object.assign(index, index),
    token_user: Object.assign(token_user, token_user),
    success: Object.assign(success, success),
}

export default payment