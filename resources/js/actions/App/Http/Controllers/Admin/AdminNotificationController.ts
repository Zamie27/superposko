import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Admin\AdminNotificationController::sendPush
* @see app/Http/Controllers/Admin/AdminNotificationController.php:51
* @route '/admin/notifications/send-push'
*/
export const sendPush = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendPush.url(options),
    method: 'post',
})

sendPush.definition = {
    methods: ["post"],
    url: '/admin/notifications/send-push',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::sendPush
* @see app/Http/Controllers/Admin/AdminNotificationController.php:51
* @route '/admin/notifications/send-push'
*/
sendPush.url = (options?: RouteQueryOptions) => {
    return sendPush.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::sendPush
* @see app/Http/Controllers/Admin/AdminNotificationController.php:51
* @route '/admin/notifications/send-push'
*/
sendPush.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendPush.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::sendPush
* @see app/Http/Controllers/Admin/AdminNotificationController.php:51
* @route '/admin/notifications/send-push'
*/
const sendPushForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendPush.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::sendPush
* @see app/Http/Controllers/Admin/AdminNotificationController.php:51
* @route '/admin/notifications/send-push'
*/
sendPushForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendPush.url(options),
    method: 'post',
})

sendPush.form = sendPushForm

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::sendEmail
* @see app/Http/Controllers/Admin/AdminNotificationController.php:138
* @route '/admin/notifications/send-email'
*/
export const sendEmail = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendEmail.url(options),
    method: 'post',
})

sendEmail.definition = {
    methods: ["post"],
    url: '/admin/notifications/send-email',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::sendEmail
* @see app/Http/Controllers/Admin/AdminNotificationController.php:138
* @route '/admin/notifications/send-email'
*/
sendEmail.url = (options?: RouteQueryOptions) => {
    return sendEmail.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::sendEmail
* @see app/Http/Controllers/Admin/AdminNotificationController.php:138
* @route '/admin/notifications/send-email'
*/
sendEmail.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendEmail.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::sendEmail
* @see app/Http/Controllers/Admin/AdminNotificationController.php:138
* @route '/admin/notifications/send-email'
*/
const sendEmailForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendEmail.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminNotificationController::sendEmail
* @see app/Http/Controllers/Admin/AdminNotificationController.php:138
* @route '/admin/notifications/send-email'
*/
sendEmailForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendEmail.url(options),
    method: 'post',
})

sendEmail.form = sendEmailForm

const AdminNotificationController = { index, sendPush, sendEmail }

export default AdminNotificationController