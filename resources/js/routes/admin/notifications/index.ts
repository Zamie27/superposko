import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::index
* @see app/Http/Controllers/Admin/AdminNotificationController.php:23
* @route '/admin/notifications'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::index
* @see app/Http/Controllers/Admin/AdminNotificationController.php:23
* @route '/admin/notifications'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::index
* @see app/Http/Controllers/Admin/AdminNotificationController.php:23
* @route '/admin/notifications'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::index
* @see app/Http/Controllers/Admin/AdminNotificationController.php:23
* @route '/admin/notifications'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::index
* @see app/Http/Controllers/Admin/AdminNotificationController.php:23
* @route '/admin/notifications'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::index
* @see app/Http/Controllers/Admin/AdminNotificationController.php:23
* @route '/admin/notifications'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::index
* @see app/Http/Controllers/Admin/AdminNotificationController.php:23
* @route '/admin/notifications'
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
* @see \App\Http\Controllers\Admin\AdminNotificationController::send_push
* @see app/Http/Controllers/Admin/AdminNotificationController.php:51
* @route '/admin/notifications/send-push'
*/
export const send_push = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send_push.url(options),
    method: 'post',
})

send_push.definition = {
    methods: ["post"],
    url: '/admin/notifications/send-push',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::send_push
* @see app/Http/Controllers/Admin/AdminNotificationController.php:51
* @route '/admin/notifications/send-push'
*/
send_push.url = (options?: RouteQueryOptions) => {
    return send_push.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::send_push
* @see app/Http/Controllers/Admin/AdminNotificationController.php:51
* @route '/admin/notifications/send-push'
*/
send_push.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send_push.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::send_push
* @see app/Http/Controllers/Admin/AdminNotificationController.php:51
* @route '/admin/notifications/send-push'
*/
const send_pushForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send_push.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::send_push
* @see app/Http/Controllers/Admin/AdminNotificationController.php:51
* @route '/admin/notifications/send-push'
*/
send_pushForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send_push.url(options),
    method: 'post',
})

send_push.form = send_pushForm

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::send_email
* @see app/Http/Controllers/Admin/AdminNotificationController.php:138
* @route '/admin/notifications/send-email'
*/
export const send_email = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send_email.url(options),
    method: 'post',
})

send_email.definition = {
    methods: ["post"],
    url: '/admin/notifications/send-email',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::send_email
* @see app/Http/Controllers/Admin/AdminNotificationController.php:138
* @route '/admin/notifications/send-email'
*/
send_email.url = (options?: RouteQueryOptions) => {
    return send_email.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::send_email
* @see app/Http/Controllers/Admin/AdminNotificationController.php:138
* @route '/admin/notifications/send-email'
*/
send_email.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send_email.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::send_email
* @see app/Http/Controllers/Admin/AdminNotificationController.php:138
* @route '/admin/notifications/send-email'
*/
const send_emailForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send_email.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::send_email
* @see app/Http/Controllers/Admin/AdminNotificationController.php:138
* @route '/admin/notifications/send-email'
*/
send_emailForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send_email.url(options),
    method: 'post',
})

send_email.form = send_emailForm

const notifications = {
    index: Object.assign(index, index),
    send_push: Object.assign(send_push, send_push),
    send_email: Object.assign(send_email, send_email),
}

export default notifications