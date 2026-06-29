import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminReportController::index
* @see app/Http/Controllers/Admin/AdminReportController.php:16
* @route '/admin/reports'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminReportController::index
* @see app/Http/Controllers/Admin/AdminReportController.php:16
* @route '/admin/reports'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminReportController::index
* @see app/Http/Controllers/Admin/AdminReportController.php:16
* @route '/admin/reports'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminReportController::index
* @see app/Http/Controllers/Admin/AdminReportController.php:16
* @route '/admin/reports'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminReportController::index
* @see app/Http/Controllers/Admin/AdminReportController.php:16
* @route '/admin/reports'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminReportController::index
* @see app/Http/Controllers/Admin/AdminReportController.php:16
* @route '/admin/reports'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminReportController::index
* @see app/Http/Controllers/Admin/AdminReportController.php:16
* @route '/admin/reports'
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
* @see \App\Http\Controllers\Admin\AdminReportController::resolve
* @see app/Http/Controllers/Admin/AdminReportController.php:47
* @route '/admin/reports/{report}/resolve'
*/
export const resolve = (args: { report: number | { id: number } } | [report: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: resolve.url(args, options),
    method: 'put',
})

resolve.definition = {
    methods: ["put"],
    url: '/admin/reports/{report}/resolve',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminReportController::resolve
* @see app/Http/Controllers/Admin/AdminReportController.php:47
* @route '/admin/reports/{report}/resolve'
*/
resolve.url = (args: { report: number | { id: number } } | [report: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { report: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { report: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            report: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        report: typeof args.report === 'object'
        ? args.report.id
        : args.report,
    }

    return resolve.definition.url
            .replace('{report}', parsedArgs.report.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminReportController::resolve
* @see app/Http/Controllers/Admin/AdminReportController.php:47
* @route '/admin/reports/{report}/resolve'
*/
resolve.put = (args: { report: number | { id: number } } | [report: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: resolve.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminReportController::resolve
* @see app/Http/Controllers/Admin/AdminReportController.php:47
* @route '/admin/reports/{report}/resolve'
*/
const resolveForm = (args: { report: number | { id: number } } | [report: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolve.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminReportController::resolve
* @see app/Http/Controllers/Admin/AdminReportController.php:47
* @route '/admin/reports/{report}/resolve'
*/
resolveForm.put = (args: { report: number | { id: number } } | [report: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolve.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

resolve.form = resolveForm

const AdminReportController = { index, resolve }

export default AdminReportController