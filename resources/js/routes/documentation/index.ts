import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\DocumentationController::publicMethod
* @see app/Http/Controllers/DocumentationController.php:430
* @route '/panduan'
*/
export const publicMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: publicMethod.url(options),
    method: 'get',
})

publicMethod.definition = {
    methods: ["get","head"],
    url: '/panduan',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DocumentationController::publicMethod
* @see app/Http/Controllers/DocumentationController.php:430
* @route '/panduan'
*/
publicMethod.url = (options?: RouteQueryOptions) => {
    return publicMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentationController::publicMethod
* @see app/Http/Controllers/DocumentationController.php:430
* @route '/panduan'
*/
publicMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: publicMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::publicMethod
* @see app/Http/Controllers/DocumentationController.php:430
* @route '/panduan'
*/
publicMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: publicMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DocumentationController::publicMethod
* @see app/Http/Controllers/DocumentationController.php:430
* @route '/panduan'
*/
const publicMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: publicMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::publicMethod
* @see app/Http/Controllers/DocumentationController.php:430
* @route '/panduan'
*/
publicMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: publicMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::publicMethod
* @see app/Http/Controllers/DocumentationController.php:430
* @route '/panduan'
*/
publicMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: publicMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

publicMethod.form = publicMethodForm

const documentation = {
    public: Object.assign(publicMethod, publicMethod),
}

export default documentation