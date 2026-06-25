import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminPriceController::index
* @see app/Http/Controllers/Admin/AdminPriceController.php:17
* @route '/admin/prices'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/prices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::index
* @see app/Http/Controllers/Admin/AdminPriceController.php:17
* @route '/admin/prices'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::index
* @see app/Http/Controllers/Admin/AdminPriceController.php:17
* @route '/admin/prices'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::index
* @see app/Http/Controllers/Admin/AdminPriceController.php:17
* @route '/admin/prices'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::index
* @see app/Http/Controllers/Admin/AdminPriceController.php:17
* @route '/admin/prices'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::index
* @see app/Http/Controllers/Admin/AdminPriceController.php:17
* @route '/admin/prices'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::index
* @see app/Http/Controllers/Admin/AdminPriceController.php:17
* @route '/admin/prices'
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
* @see \App\Http\Controllers\Admin\AdminPriceController::update
* @see app/Http/Controllers/Admin/AdminPriceController.php:38
* @route '/admin/prices'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/prices',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::update
* @see app/Http/Controllers/Admin/AdminPriceController.php:38
* @route '/admin/prices'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::update
* @see app/Http/Controllers/Admin/AdminPriceController.php:38
* @route '/admin/prices'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::update
* @see app/Http/Controllers/Admin/AdminPriceController.php:38
* @route '/admin/prices'
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
* @see \App\Http\Controllers\Admin\AdminPriceController::update
* @see app/Http/Controllers/Admin/AdminPriceController.php:38
* @route '/admin/prices'
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

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::updateQris
* @see app/Http/Controllers/Admin/AdminPriceController.php:75
* @route '/admin/prices/qris'
*/
export const updateQris = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateQris.url(options),
    method: 'post',
})

updateQris.definition = {
    methods: ["post"],
    url: '/admin/prices/qris',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::updateQris
* @see app/Http/Controllers/Admin/AdminPriceController.php:75
* @route '/admin/prices/qris'
*/
updateQris.url = (options?: RouteQueryOptions) => {
    return updateQris.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::updateQris
* @see app/Http/Controllers/Admin/AdminPriceController.php:75
* @route '/admin/prices/qris'
*/
updateQris.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateQris.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::updateQris
* @see app/Http/Controllers/Admin/AdminPriceController.php:75
* @route '/admin/prices/qris'
*/
const updateQrisForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateQris.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::updateQris
* @see app/Http/Controllers/Admin/AdminPriceController.php:75
* @route '/admin/prices/qris'
*/
updateQrisForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateQris.url(options),
    method: 'post',
})

updateQris.form = updateQrisForm

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::deleteQris
* @see app/Http/Controllers/Admin/AdminPriceController.php:108
* @route '/admin/prices/qris'
*/
export const deleteQris = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteQris.url(options),
    method: 'delete',
})

deleteQris.definition = {
    methods: ["delete"],
    url: '/admin/prices/qris',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::deleteQris
* @see app/Http/Controllers/Admin/AdminPriceController.php:108
* @route '/admin/prices/qris'
*/
deleteQris.url = (options?: RouteQueryOptions) => {
    return deleteQris.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::deleteQris
* @see app/Http/Controllers/Admin/AdminPriceController.php:108
* @route '/admin/prices/qris'
*/
deleteQris.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteQris.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::deleteQris
* @see app/Http/Controllers/Admin/AdminPriceController.php:108
* @route '/admin/prices/qris'
*/
const deleteQrisForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteQris.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPriceController::deleteQris
* @see app/Http/Controllers/Admin/AdminPriceController.php:108
* @route '/admin/prices/qris'
*/
deleteQrisForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteQris.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

deleteQris.form = deleteQrisForm

const AdminPriceController = { index, update, updateQris, deleteQris }

export default AdminPriceController