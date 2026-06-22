import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::index
* @see app/Http/Controllers/Admin/AdminPreorderController.php:18
* @route '/admin/preorders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/preorders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::index
* @see app/Http/Controllers/Admin/AdminPreorderController.php:18
* @route '/admin/preorders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::index
* @see app/Http/Controllers/Admin/AdminPreorderController.php:18
* @route '/admin/preorders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::index
* @see app/Http/Controllers/Admin/AdminPreorderController.php:18
* @route '/admin/preorders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::index
* @see app/Http/Controllers/Admin/AdminPreorderController.php:18
* @route '/admin/preorders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::index
* @see app/Http/Controllers/Admin/AdminPreorderController.php:18
* @route '/admin/preorders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::index
* @see app/Http/Controllers/Admin/AdminPreorderController.php:18
* @route '/admin/preorders'
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
* @see \App\Http\Controllers\Admin\AdminPreorderController::approve
* @see app/Http/Controllers/Admin/AdminPreorderController.php:44
* @route '/admin/preorders/{preorder}/approve'
*/
export const approve = (args: { preorder: number | { id: number } } | [preorder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/admin/preorders/{preorder}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::approve
* @see app/Http/Controllers/Admin/AdminPreorderController.php:44
* @route '/admin/preorders/{preorder}/approve'
*/
approve.url = (args: { preorder: number | { id: number } } | [preorder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { preorder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { preorder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            preorder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        preorder: typeof args.preorder === 'object'
        ? args.preorder.id
        : args.preorder,
    }

    return approve.definition.url
            .replace('{preorder}', parsedArgs.preorder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::approve
* @see app/Http/Controllers/Admin/AdminPreorderController.php:44
* @route '/admin/preorders/{preorder}/approve'
*/
approve.post = (args: { preorder: number | { id: number } } | [preorder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::approve
* @see app/Http/Controllers/Admin/AdminPreorderController.php:44
* @route '/admin/preorders/{preorder}/approve'
*/
const approveForm = (args: { preorder: number | { id: number } } | [preorder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::approve
* @see app/Http/Controllers/Admin/AdminPreorderController.php:44
* @route '/admin/preorders/{preorder}/approve'
*/
approveForm.post = (args: { preorder: number | { id: number } } | [preorder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve.url(args, options),
    method: 'post',
})

approve.form = approveForm

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::reject
* @see app/Http/Controllers/Admin/AdminPreorderController.php:71
* @route '/admin/preorders/{preorder}/reject'
*/
export const reject = (args: { preorder: number | { id: number } } | [preorder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/admin/preorders/{preorder}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::reject
* @see app/Http/Controllers/Admin/AdminPreorderController.php:71
* @route '/admin/preorders/{preorder}/reject'
*/
reject.url = (args: { preorder: number | { id: number } } | [preorder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { preorder: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { preorder: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            preorder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        preorder: typeof args.preorder === 'object'
        ? args.preorder.id
        : args.preorder,
    }

    return reject.definition.url
            .replace('{preorder}', parsedArgs.preorder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::reject
* @see app/Http/Controllers/Admin/AdminPreorderController.php:71
* @route '/admin/preorders/{preorder}/reject'
*/
reject.post = (args: { preorder: number | { id: number } } | [preorder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::reject
* @see app/Http/Controllers/Admin/AdminPreorderController.php:71
* @route '/admin/preorders/{preorder}/reject'
*/
const rejectForm = (args: { preorder: number | { id: number } } | [preorder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPreorderController::reject
* @see app/Http/Controllers/Admin/AdminPreorderController.php:71
* @route '/admin/preorders/{preorder}/reject'
*/
rejectForm.post = (args: { preorder: number | { id: number } } | [preorder: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

reject.form = rejectForm

const AdminPreorderController = { index, approve, reject }

export default AdminPreorderController