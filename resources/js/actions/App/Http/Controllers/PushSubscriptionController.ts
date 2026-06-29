import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PushSubscriptionController::store
* @see app/Http/Controllers/PushSubscriptionController.php:14
* @route '/push-subscriptions'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/push-subscriptions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PushSubscriptionController::store
* @see app/Http/Controllers/PushSubscriptionController.php:14
* @route '/push-subscriptions'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PushSubscriptionController::store
* @see app/Http/Controllers/PushSubscriptionController.php:14
* @route '/push-subscriptions'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PushSubscriptionController::store
* @see app/Http/Controllers/PushSubscriptionController.php:14
* @route '/push-subscriptions'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PushSubscriptionController::store
* @see app/Http/Controllers/PushSubscriptionController.php:14
* @route '/push-subscriptions'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\PushSubscriptionController::destroy
* @see app/Http/Controllers/PushSubscriptionController.php:41
* @route '/push-subscriptions'
*/
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/push-subscriptions',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\PushSubscriptionController::destroy
* @see app/Http/Controllers/PushSubscriptionController.php:41
* @route '/push-subscriptions'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PushSubscriptionController::destroy
* @see app/Http/Controllers/PushSubscriptionController.php:41
* @route '/push-subscriptions'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\PushSubscriptionController::destroy
* @see app/Http/Controllers/PushSubscriptionController.php:41
* @route '/push-subscriptions'
*/
const destroyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PushSubscriptionController::destroy
* @see app/Http/Controllers/PushSubscriptionController.php:41
* @route '/push-subscriptions'
*/
destroyForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const PushSubscriptionController = { store, destroy }

export default PushSubscriptionController