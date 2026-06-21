import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import users from './users'
import prices from './prices'
import subscriptions from './subscriptions'
import preorders from './preorders'
import settings from './settings'
import reports from './reports'
import payment from './payment'
/**
* @see \App\Http\Controllers\Admin\AdminController::dashboard
* @see app/Http/Controllers/Admin/AdminController.php:16
* @route '/admin/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/admin/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminController::dashboard
* @see app/Http/Controllers/Admin/AdminController.php:16
* @route '/admin/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminController::dashboard
* @see app/Http/Controllers/Admin/AdminController.php:16
* @route '/admin/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminController::dashboard
* @see app/Http/Controllers/Admin/AdminController.php:16
* @route '/admin/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminController::dashboard
* @see app/Http/Controllers/Admin/AdminController.php:16
* @route '/admin/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminController::dashboard
* @see app/Http/Controllers/Admin/AdminController.php:16
* @route '/admin/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminController::dashboard
* @see app/Http/Controllers/Admin/AdminController.php:16
* @route '/admin/dashboard'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

const admin = {
    dashboard: Object.assign(dashboard, dashboard),
    users: Object.assign(users, users),
    prices: Object.assign(prices, prices),
    subscriptions: Object.assign(subscriptions, subscriptions),
    preorders: Object.assign(preorders, preorders),
    settings: Object.assign(settings, settings),
    reports: Object.assign(reports, reports),
    payment: Object.assign(payment, payment),
}

export default admin