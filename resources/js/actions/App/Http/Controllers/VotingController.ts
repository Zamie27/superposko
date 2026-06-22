import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
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

/**
* @see \App\Http\Controllers\VotingController::storePoll
* @see app/Http/Controllers/VotingController.php:106
* @route '/voting/poll'
*/
export const storePoll = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storePoll.url(options),
    method: 'post',
})

storePoll.definition = {
    methods: ["post"],
    url: '/voting/poll',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\VotingController::storePoll
* @see app/Http/Controllers/VotingController.php:106
* @route '/voting/poll'
*/
storePoll.url = (options?: RouteQueryOptions) => {
    return storePoll.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::storePoll
* @see app/Http/Controllers/VotingController.php:106
* @route '/voting/poll'
*/
storePoll.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storePoll.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::storePoll
* @see app/Http/Controllers/VotingController.php:106
* @route '/voting/poll'
*/
const storePollForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storePoll.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::storePoll
* @see app/Http/Controllers/VotingController.php:106
* @route '/voting/poll'
*/
storePollForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storePoll.url(options),
    method: 'post',
})

storePoll.form = storePollForm

/**
* @see \App\Http\Controllers\VotingController::vote
* @see app/Http/Controllers/VotingController.php:150
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
* @see app/Http/Controllers/VotingController.php:150
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
* @see app/Http/Controllers/VotingController.php:150
* @route '/voting/poll/{poll}/vote'
*/
vote.post = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: vote.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::vote
* @see app/Http/Controllers/VotingController.php:150
* @route '/voting/poll/{poll}/vote'
*/
const voteForm = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: vote.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::vote
* @see app/Http/Controllers/VotingController.php:150
* @route '/voting/poll/{poll}/vote'
*/
voteForm.post = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: vote.url(args, options),
    method: 'post',
})

vote.form = voteForm

/**
* @see \App\Http\Controllers\VotingController::cancelVote
* @see app/Http/Controllers/VotingController.php:203
* @route '/voting/poll/{poll}/vote'
*/
export const cancelVote = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: cancelVote.url(args, options),
    method: 'delete',
})

cancelVote.definition = {
    methods: ["delete"],
    url: '/voting/poll/{poll}/vote',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\VotingController::cancelVote
* @see app/Http/Controllers/VotingController.php:203
* @route '/voting/poll/{poll}/vote'
*/
cancelVote.url = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return cancelVote.definition.url
            .replace('{poll}', parsedArgs.poll.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::cancelVote
* @see app/Http/Controllers/VotingController.php:203
* @route '/voting/poll/{poll}/vote'
*/
cancelVote.delete = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: cancelVote.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\VotingController::cancelVote
* @see app/Http/Controllers/VotingController.php:203
* @route '/voting/poll/{poll}/vote'
*/
const cancelVoteForm = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancelVote.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::cancelVote
* @see app/Http/Controllers/VotingController.php:203
* @route '/voting/poll/{poll}/vote'
*/
cancelVoteForm.delete = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancelVote.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

cancelVote.form = cancelVoteForm

/**
* @see \App\Http\Controllers\VotingController::destroyPoll
* @see app/Http/Controllers/VotingController.php:232
* @route '/voting/poll/{poll}'
*/
export const destroyPoll = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyPoll.url(args, options),
    method: 'delete',
})

destroyPoll.definition = {
    methods: ["delete"],
    url: '/voting/poll/{poll}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\VotingController::destroyPoll
* @see app/Http/Controllers/VotingController.php:232
* @route '/voting/poll/{poll}'
*/
destroyPoll.url = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroyPoll.definition.url
            .replace('{poll}', parsedArgs.poll.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::destroyPoll
* @see app/Http/Controllers/VotingController.php:232
* @route '/voting/poll/{poll}'
*/
destroyPoll.delete = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyPoll.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\VotingController::destroyPoll
* @see app/Http/Controllers/VotingController.php:232
* @route '/voting/poll/{poll}'
*/
const destroyPollForm = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyPoll.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::destroyPoll
* @see app/Http/Controllers/VotingController.php:232
* @route '/voting/poll/{poll}'
*/
destroyPollForm.delete = (args: { poll: number | { id: number } } | [poll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyPoll.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyPoll.form = destroyPollForm

/**
* @see \App\Http\Controllers\VotingController::storeAspiration
* @see app/Http/Controllers/VotingController.php:255
* @route '/voting/aspiration'
*/
export const storeAspiration = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeAspiration.url(options),
    method: 'post',
})

storeAspiration.definition = {
    methods: ["post"],
    url: '/voting/aspiration',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\VotingController::storeAspiration
* @see app/Http/Controllers/VotingController.php:255
* @route '/voting/aspiration'
*/
storeAspiration.url = (options?: RouteQueryOptions) => {
    return storeAspiration.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::storeAspiration
* @see app/Http/Controllers/VotingController.php:255
* @route '/voting/aspiration'
*/
storeAspiration.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeAspiration.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::storeAspiration
* @see app/Http/Controllers/VotingController.php:255
* @route '/voting/aspiration'
*/
const storeAspirationForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeAspiration.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::storeAspiration
* @see app/Http/Controllers/VotingController.php:255
* @route '/voting/aspiration'
*/
storeAspirationForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeAspiration.url(options),
    method: 'post',
})

storeAspiration.form = storeAspirationForm

/**
* @see \App\Http\Controllers\VotingController::likeAspiration
* @see app/Http/Controllers/VotingController.php:287
* @route '/voting/aspiration/{aspiration}/like'
*/
export const likeAspiration = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: likeAspiration.url(args, options),
    method: 'post',
})

likeAspiration.definition = {
    methods: ["post"],
    url: '/voting/aspiration/{aspiration}/like',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\VotingController::likeAspiration
* @see app/Http/Controllers/VotingController.php:287
* @route '/voting/aspiration/{aspiration}/like'
*/
likeAspiration.url = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { aspiration: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { aspiration: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            aspiration: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        aspiration: typeof args.aspiration === 'object'
        ? args.aspiration.id
        : args.aspiration,
    }

    return likeAspiration.definition.url
            .replace('{aspiration}', parsedArgs.aspiration.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::likeAspiration
* @see app/Http/Controllers/VotingController.php:287
* @route '/voting/aspiration/{aspiration}/like'
*/
likeAspiration.post = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: likeAspiration.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::likeAspiration
* @see app/Http/Controllers/VotingController.php:287
* @route '/voting/aspiration/{aspiration}/like'
*/
const likeAspirationForm = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: likeAspiration.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::likeAspiration
* @see app/Http/Controllers/VotingController.php:287
* @route '/voting/aspiration/{aspiration}/like'
*/
likeAspirationForm.post = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: likeAspiration.url(args, options),
    method: 'post',
})

likeAspiration.form = likeAspirationForm

/**
* @see \App\Http\Controllers\VotingController::respondAspiration
* @see app/Http/Controllers/VotingController.php:315
* @route '/voting/aspiration/{aspiration}/respond'
*/
export const respondAspiration = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: respondAspiration.url(args, options),
    method: 'put',
})

respondAspiration.definition = {
    methods: ["put"],
    url: '/voting/aspiration/{aspiration}/respond',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\VotingController::respondAspiration
* @see app/Http/Controllers/VotingController.php:315
* @route '/voting/aspiration/{aspiration}/respond'
*/
respondAspiration.url = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { aspiration: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { aspiration: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            aspiration: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        aspiration: typeof args.aspiration === 'object'
        ? args.aspiration.id
        : args.aspiration,
    }

    return respondAspiration.definition.url
            .replace('{aspiration}', parsedArgs.aspiration.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::respondAspiration
* @see app/Http/Controllers/VotingController.php:315
* @route '/voting/aspiration/{aspiration}/respond'
*/
respondAspiration.put = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: respondAspiration.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\VotingController::respondAspiration
* @see app/Http/Controllers/VotingController.php:315
* @route '/voting/aspiration/{aspiration}/respond'
*/
const respondAspirationForm = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: respondAspiration.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::respondAspiration
* @see app/Http/Controllers/VotingController.php:315
* @route '/voting/aspiration/{aspiration}/respond'
*/
respondAspirationForm.put = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: respondAspiration.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

respondAspiration.form = respondAspirationForm

/**
* @see \App\Http\Controllers\VotingController::destroyAspiration
* @see app/Http/Controllers/VotingController.php:346
* @route '/voting/aspiration/{aspiration}'
*/
export const destroyAspiration = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyAspiration.url(args, options),
    method: 'delete',
})

destroyAspiration.definition = {
    methods: ["delete"],
    url: '/voting/aspiration/{aspiration}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\VotingController::destroyAspiration
* @see app/Http/Controllers/VotingController.php:346
* @route '/voting/aspiration/{aspiration}'
*/
destroyAspiration.url = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { aspiration: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { aspiration: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            aspiration: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        aspiration: typeof args.aspiration === 'object'
        ? args.aspiration.id
        : args.aspiration,
    }

    return destroyAspiration.definition.url
            .replace('{aspiration}', parsedArgs.aspiration.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::destroyAspiration
* @see app/Http/Controllers/VotingController.php:346
* @route '/voting/aspiration/{aspiration}'
*/
destroyAspiration.delete = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyAspiration.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\VotingController::destroyAspiration
* @see app/Http/Controllers/VotingController.php:346
* @route '/voting/aspiration/{aspiration}'
*/
const destroyAspirationForm = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyAspiration.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::destroyAspiration
* @see app/Http/Controllers/VotingController.php:346
* @route '/voting/aspiration/{aspiration}'
*/
destroyAspirationForm.delete = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyAspiration.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyAspiration.form = destroyAspirationForm

const VotingController = { index, storePoll, vote, cancelVote, destroyPoll, storeAspiration, likeAspiration, respondAspiration, destroyAspiration }

export default VotingController