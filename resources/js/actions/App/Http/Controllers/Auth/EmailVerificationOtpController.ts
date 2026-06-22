import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::verify
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:15
* @route '/email/verify-otp'
*/
export const verify = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify.url(options),
    method: 'post',
})

verify.definition = {
    methods: ["post"],
    url: '/email/verify-otp',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::verify
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:15
* @route '/email/verify-otp'
*/
verify.url = (options?: RouteQueryOptions) => {
    return verify.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::verify
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:15
* @route '/email/verify-otp'
*/
verify.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::verify
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:15
* @route '/email/verify-otp'
*/
const verifyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verify.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::verify
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:15
* @route '/email/verify-otp'
*/
verifyForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verify.url(options),
    method: 'post',
})

verify.form = verifyForm

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::resend
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:63
* @route '/email/resend-otp'
*/
export const resend = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resend.url(options),
    method: 'post',
})

resend.definition = {
    methods: ["post"],
    url: '/email/resend-otp',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::resend
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:63
* @route '/email/resend-otp'
*/
resend.url = (options?: RouteQueryOptions) => {
    return resend.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::resend
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:63
* @route '/email/resend-otp'
*/
resend.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resend.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::resend
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:63
* @route '/email/resend-otp'
*/
const resendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resend.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::resend
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:63
* @route '/email/resend-otp'
*/
resendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resend.url(options),
    method: 'post',
})

resend.form = resendForm

const EmailVerificationOtpController = { verify, resend }

export default EmailVerificationOtpController