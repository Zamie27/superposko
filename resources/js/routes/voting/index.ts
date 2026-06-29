import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import poll from './poll'
import aspiration from './aspiration'
/**
* @see \App\Http\Controllers\VotingController::index
* @see app/Http/Controllers/VotingController.php:24
* @route '/voting'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/voting',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\VotingController::index
* @see app/Http/Controllers/VotingController.php:24
* @route '/voting'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::index
* @see app/Http/Controllers/VotingController.php:24
* @route '/voting'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VotingController::index
* @see app/Http/Controllers/VotingController.php:24
* @route '/voting'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\VotingController::index
* @see app/Http/Controllers/VotingController.php:24
* @route '/voting'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VotingController::index
* @see app/Http/Controllers/VotingController.php:24
* @route '/voting'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\VotingController::index
* @see app/Http/Controllers/VotingController.php:24
* @route '/voting'
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

const voting = {
    index: Object.assign(index, index),
    poll: Object.assign(poll, poll),
    aspiration: Object.assign(aspiration, aspiration),
}

export default voting