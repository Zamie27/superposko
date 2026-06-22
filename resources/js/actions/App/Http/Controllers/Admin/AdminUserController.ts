import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Admin\AdminUserController::resetPassword
* @see app/Http/Controllers/Admin/AdminUserController.php:46
* @route '/admin/users/reset-password'
*/
export const resetPassword = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resetPassword.url(options),
    method: 'post',
})

resetPassword.definition = {
    methods: ["post"],
    url: '/admin/users/reset-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminUserController::resetPassword
* @see app/Http/Controllers/Admin/AdminUserController.php:46
* @route '/admin/users/reset-password'
*/
resetPassword.url = (options?: RouteQueryOptions) => {
    return resetPassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminUserController::resetPassword
* @see app/Http/Controllers/Admin/AdminUserController.php:46
* @route '/admin/users/reset-password'
*/
resetPassword.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resetPassword.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::resetPassword
* @see app/Http/Controllers/Admin/AdminUserController.php:46
* @route '/admin/users/reset-password'
*/
const resetPasswordForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resetPassword.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::resetPassword
* @see app/Http/Controllers/Admin/AdminUserController.php:46
* @route '/admin/users/reset-password'
*/
resetPasswordForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resetPassword.url(options),
    method: 'post',
})

resetPassword.form = resetPasswordForm

/**
* @see \App\Http\Controllers\Admin\AdminUserController::sendResetEmail
* @see app/Http/Controllers/Admin/AdminUserController.php:67
* @route '/admin/users/send-reset-email'
*/
export const sendResetEmail = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendResetEmail.url(options),
    method: 'post',
})

sendResetEmail.definition = {
    methods: ["post"],
    url: '/admin/users/send-reset-email',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminUserController::sendResetEmail
* @see app/Http/Controllers/Admin/AdminUserController.php:67
* @route '/admin/users/send-reset-email'
*/
sendResetEmail.url = (options?: RouteQueryOptions) => {
    return sendResetEmail.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminUserController::sendResetEmail
* @see app/Http/Controllers/Admin/AdminUserController.php:67
* @route '/admin/users/send-reset-email'
*/
sendResetEmail.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendResetEmail.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::sendResetEmail
* @see app/Http/Controllers/Admin/AdminUserController.php:67
* @route '/admin/users/send-reset-email'
*/
const sendResetEmailForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendResetEmail.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::sendResetEmail
* @see app/Http/Controllers/Admin/AdminUserController.php:67
* @route '/admin/users/send-reset-email'
*/
sendResetEmailForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendResetEmail.url(options),
    method: 'post',
})

sendResetEmail.form = sendResetEmailForm

/**
* @see \App\Http\Controllers\Admin\AdminUserController::updateRole
* @see app/Http/Controllers/Admin/AdminUserController.php:94
* @route '/admin/users/{user}/role'
*/
export const updateRole = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateRole.url(args, options),
    method: 'put',
})

updateRole.definition = {
    methods: ["put"],
    url: '/admin/users/{user}/role',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminUserController::updateRole
* @see app/Http/Controllers/Admin/AdminUserController.php:94
* @route '/admin/users/{user}/role'
*/
updateRole.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateRole.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminUserController::updateRole
* @see app/Http/Controllers/Admin/AdminUserController.php:94
* @route '/admin/users/{user}/role'
*/
updateRole.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateRole.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::updateRole
* @see app/Http/Controllers/Admin/AdminUserController.php:94
* @route '/admin/users/{user}/role'
*/
const updateRoleForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateRole.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::updateRole
* @see app/Http/Controllers/Admin/AdminUserController.php:94
* @route '/admin/users/{user}/role'
*/
updateRoleForm.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateRole.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateRole.form = updateRoleForm

/**
* @see \App\Http\Controllers\Admin\AdminUserController::updateTrial
* @see app/Http/Controllers/Admin/AdminUserController.php:117
* @route '/admin/users/{user}/trial'
*/
export const updateTrial = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateTrial.url(args, options),
    method: 'put',
})

updateTrial.definition = {
    methods: ["put"],
    url: '/admin/users/{user}/trial',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminUserController::updateTrial
* @see app/Http/Controllers/Admin/AdminUserController.php:117
* @route '/admin/users/{user}/trial'
*/
updateTrial.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateTrial.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminUserController::updateTrial
* @see app/Http/Controllers/Admin/AdminUserController.php:117
* @route '/admin/users/{user}/trial'
*/
updateTrial.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateTrial.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::updateTrial
* @see app/Http/Controllers/Admin/AdminUserController.php:117
* @route '/admin/users/{user}/trial'
*/
const updateTrialForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateTrial.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminUserController::updateTrial
* @see app/Http/Controllers/Admin/AdminUserController.php:117
* @route '/admin/users/{user}/trial'
*/
updateTrialForm.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateTrial.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateTrial.form = updateTrialForm

const AdminUserController = { index, resetPassword, sendResetEmail, updateRole, updateTrial }

export default AdminUserController