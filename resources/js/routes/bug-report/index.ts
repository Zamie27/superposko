import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\BugReportController::store
* @see app/Http/Controllers/BugReportController.php:15
* @route '/bug-report'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/bug-report',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BugReportController::store
* @see app/Http/Controllers/BugReportController.php:15
* @route '/bug-report'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BugReportController::store
* @see app/Http/Controllers/BugReportController.php:15
* @route '/bug-report'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BugReportController::store
* @see app/Http/Controllers/BugReportController.php:15
* @route '/bug-report'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BugReportController::store
* @see app/Http/Controllers/BugReportController.php:15
* @route '/bug-report'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const bugReport = {
    store: Object.assign(store, store),
}

export default bugReport