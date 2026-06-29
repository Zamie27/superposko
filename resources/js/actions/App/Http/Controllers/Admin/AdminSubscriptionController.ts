import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:18
* @route '/admin/subscriptions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/subscriptions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:18
* @route '/admin/subscriptions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:18
* @route '/admin/subscriptions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:18
* @route '/admin/subscriptions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:18
* @route '/admin/subscriptions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:18
* @route '/admin/subscriptions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:18
* @route '/admin/subscriptions'
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
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::bypass
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:45
* @route '/admin/subscriptions/{user}/bypass'
*/
export const bypass = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: bypass.url(args, options),
    method: 'put',
})

bypass.definition = {
    methods: ["put"],
    url: '/admin/subscriptions/{user}/bypass',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::bypass
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:45
* @route '/admin/subscriptions/{user}/bypass'
*/
bypass.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return bypass.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::bypass
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:45
* @route '/admin/subscriptions/{user}/bypass'
*/
bypass.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: bypass.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::bypass
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:45
* @route '/admin/subscriptions/{user}/bypass'
*/
const bypassForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: bypass.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::bypass
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:45
* @route '/admin/subscriptions/{user}/bypass'
*/
bypassForm.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: bypass.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

bypass.form = bypassForm

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::updateDuration
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:67
* @route '/admin/subscriptions/{user}/duration'
*/
export const updateDuration = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateDuration.url(args, options),
    method: 'put',
})

updateDuration.definition = {
    methods: ["put"],
    url: '/admin/subscriptions/{user}/duration',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::updateDuration
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:67
* @route '/admin/subscriptions/{user}/duration'
*/
updateDuration.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateDuration.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::updateDuration
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:67
* @route '/admin/subscriptions/{user}/duration'
*/
updateDuration.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateDuration.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::updateDuration
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:67
* @route '/admin/subscriptions/{user}/duration'
*/
const updateDurationForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateDuration.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::updateDuration
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:67
* @route '/admin/subscriptions/{user}/duration'
*/
updateDurationForm.put = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateDuration.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateDuration.form = updateDurationForm

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::revoke
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:93
* @route '/admin/subscriptions/{user}/revoke'
*/
export const revoke = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: revoke.url(args, options),
    method: 'delete',
})

revoke.definition = {
    methods: ["delete"],
    url: '/admin/subscriptions/{user}/revoke',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::revoke
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:93
* @route '/admin/subscriptions/{user}/revoke'
*/
revoke.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return revoke.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::revoke
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:93
* @route '/admin/subscriptions/{user}/revoke'
*/
revoke.delete = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: revoke.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::revoke
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:93
* @route '/admin/subscriptions/{user}/revoke'
*/
const revokeForm = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: revoke.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionController::revoke
* @see app/Http/Controllers/Admin/AdminSubscriptionController.php:93
* @route '/admin/subscriptions/{user}/revoke'
*/
revokeForm.delete = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: revoke.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

revoke.form = revokeForm

const AdminSubscriptionController = { index, bypass, updateDuration, revoke }

export default AdminSubscriptionController