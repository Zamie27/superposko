import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\VotingController::store
* @see app/Http/Controllers/VotingController.php:255
* @route '/voting/aspiration'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/voting/aspiration',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\VotingController::store
* @see app/Http/Controllers/VotingController.php:255
* @route '/voting/aspiration'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::store
* @see app/Http/Controllers/VotingController.php:255
* @route '/voting/aspiration'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::store
* @see app/Http/Controllers/VotingController.php:255
* @route '/voting/aspiration'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::store
* @see app/Http/Controllers/VotingController.php:255
* @route '/voting/aspiration'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\VotingController::like
* @see app/Http/Controllers/VotingController.php:287
* @route '/voting/aspiration/{aspiration}/like'
*/
export const like = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: like.url(args, options),
    method: 'post',
})

like.definition = {
    methods: ["post"],
    url: '/voting/aspiration/{aspiration}/like',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\VotingController::like
* @see app/Http/Controllers/VotingController.php:287
* @route '/voting/aspiration/{aspiration}/like'
*/
like.url = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return like.definition.url
            .replace('{aspiration}', parsedArgs.aspiration.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::like
* @see app/Http/Controllers/VotingController.php:287
* @route '/voting/aspiration/{aspiration}/like'
*/
like.post = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: like.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::like
* @see app/Http/Controllers/VotingController.php:287
* @route '/voting/aspiration/{aspiration}/like'
*/
const likeForm = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: like.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::like
* @see app/Http/Controllers/VotingController.php:287
* @route '/voting/aspiration/{aspiration}/like'
*/
likeForm.post = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: like.url(args, options),
    method: 'post',
})

like.form = likeForm

/**
* @see \App\Http\Controllers\VotingController::respond
* @see app/Http/Controllers/VotingController.php:315
* @route '/voting/aspiration/{aspiration}/respond'
*/
export const respond = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: respond.url(args, options),
    method: 'put',
})

respond.definition = {
    methods: ["put"],
    url: '/voting/aspiration/{aspiration}/respond',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\VotingController::respond
* @see app/Http/Controllers/VotingController.php:315
* @route '/voting/aspiration/{aspiration}/respond'
*/
respond.url = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return respond.definition.url
            .replace('{aspiration}', parsedArgs.aspiration.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::respond
* @see app/Http/Controllers/VotingController.php:315
* @route '/voting/aspiration/{aspiration}/respond'
*/
respond.put = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: respond.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\VotingController::respond
* @see app/Http/Controllers/VotingController.php:315
* @route '/voting/aspiration/{aspiration}/respond'
*/
const respondForm = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: respond.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\VotingController::respond
* @see app/Http/Controllers/VotingController.php:315
* @route '/voting/aspiration/{aspiration}/respond'
*/
respondForm.put = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: respond.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

respond.form = respondForm

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:346
* @route '/voting/aspiration/{aspiration}'
*/
export const destroy = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/voting/aspiration/{aspiration}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:346
* @route '/voting/aspiration/{aspiration}'
*/
destroy.url = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{aspiration}', parsedArgs.aspiration.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:346
* @route '/voting/aspiration/{aspiration}'
*/
destroy.delete = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\VotingController::destroy
* @see app/Http/Controllers/VotingController.php:346
* @route '/voting/aspiration/{aspiration}'
*/
const destroyForm = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/VotingController.php:346
* @route '/voting/aspiration/{aspiration}'
*/
destroyForm.delete = (args: { aspiration: number | { id: number } } | [aspiration: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const aspiration = {
    store: Object.assign(store, store),
    like: Object.assign(like, like),
    respond: Object.assign(respond, respond),
    destroy: Object.assign(destroy, destroy),
}

export default aspiration