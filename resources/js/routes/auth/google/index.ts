import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
import complete460dbc from './complete'
/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::callback
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:31
 * @route '/auth/google/callback'
 */
export const callback = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: callback.url(options),
    method: 'get',
})

callback.definition = {
    methods: ["get","head"],
    url: '/auth/google/callback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::callback
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:31
 * @route '/auth/google/callback'
 */
callback.url = (options?: RouteQueryOptions) => {
    return callback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::callback
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:31
 * @route '/auth/google/callback'
 */
callback.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: callback.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::callback
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:31
 * @route '/auth/google/callback'
 */
callback.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: callback.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Auth\GoogleLoginController::callback
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:31
 * @route '/auth/google/callback'
 */
    const callbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: callback.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Auth\GoogleLoginController::callback
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:31
 * @route '/auth/google/callback'
 */
        callbackForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: callback.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Auth\GoogleLoginController::callback
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:31
 * @route '/auth/google/callback'
 */
        callbackForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: callback.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    callback.form = callbackForm
/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::complete
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:73
 * @route '/auth/google/complete'
 */
export const complete = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complete.url(options),
    method: 'get',
})

complete.definition = {
    methods: ["get","head"],
    url: '/auth/google/complete',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::complete
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:73
 * @route '/auth/google/complete'
 */
complete.url = (options?: RouteQueryOptions) => {
    return complete.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::complete
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:73
 * @route '/auth/google/complete'
 */
complete.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: complete.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::complete
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:73
 * @route '/auth/google/complete'
 */
complete.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: complete.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Auth\GoogleLoginController::complete
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:73
 * @route '/auth/google/complete'
 */
    const completeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: complete.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Auth\GoogleLoginController::complete
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:73
 * @route '/auth/google/complete'
 */
        completeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: complete.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Auth\GoogleLoginController::complete
 * @see app/Http/Controllers/Auth/GoogleLoginController.php:73
 * @route '/auth/google/complete'
 */
        completeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: complete.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    complete.form = completeForm
const google = {
    callback: Object.assign(callback, callback),
complete: Object.assign(complete, complete460dbc),
}

export default google