import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::redirectToGoogle
* @see app/Http/Controllers/Auth/GoogleLoginController.php:23
* @route '/auth/google'
*/
export const redirectToGoogle = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirectToGoogle.url(options),
    method: 'get',
})

redirectToGoogle.definition = {
    methods: ["get","head"],
    url: '/auth/google',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::redirectToGoogle
* @see app/Http/Controllers/Auth/GoogleLoginController.php:23
* @route '/auth/google'
*/
redirectToGoogle.url = (options?: RouteQueryOptions) => {
    return redirectToGoogle.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::redirectToGoogle
* @see app/Http/Controllers/Auth/GoogleLoginController.php:23
* @route '/auth/google'
*/
redirectToGoogle.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirectToGoogle.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::redirectToGoogle
* @see app/Http/Controllers/Auth/GoogleLoginController.php:23
* @route '/auth/google'
*/
redirectToGoogle.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: redirectToGoogle.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::redirectToGoogle
* @see app/Http/Controllers/Auth/GoogleLoginController.php:23
* @route '/auth/google'
*/
const redirectToGoogleForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirectToGoogle.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::redirectToGoogle
* @see app/Http/Controllers/Auth/GoogleLoginController.php:23
* @route '/auth/google'
*/
redirectToGoogleForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirectToGoogle.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::redirectToGoogle
* @see app/Http/Controllers/Auth/GoogleLoginController.php:23
* @route '/auth/google'
*/
redirectToGoogleForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirectToGoogle.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

redirectToGoogle.form = redirectToGoogleForm

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::handleGoogleCallback
* @see app/Http/Controllers/Auth/GoogleLoginController.php:31
* @route '/auth/google/callback'
*/
export const handleGoogleCallback = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: handleGoogleCallback.url(options),
    method: 'get',
})

handleGoogleCallback.definition = {
    methods: ["get","head"],
    url: '/auth/google/callback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::handleGoogleCallback
* @see app/Http/Controllers/Auth/GoogleLoginController.php:31
* @route '/auth/google/callback'
*/
handleGoogleCallback.url = (options?: RouteQueryOptions) => {
    return handleGoogleCallback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::handleGoogleCallback
* @see app/Http/Controllers/Auth/GoogleLoginController.php:31
* @route '/auth/google/callback'
*/
handleGoogleCallback.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: handleGoogleCallback.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::handleGoogleCallback
* @see app/Http/Controllers/Auth/GoogleLoginController.php:31
* @route '/auth/google/callback'
*/
handleGoogleCallback.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: handleGoogleCallback.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::handleGoogleCallback
* @see app/Http/Controllers/Auth/GoogleLoginController.php:31
* @route '/auth/google/callback'
*/
const handleGoogleCallbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleGoogleCallback.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::handleGoogleCallback
* @see app/Http/Controllers/Auth/GoogleLoginController.php:31
* @route '/auth/google/callback'
*/
handleGoogleCallbackForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleGoogleCallback.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::handleGoogleCallback
* @see app/Http/Controllers/Auth/GoogleLoginController.php:31
* @route '/auth/google/callback'
*/
handleGoogleCallbackForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: handleGoogleCallback.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

handleGoogleCallback.form = handleGoogleCallbackForm

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::showCompleteProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:73
* @route '/auth/google/complete'
*/
export const showCompleteProfile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCompleteProfile.url(options),
    method: 'get',
})

showCompleteProfile.definition = {
    methods: ["get","head"],
    url: '/auth/google/complete',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::showCompleteProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:73
* @route '/auth/google/complete'
*/
showCompleteProfile.url = (options?: RouteQueryOptions) => {
    return showCompleteProfile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::showCompleteProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:73
* @route '/auth/google/complete'
*/
showCompleteProfile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCompleteProfile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::showCompleteProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:73
* @route '/auth/google/complete'
*/
showCompleteProfile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showCompleteProfile.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::showCompleteProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:73
* @route '/auth/google/complete'
*/
const showCompleteProfileForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCompleteProfile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::showCompleteProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:73
* @route '/auth/google/complete'
*/
showCompleteProfileForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCompleteProfile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::showCompleteProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:73
* @route '/auth/google/complete'
*/
showCompleteProfileForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showCompleteProfile.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showCompleteProfile.form = showCompleteProfileForm

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::completeProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:92
* @route '/auth/google/complete'
*/
export const completeProfile = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: completeProfile.url(options),
    method: 'post',
})

completeProfile.definition = {
    methods: ["post"],
    url: '/auth/google/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::completeProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:92
* @route '/auth/google/complete'
*/
completeProfile.url = (options?: RouteQueryOptions) => {
    return completeProfile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::completeProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:92
* @route '/auth/google/complete'
*/
completeProfile.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: completeProfile.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::completeProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:92
* @route '/auth/google/complete'
*/
const completeProfileForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: completeProfile.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\GoogleLoginController::completeProfile
* @see app/Http/Controllers/Auth/GoogleLoginController.php:92
* @route '/auth/google/complete'
*/
completeProfileForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: completeProfile.url(options),
    method: 'post',
})

completeProfile.form = completeProfileForm

const GoogleLoginController = { redirectToGoogle, handleGoogleCallback, showCompleteProfile, completeProfile }

export default GoogleLoginController