import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\EmailChangeController::otp
* @see app/Http/Controllers/Settings/EmailChangeController.php:20
* @route '/settings/profile/email-otp'
*/
export const otp = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: otp.url(options),
    method: 'post',
})

otp.definition = {
    methods: ["post"],
    url: '/settings/profile/email-otp',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::otp
* @see app/Http/Controllers/Settings/EmailChangeController.php:20
* @route '/settings/profile/email-otp'
*/
otp.url = (options?: RouteQueryOptions) => {
    return otp.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::otp
* @see app/Http/Controllers/Settings/EmailChangeController.php:20
* @route '/settings/profile/email-otp'
*/
otp.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: otp.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::otp
* @see app/Http/Controllers/Settings/EmailChangeController.php:20
* @route '/settings/profile/email-otp'
*/
const otpForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: otp.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::otp
* @see app/Http/Controllers/Settings/EmailChangeController.php:20
* @route '/settings/profile/email-otp'
*/
otpForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: otp.url(options),
    method: 'post',
})

otp.form = otpForm

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::change
* @see app/Http/Controllers/Settings/EmailChangeController.php:73
* @route '/settings/profile/email-change'
*/
export const change = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: change.url(options),
    method: 'put',
})

change.definition = {
    methods: ["put"],
    url: '/settings/profile/email-change',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::change
* @see app/Http/Controllers/Settings/EmailChangeController.php:73
* @route '/settings/profile/email-change'
*/
change.url = (options?: RouteQueryOptions) => {
    return change.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::change
* @see app/Http/Controllers/Settings/EmailChangeController.php:73
* @route '/settings/profile/email-change'
*/
change.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: change.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::change
* @see app/Http/Controllers/Settings/EmailChangeController.php:73
* @route '/settings/profile/email-change'
*/
const changeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: change.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::change
* @see app/Http/Controllers/Settings/EmailChangeController.php:73
* @route '/settings/profile/email-change'
*/
changeForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: change.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

change.form = changeForm

const email = {
    otp: Object.assign(otp, otp),
    change: Object.assign(change, change),
}

export default email