import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import vote85807a from './vote'
/**
* @see \App\Http\Controllers\VotingController::store
* @see app/Http/Controllers/VotingController.php:98
* @route '/voting/poll'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/voting/poll',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\VotingController::store
* @see app/Http/Controllers/VotingController.php:98
* @route '/voting/poll'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::store
* @see app/Http/Controllers/VotingController.php:98
* @route '/voting/poll'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::store
* @see app/Http/Controllers/VotingController.php:98
* @route '/voting/poll'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::store
* @see app/Http/Controllers/VotingController.php:98
* @route '/voting/poll'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\VotingController::vote
* @see app/Http/Controllers/VotingController.php:142
* @route '/voting/poll/{poll}/vote'
*/
export const vote = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: vote.url(args, options),
    method: 'post',
})

vote.definition = {
    methods: ["post"],
    url: '/voting/poll/{poll}/vote',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\VotingController::vote
* @see app/Http/Controllers/VotingController.php:142
* @route '/voting/poll/{poll}/vote'
*/
vote.url = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return vote.definition.url
            .replace('{poll}', parsedArgs.poll.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::vote
* @see app/Http/Controllers/VotingController.php:142
* @route '/voting/poll/{poll}/vote'
*/
vote.post = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: vote.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::vote
* @see app/Http/Controllers/VotingController.php:142
* @route '/voting/poll/{poll}/vote'
*/
const voteForm = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: vote.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::vote
* @see app/Http/Controllers/VotingController.php:142
* @route '/voting/poll/{poll}/vote'
*/
voteForm.post = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: vote.url(args, options),
    method: 'post',
})

vote.form = voteForm

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:221
* @route '/voting/poll/{poll}'
*/
export const destroy = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/voting/poll/{poll}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:221
* @route '/voting/poll/{poll}'
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
* @see app/Http/Controllers/VotingController.php:221
* @route '/voting/poll/{poll}'
*/
destroy.delete = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:221
* @route '/voting/poll/{poll}'
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
* @see app/Http/Controllers/VotingController.php:221
* @route '/voting/poll/{poll}'
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

const poll = {
    store: Object.assign(store, store),
    vote: Object.assign(vote, vote85807a),
    destroy: Object.assign(destroy, destroy),
}

export default poll