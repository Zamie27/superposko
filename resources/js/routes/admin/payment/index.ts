import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\PaymentController::test
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
export const test = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: test.url(options),
    method: 'get',
})

test.definition = {
    methods: ["get","head"],
    url: '/admin/payment/test',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PaymentController::test
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
test.url = (options?: RouteQueryOptions) => {
    return test.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::test
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
test.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: test.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::test
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
test.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: test.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PaymentController::test
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
const testForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: test.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::test
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
testForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: test.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PaymentController::test
* @see app/Http/Controllers/PaymentController.php:23
* @route '/admin/payment/test'
*/
testForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: test.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

test.form = testForm

/**
* @see \App\Http\Controllers\PaymentController::token
* @see app/Http/Controllers/PaymentController.php:61
* @route '/admin/payment/test/token'
*/
export const token = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: token.url(options),
    method: 'post',
})

token.definition = {
    methods: ["post"],
    url: '/admin/payment/test/token',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PaymentController::token
* @see app/Http/Controllers/PaymentController.php:61
* @route '/admin/payment/test/token'
*/
token.url = (options?: RouteQueryOptions) => {
    return token.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PaymentController::token
* @see app/Http/Controllers/PaymentController.php:61
* @route '/admin/payment/test/token'
*/
token.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: token.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::token
* @see app/Http/Controllers/PaymentController.php:61
* @route '/admin/payment/test/token'
*/
const tokenForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: token.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PaymentController::token
* @see app/Http/Controllers/PaymentController.php:61
* @route '/admin/payment/test/token'
*/
tokenForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: token.url(options),
    method: 'post',
})

token.form = tokenForm

const payment = {
    test: Object.assign(test, test),
    token: Object.assign(token, token),
}

export default payment