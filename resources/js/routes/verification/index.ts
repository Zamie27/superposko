import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
export const notice = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notice.url(options),
    method: 'get',
})

notice.definition = {
    methods: ["get","head"],
    url: '/email/verify',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
notice.url = (options?: RouteQueryOptions) => {
    return notice.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
notice.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notice.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
notice.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: notice.url(options),
    method: 'head',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
const noticeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notice.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
noticeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notice.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationPromptController::notice
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationPromptController.php:18
* @route '/email/verify'
*/
noticeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notice.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

notice.form = noticeForm

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
export const verify = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

verify.definition = {
    methods: ["get","head"],
    url: '/email/verify/{id}/{hash}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
verify.url = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            hash: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
        hash: args.hash,
    }

    return verify.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{hash}', parsedArgs.hash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
verify.get = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
verify.head = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verify.url(args, options),
    method: 'head',
})

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
const verifyForm = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
verifyForm.get = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\VerifyEmailController::verify
* @see vendor/laravel/fortify/src/Http/Controllers/VerifyEmailController.php:18
* @route '/email/verify/{id}/{hash}'
*/
verifyForm.head = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

verify.form = verifyForm

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::send
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationNotificationController.php:19
* @route '/email/verification-notification'
*/
export const send = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/email/verification-notification',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::send
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationNotificationController.php:19
* @route '/email/verification-notification'
*/
send.url = (options?: RouteQueryOptions) => {
    return send.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::send
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationNotificationController.php:19
* @route '/email/verification-notification'
*/
send.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::send
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationNotificationController.php:19
* @route '/email/verification-notification'
*/
const sendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

/**
* @see \Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController::send
* @see vendor/laravel/fortify/src/Http/Controllers/EmailVerificationNotificationController.php:19
* @route '/email/verification-notification'
*/
sendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

send.form = sendForm

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::verify_otp
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:15
* @route '/email/verify-otp'
*/
export const verify_otp = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify_otp.url(options),
    method: 'post',
})

verify_otp.definition = {
    methods: ["post"],
    url: '/email/verify-otp',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::verify_otp
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:15
* @route '/email/verify-otp'
*/
verify_otp.url = (options?: RouteQueryOptions) => {
    return verify_otp.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::verify_otp
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:15
* @route '/email/verify-otp'
*/
verify_otp.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify_otp.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::verify_otp
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:15
* @route '/email/verify-otp'
*/
const verify_otpForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verify_otp.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::verify_otp
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:15
* @route '/email/verify-otp'
*/
verify_otpForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verify_otp.url(options),
    method: 'post',
})

verify_otp.form = verify_otpForm

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::resend_otp
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:63
* @route '/email/resend-otp'
*/
export const resend_otp = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resend_otp.url(options),
    method: 'post',
})

resend_otp.definition = {
    methods: ["post"],
    url: '/email/resend-otp',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::resend_otp
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:63
* @route '/email/resend-otp'
*/
resend_otp.url = (options?: RouteQueryOptions) => {
    return resend_otp.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::resend_otp
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:63
* @route '/email/resend-otp'
*/
resend_otp.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resend_otp.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::resend_otp
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:63
* @route '/email/resend-otp'
*/
const resend_otpForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resend_otp.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationOtpController::resend_otp
* @see app/Http/Controllers/Auth/EmailVerificationOtpController.php:63
* @route '/email/resend-otp'
*/
resend_otpForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resend_otp.url(options),
    method: 'post',
})

resend_otp.form = resend_otpForm

const verification = {
    notice: Object.assign(notice, notice),
    verify: Object.assign(verify, verify),
    send: Object.assign(send, send),
    verify_otp: Object.assign(verify_otp, verify_otp),
    resend_otp: Object.assign(resend_otp, resend_otp),
}

export default verification