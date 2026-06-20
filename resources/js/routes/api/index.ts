import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\ApiController::edit
* @see app/Http/Controllers/Settings/ApiController.php:13
* @route '/settings/api'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/api',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\ApiController::edit
* @see app/Http/Controllers/Settings/ApiController.php:13
* @route '/settings/api'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\ApiController::edit
* @see app/Http/Controllers/Settings/ApiController.php:13
* @route '/settings/api'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\ApiController::edit
* @see app/Http/Controllers/Settings/ApiController.php:13
* @route '/settings/api'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Settings\ApiController::edit
* @see app/Http/Controllers/Settings/ApiController.php:13
* @route '/settings/api'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\ApiController::edit
* @see app/Http/Controllers/Settings/ApiController.php:13
* @route '/settings/api'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\ApiController::edit
* @see app/Http/Controllers/Settings/ApiController.php:13
* @route '/settings/api'
*/
editForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\Settings\ApiController::update
* @see app/Http/Controllers/Settings/ApiController.php:21
* @route '/settings/api'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/settings/api',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Settings\ApiController::update
* @see app/Http/Controllers/Settings/ApiController.php:21
* @route '/settings/api'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\ApiController::update
* @see app/Http/Controllers/Settings/ApiController.php:21
* @route '/settings/api'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Settings\ApiController::update
* @see app/Http/Controllers/Settings/ApiController.php:21
* @route '/settings/api'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\ApiController::update
* @see app/Http/Controllers/Settings/ApiController.php:21
* @route '/settings/api'
*/
updateForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const api = {
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
}

export default api