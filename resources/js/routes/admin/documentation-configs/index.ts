import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::index
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:19
* @route '/admin/documentation-configs'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/documentation-configs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::index
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:19
* @route '/admin/documentation-configs'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::index
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:19
* @route '/admin/documentation-configs'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::index
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:19
* @route '/admin/documentation-configs'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::index
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:19
* @route '/admin/documentation-configs'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::index
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:19
* @route '/admin/documentation-configs'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::index
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:19
* @route '/admin/documentation-configs'
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
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::update
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:43
* @route '/admin/documentation-configs/{host}'
*/
export const update = (args: { host: number | { id: number } } | [host: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/documentation-configs/{host}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::update
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:43
* @route '/admin/documentation-configs/{host}'
*/
update.url = (args: { host: number | { id: number } } | [host: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { host: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { host: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            host: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        host: typeof args.host === 'object'
        ? args.host.id
        : args.host,
    }

    return update.definition.url
            .replace('{host}', parsedArgs.host.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::update
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:43
* @route '/admin/documentation-configs/{host}'
*/
update.put = (args: { host: number | { id: number } } | [host: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::update
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:43
* @route '/admin/documentation-configs/{host}'
*/
const updateForm = (args: { host: number | { id: number } } | [host: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminDocumentationConfigController::update
* @see app/Http/Controllers/Admin/AdminDocumentationConfigController.php:43
* @route '/admin/documentation-configs/{host}'
*/
updateForm.put = (args: { host: number | { id: number } } | [host: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const documentationConfigs = {
    index: Object.assign(index, index),
    update: Object.assign(update, update),
}

export default documentationConfigs