import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\EmailChangeController::sendOtp
* @see app/Http/Controllers/Settings/EmailChangeController.php:20
* @route '/settings/profile/email-otp'
*/
export const sendOtp = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendOtp.url(options),
    method: 'post',
})

sendOtp.definition = {
    methods: ["post"],
    url: '/settings/profile/email-otp',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::sendOtp
* @see app/Http/Controllers/Settings/EmailChangeController.php:20
* @route '/settings/profile/email-otp'
*/
sendOtp.url = (options?: RouteQueryOptions) => {
    return sendOtp.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::sendOtp
* @see app/Http/Controllers/Settings/EmailChangeController.php:20
* @route '/settings/profile/email-otp'
*/
sendOtp.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendOtp.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::sendOtp
* @see app/Http/Controllers/Settings/EmailChangeController.php:20
* @route '/settings/profile/email-otp'
*/
const sendOtpForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendOtp.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::sendOtp
* @see app/Http/Controllers/Settings/EmailChangeController.php:20
* @route '/settings/profile/email-otp'
*/
sendOtpForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendOtp.url(options),
    method: 'post',
})

sendOtp.form = sendOtpForm

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::verifyAndChange
* @see app/Http/Controllers/Settings/EmailChangeController.php:73
* @route '/settings/profile/email-change'
*/
export const verifyAndChange = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: verifyAndChange.url(options),
    method: 'put',
})

verifyAndChange.definition = {
    methods: ["put"],
    url: '/settings/profile/email-change',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::verifyAndChange
* @see app/Http/Controllers/Settings/EmailChangeController.php:73
* @route '/settings/profile/email-change'
*/
verifyAndChange.url = (options?: RouteQueryOptions) => {
    return verifyAndChange.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::verifyAndChange
* @see app/Http/Controllers/Settings/EmailChangeController.php:73
* @route '/settings/profile/email-change'
*/
verifyAndChange.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: verifyAndChange.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::verifyAndChange
* @see app/Http/Controllers/Settings/EmailChangeController.php:73
* @route '/settings/profile/email-change'
*/
const verifyAndChangeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verifyAndChange.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\EmailChangeController::verifyAndChange
* @see app/Http/Controllers/Settings/EmailChangeController.php:73
* @route '/settings/profile/email-change'
*/
verifyAndChangeForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verifyAndChange.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

verifyAndChange.form = verifyAndChangeForm

const EmailChangeController = { sendOtp, verifyAndChange }

export default EmailChangeController