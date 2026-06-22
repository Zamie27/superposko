import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::index
* @see app/Http/Controllers/Admin/AdminBugReportController.php:17
* @route '/admin/bug-reports'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/bug-reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::index
* @see app/Http/Controllers/Admin/AdminBugReportController.php:17
* @route '/admin/bug-reports'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::index
* @see app/Http/Controllers/Admin/AdminBugReportController.php:17
* @route '/admin/bug-reports'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::index
* @see app/Http/Controllers/Admin/AdminBugReportController.php:17
* @route '/admin/bug-reports'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::index
* @see app/Http/Controllers/Admin/AdminBugReportController.php:17
* @route '/admin/bug-reports'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::index
* @see app/Http/Controllers/Admin/AdminBugReportController.php:17
* @route '/admin/bug-reports'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::index
* @see app/Http/Controllers/Admin/AdminBugReportController.php:17
* @route '/admin/bug-reports'
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
* @see \App\Http\Controllers\Admin\AdminBugReportController::resolve
* @see app/Http/Controllers/Admin/AdminBugReportController.php:48
* @route '/admin/bug-reports/{bugReport}/resolve'
*/
export const resolve = (args: { bugReport: number | { id: number } } | [bugReport: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: resolve.url(args, options),
    method: 'put',
})

resolve.definition = {
    methods: ["put"],
    url: '/admin/bug-reports/{bugReport}/resolve',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::resolve
* @see app/Http/Controllers/Admin/AdminBugReportController.php:48
* @route '/admin/bug-reports/{bugReport}/resolve'
*/
resolve.url = (args: { bugReport: number | { id: number } } | [bugReport: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bugReport: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { bugReport: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            bugReport: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        bugReport: typeof args.bugReport === 'object'
        ? args.bugReport.id
        : args.bugReport,
    }

    return resolve.definition.url
            .replace('{bugReport}', parsedArgs.bugReport.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::resolve
* @see app/Http/Controllers/Admin/AdminBugReportController.php:48
* @route '/admin/bug-reports/{bugReport}/resolve'
*/
resolve.put = (args: { bugReport: number | { id: number } } | [bugReport: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: resolve.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::resolve
* @see app/Http/Controllers/Admin/AdminBugReportController.php:48
* @route '/admin/bug-reports/{bugReport}/resolve'
*/
const resolveForm = (args: { bugReport: number | { id: number } } | [bugReport: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolve.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminBugReportController::resolve
* @see app/Http/Controllers/Admin/AdminBugReportController.php:48
* @route '/admin/bug-reports/{bugReport}/resolve'
*/
resolveForm.put = (args: { bugReport: number | { id: number } } | [bugReport: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolve.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

resolve.form = resolveForm

const bugReports = {
    index: Object.assign(index, index),
    resolve: Object.assign(resolve, resolve),
}

export default bugReports