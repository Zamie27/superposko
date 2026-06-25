import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminPriceController::update
* @see app/Http/Controllers/Admin/AdminPriceController.php:75
* @route '/admin/prices/qris'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/admin/prices/qris',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::update
* @see app/Http/Controllers/Admin/AdminPriceController.php:75
* @route '/admin/prices/qris'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::update
* @see app/Http/Controllers/Admin/AdminPriceController.php:75
* @route '/admin/prices/qris'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::update
* @see app/Http/Controllers/Admin/AdminPriceController.php:75
* @route '/admin/prices/qris'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::update
* @see app/Http/Controllers/Admin/AdminPriceController.php:75
* @route '/admin/prices/qris'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::deleteMethod
* @see app/Http/Controllers/Admin/AdminPriceController.php:108
* @route '/admin/prices/qris'
*/
export const deleteMethod = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/admin/prices/qris',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::deleteMethod
* @see app/Http/Controllers/Admin/AdminPriceController.php:108
* @route '/admin/prices/qris'
*/
deleteMethod.url = (options?: RouteQueryOptions) => {
    return deleteMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::deleteMethod
* @see app/Http/Controllers/Admin/AdminPriceController.php:108
* @route '/admin/prices/qris'
*/
deleteMethod.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::deleteMethod
* @see app/Http/Controllers/Admin/AdminPriceController.php:108
* @route '/admin/prices/qris'
*/
const deleteMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::deleteMethod
* @see app/Http/Controllers/Admin/AdminPriceController.php:108
* @route '/admin/prices/qris'
*/
deleteMethodForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

deleteMethod.form = deleteMethodForm

const qris = {
    update: Object.assign(update, update),
    delete: Object.assign(deleteMethod, deleteMethod),
}

export default qris