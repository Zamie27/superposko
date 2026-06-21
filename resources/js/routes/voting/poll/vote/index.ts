import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:202
* @route '/voting/poll/{poll}/vote'
*/
export const destroy = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/voting/poll/{poll}/vote',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:202
* @route '/voting/poll/{poll}/vote'
*/
destroy.url = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { poll: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { poll: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            poll: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        poll: typeof args.poll === 'object'
        ? args.poll.id
        : args.poll,
    }

    return destroy.definition.url
            .replace('{poll}', parsedArgs.poll.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:202
* @route '/voting/poll/{poll}/vote'
*/
destroy.delete = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:202
* @route '/voting/poll/{poll}/vote'
*/
const destroyForm = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:202
* @route '/voting/poll/{poll}/vote'
*/
destroyForm.delete = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const vote = {
    destroy: Object.assign(destroy, destroy),
}

export default vote