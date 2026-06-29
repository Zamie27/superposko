import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import proker from './proker'
import daily from './daily'
/**
* @see \App\Http\Controllers\LogbookController::index
* @see app/Http/Controllers/LogbookController.php:22
* @route '/logbook'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/logbook',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LogbookController::index
* @see app/Http/Controllers/LogbookController.php:22
* @route '/logbook'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::index
* @see app/Http/Controllers/LogbookController.php:22
* @route '/logbook'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LogbookController::index
* @see app/Http/Controllers/LogbookController.php:22
* @route '/logbook'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LogbookController::index
* @see app/Http/Controllers/LogbookController.php:22
* @route '/logbook'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LogbookController::index
* @see app/Http/Controllers/LogbookController.php:22
* @route '/logbook'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LogbookController::index
* @see app/Http/Controllers/LogbookController.php:22
* @route '/logbook'
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

const logbook = {
    index: Object.assign(index, index),
    proker: Object.assign(proker, proker),
    daily: Object.assign(daily, daily),
}

export default logbook