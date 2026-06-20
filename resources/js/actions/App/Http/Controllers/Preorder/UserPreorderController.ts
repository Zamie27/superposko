import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::index
* @see app/Http/Controllers/Preorder/UserPreorderController.php:19
* @route '/user/preorder'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/user/preorder',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::index
* @see app/Http/Controllers/Preorder/UserPreorderController.php:19
* @route '/user/preorder'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::index
* @see app/Http/Controllers/Preorder/UserPreorderController.php:19
* @route '/user/preorder'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::index
* @see app/Http/Controllers/Preorder/UserPreorderController.php:19
* @route '/user/preorder'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::index
* @see app/Http/Controllers/Preorder/UserPreorderController.php:19
* @route '/user/preorder'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::index
* @see app/Http/Controllers/Preorder/UserPreorderController.php:19
* @route '/user/preorder'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::index
* @see app/Http/Controllers/Preorder/UserPreorderController.php:19
* @route '/user/preorder'
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
* @see \App\Http\Controllers\Preorder\UserPreorderController::store
* @see app/Http/Controllers/Preorder/UserPreorderController.php:43
* @route '/user/preorder'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/user/preorder',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::store
* @see app/Http/Controllers/Preorder/UserPreorderController.php:43
* @route '/user/preorder'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::store
* @see app/Http/Controllers/Preorder/UserPreorderController.php:43
* @route '/user/preorder'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::store
* @see app/Http/Controllers/Preorder/UserPreorderController.php:43
* @route '/user/preorder'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Preorder\UserPreorderController::store
* @see app/Http/Controllers/Preorder/UserPreorderController.php:43
* @route '/user/preorder'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const UserPreorderController = { index, store }

export default UserPreorderController