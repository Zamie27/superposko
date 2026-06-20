import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminUserController::index
* @see app/Http/Controllers/Admin/AdminUserController.php:20
* @route '/admin/users'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/users',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminUserController::index
* @see app/Http/Controllers/Admin/AdminUserController.php:20
* @route '/admin/users'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminUserController::index
* @see app/Http/Controllers/Admin/AdminUserController.php:20
* @route '/admin/users'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::index
* @see app/Http/Controllers/Admin/AdminUserController.php:20
* @route '/admin/users'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::index
* @see app/Http/Controllers/Admin/AdminUserController.php:20
* @route '/admin/users'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::index
* @see app/Http/Controllers/Admin/AdminUserController.php:20
* @route '/admin/users'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::index
* @see app/Http/Controllers/Admin/AdminUserController.php:20
* @route '/admin/users'
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
* @see \App\Http\Controllers\Admin\AdminUserController::reset_password
* @see app/Http/Controllers/Admin/AdminUserController.php:46
* @route '/admin/users/reset-password'
*/
export const reset_password = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reset_password.url(options),
    method: 'post',
})

reset_password.definition = {
    methods: ["post"],
    url: '/admin/users/reset-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminUserController::reset_password
* @see app/Http/Controllers/Admin/AdminUserController.php:46
* @route '/admin/users/reset-password'
*/
reset_password.url = (options?: RouteQueryOptions) => {
    return reset_password.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminUserController::reset_password
* @see app/Http/Controllers/Admin/AdminUserController.php:46
* @route '/admin/users/reset-password'
*/
reset_password.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reset_password.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::reset_password
* @see app/Http/Controllers/Admin/AdminUserController.php:46
* @route '/admin/users/reset-password'
*/
const reset_passwordForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reset_password.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::reset_password
* @see app/Http/Controllers/Admin/AdminUserController.php:46
* @route '/admin/users/reset-password'
*/
reset_passwordForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reset_password.url(options),
    method: 'post',
})

reset_password.form = reset_passwordForm

/**
* @see \App\Http\Controllers\Admin\AdminUserController::send_reset_email
* @see app/Http/Controllers/Admin/AdminUserController.php:67
* @route '/admin/users/send-reset-email'
*/
export const send_reset_email = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send_reset_email.url(options),
    method: 'post',
})

send_reset_email.definition = {
    methods: ["post"],
    url: '/admin/users/send-reset-email',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminUserController::send_reset_email
* @see app/Http/Controllers/Admin/AdminUserController.php:67
* @route '/admin/users/send-reset-email'
*/
send_reset_email.url = (options?: RouteQueryOptions) => {
    return send_reset_email.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminUserController::send_reset_email
* @see app/Http/Controllers/Admin/AdminUserController.php:67
* @route '/admin/users/send-reset-email'
*/
send_reset_email.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send_reset_email.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::send_reset_email
* @see app/Http/Controllers/Admin/AdminUserController.php:67
* @route '/admin/users/send-reset-email'
*/
const send_reset_emailForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send_reset_email.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::send_reset_email
* @see app/Http/Controllers/Admin/AdminUserController.php:67
* @route '/admin/users/send-reset-email'
*/
send_reset_emailForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send_reset_email.url(options),
    method: 'post',
})

send_reset_email.form = send_reset_emailForm

/**
* @see \App\Http\Controllers\Admin\AdminUserController::update_role
* @see app/Http/Controllers/Admin/AdminUserController.php:94
* @route '/admin/users/{user}/role'
*/
export const update_role = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update_role.url(args, options),
    method: 'put',
})

update_role.definition = {
    methods: ["put"],
    url: '/admin/users/{user}/role',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminUserController::update_role
* @see app/Http/Controllers/Admin/AdminUserController.php:94
* @route '/admin/users/{user}/role'
*/
update_role.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { user: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: typeof args.user === 'object'
        ? args.user.id
        : args.user,
    }

    return update_role.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminUserController::update_role
* @see app/Http/Controllers/Admin/AdminUserController.php:94
* @route '/admin/users/{user}/role'
*/
update_role.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update_role.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::update_role
* @see app/Http/Controllers/Admin/AdminUserController.php:94
* @route '/admin/users/{user}/role'
*/
const update_roleForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update_role.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::update_role
* @see app/Http/Controllers/Admin/AdminUserController.php:94
* @route '/admin/users/{user}/role'
*/
update_roleForm.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update_role.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update_role.form = update_roleForm

const users = {
    index: Object.assign(index, index),
    reset_password: Object.assign(reset_password, reset_password),
    send_reset_email: Object.assign(send_reset_email, send_reset_email),
    update_role: Object.assign(update_role, update_role),
}

export default users