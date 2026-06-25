import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:19
* @route '/admin/subscription-requests'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/subscription-requests',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:19
* @route '/admin/subscription-requests'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:19
* @route '/admin/subscription-requests'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:19
* @route '/admin/subscription-requests'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:19
* @route '/admin/subscription-requests'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:19
* @route '/admin/subscription-requests'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::index
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:19
* @route '/admin/subscription-requests'
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
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::approve
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:46
* @route '/admin/subscription-requests/{subscriptionRequest}/approve'
*/
export const approve = (args: { subscriptionRequest: number | { id: number } } | [subscriptionRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/admin/subscription-requests/{subscriptionRequest}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::approve
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:46
* @route '/admin/subscription-requests/{subscriptionRequest}/approve'
*/
approve.url = (args: { subscriptionRequest: number | { id: number } } | [subscriptionRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { subscriptionRequest: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { subscriptionRequest: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            subscriptionRequest: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        subscriptionRequest: typeof args.subscriptionRequest === 'object'
        ? args.subscriptionRequest.id
        : args.subscriptionRequest,
    }

    return approve.definition.url
            .replace('{subscriptionRequest}', parsedArgs.subscriptionRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::approve
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:46
* @route '/admin/subscription-requests/{subscriptionRequest}/approve'
*/
approve.post = (args: { subscriptionRequest: number | { id: number } } | [subscriptionRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::approve
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:46
* @route '/admin/subscription-requests/{subscriptionRequest}/approve'
*/
const approveForm = (args: { subscriptionRequest: number | { id: number } } | [subscriptionRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::approve
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:46
* @route '/admin/subscription-requests/{subscriptionRequest}/approve'
*/
approveForm.post = (args: { subscriptionRequest: number | { id: number } } | [subscriptionRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

approve.form = approveForm

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::reject
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:76
* @route '/admin/subscription-requests/{subscriptionRequest}/reject'
*/
export const reject = (args: { subscriptionRequest: number | { id: number } } | [subscriptionRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/admin/subscription-requests/{subscriptionRequest}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::reject
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:76
* @route '/admin/subscription-requests/{subscriptionRequest}/reject'
*/
reject.url = (args: { subscriptionRequest: number | { id: number } } | [subscriptionRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { subscriptionRequest: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { subscriptionRequest: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            subscriptionRequest: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        subscriptionRequest: typeof args.subscriptionRequest === 'object'
        ? args.subscriptionRequest.id
        : args.subscriptionRequest,
    }

    return reject.definition.url
            .replace('{subscriptionRequest}', parsedArgs.subscriptionRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::reject
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:76
* @route '/admin/subscription-requests/{subscriptionRequest}/reject'
*/
reject.post = (args: { subscriptionRequest: number | { id: number } } | [subscriptionRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::reject
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:76
* @route '/admin/subscription-requests/{subscriptionRequest}/reject'
*/
const rejectForm = (args: { subscriptionRequest: number | { id: number } } | [subscriptionRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminSubscriptionRequestController::reject
* @see app/Http/Controllers/Admin/AdminSubscriptionRequestController.php:76
* @route '/admin/subscription-requests/{subscriptionRequest}/reject'
*/
rejectForm.post = (args: { subscriptionRequest: number | { id: number } } | [subscriptionRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

reject.form = rejectForm

const AdminSubscriptionRequestController = { index, approve, reject }

export default AdminSubscriptionRequestController