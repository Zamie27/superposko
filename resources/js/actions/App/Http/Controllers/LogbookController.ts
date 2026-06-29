import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
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

/**
* @see \App\Http\Controllers\LogbookController::storeProker
* @see app/Http/Controllers/LogbookController.php:86
* @route '/logbook/proker'
*/
export const storeProker = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeProker.url(options),
    method: 'post',
})

storeProker.definition = {
    methods: ["post"],
    url: '/logbook/proker',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LogbookController::storeProker
* @see app/Http/Controllers/LogbookController.php:86
* @route '/logbook/proker'
*/
storeProker.url = (options?: RouteQueryOptions) => {
    return storeProker.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::storeProker
* @see app/Http/Controllers/LogbookController.php:86
* @route '/logbook/proker'
*/
storeProker.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeProker.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::storeProker
* @see app/Http/Controllers/LogbookController.php:86
* @route '/logbook/proker'
*/
const storeProkerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeProker.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::storeProker
* @see app/Http/Controllers/LogbookController.php:86
* @route '/logbook/proker'
*/
storeProkerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeProker.url(options),
    method: 'post',
})

storeProker.form = storeProkerForm

/**
* @see \App\Http\Controllers\LogbookController::updateProker
* @see app/Http/Controllers/LogbookController.php:133
* @route '/logbook/proker/{proker}'
*/
export const updateProker = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateProker.url(args, options),
    method: 'put',
})

updateProker.definition = {
    methods: ["put"],
    url: '/logbook/proker/{proker}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\LogbookController::updateProker
* @see app/Http/Controllers/LogbookController.php:133
* @route '/logbook/proker/{proker}'
*/
updateProker.url = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { proker: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { proker: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            proker: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proker: typeof args.proker === 'object'
        ? args.proker.id
        : args.proker,
    }

    return updateProker.definition.url
            .replace('{proker}', parsedArgs.proker.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::updateProker
* @see app/Http/Controllers/LogbookController.php:133
* @route '/logbook/proker/{proker}'
*/
updateProker.put = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateProker.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\LogbookController::updateProker
* @see app/Http/Controllers/LogbookController.php:133
* @route '/logbook/proker/{proker}'
*/
const updateProkerForm = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateProker.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::updateProker
* @see app/Http/Controllers/LogbookController.php:133
* @route '/logbook/proker/{proker}'
*/
updateProkerForm.put = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateProker.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateProker.form = updateProkerForm

/**
* @see \App\Http\Controllers\LogbookController::destroyProker
* @see app/Http/Controllers/LogbookController.php:181
* @route '/logbook/proker/{proker}'
*/
export const destroyProker = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyProker.url(args, options),
    method: 'delete',
})

destroyProker.definition = {
    methods: ["delete"],
    url: '/logbook/proker/{proker}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LogbookController::destroyProker
* @see app/Http/Controllers/LogbookController.php:181
* @route '/logbook/proker/{proker}'
*/
destroyProker.url = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { proker: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { proker: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            proker: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        proker: typeof args.proker === 'object'
        ? args.proker.id
        : args.proker,
    }

    return destroyProker.definition.url
            .replace('{proker}', parsedArgs.proker.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::destroyProker
* @see app/Http/Controllers/LogbookController.php:181
* @route '/logbook/proker/{proker}'
*/
destroyProker.delete = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyProker.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\LogbookController::destroyProker
* @see app/Http/Controllers/LogbookController.php:181
* @route '/logbook/proker/{proker}'
*/
const destroyProkerForm = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyProker.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::destroyProker
* @see app/Http/Controllers/LogbookController.php:181
* @route '/logbook/proker/{proker}'
*/
destroyProkerForm.delete = (args: { proker: number | { id: number } } | [proker: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyProker.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyProker.form = destroyProkerForm

/**
* @see \App\Http\Controllers\LogbookController::storeLogbook
* @see app/Http/Controllers/LogbookController.php:205
* @route '/logbook/daily'
*/
export const storeLogbook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeLogbook.url(options),
    method: 'post',
})

storeLogbook.definition = {
    methods: ["post"],
    url: '/logbook/daily',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LogbookController::storeLogbook
* @see app/Http/Controllers/LogbookController.php:205
* @route '/logbook/daily'
*/
storeLogbook.url = (options?: RouteQueryOptions) => {
    return storeLogbook.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::storeLogbook
* @see app/Http/Controllers/LogbookController.php:205
* @route '/logbook/daily'
*/
storeLogbook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeLogbook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::storeLogbook
* @see app/Http/Controllers/LogbookController.php:205
* @route '/logbook/daily'
*/
const storeLogbookForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeLogbook.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::storeLogbook
* @see app/Http/Controllers/LogbookController.php:205
* @route '/logbook/daily'
*/
storeLogbookForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeLogbook.url(options),
    method: 'post',
})

storeLogbook.form = storeLogbookForm

/**
* @see \App\Http\Controllers\LogbookController::updateLogbook
* @see app/Http/Controllers/LogbookController.php:245
* @route '/logbook/daily/{logbook}'
*/
export const updateLogbook = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateLogbook.url(args, options),
    method: 'put',
})

updateLogbook.definition = {
    methods: ["put"],
    url: '/logbook/daily/{logbook}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\LogbookController::updateLogbook
* @see app/Http/Controllers/LogbookController.php:245
* @route '/logbook/daily/{logbook}'
*/
updateLogbook.url = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { logbook: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { logbook: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            logbook: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        logbook: typeof args.logbook === 'object'
        ? args.logbook.id
        : args.logbook,
    }

    return updateLogbook.definition.url
            .replace('{logbook}', parsedArgs.logbook.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::updateLogbook
* @see app/Http/Controllers/LogbookController.php:245
* @route '/logbook/daily/{logbook}'
*/
updateLogbook.put = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateLogbook.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\LogbookController::updateLogbook
* @see app/Http/Controllers/LogbookController.php:245
* @route '/logbook/daily/{logbook}'
*/
const updateLogbookForm = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateLogbook.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::updateLogbook
* @see app/Http/Controllers/LogbookController.php:245
* @route '/logbook/daily/{logbook}'
*/
updateLogbookForm.put = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateLogbook.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateLogbook.form = updateLogbookForm

/**
* @see \App\Http\Controllers\LogbookController::destroyLogbook
* @see app/Http/Controllers/LogbookController.php:293
* @route '/logbook/daily/{logbook}'
*/
export const destroyLogbook = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyLogbook.url(args, options),
    method: 'delete',
})

destroyLogbook.definition = {
    methods: ["delete"],
    url: '/logbook/daily/{logbook}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LogbookController::destroyLogbook
* @see app/Http/Controllers/LogbookController.php:293
* @route '/logbook/daily/{logbook}'
*/
destroyLogbook.url = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { logbook: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { logbook: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            logbook: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        logbook: typeof args.logbook === 'object'
        ? args.logbook.id
        : args.logbook,
    }

    return destroyLogbook.definition.url
            .replace('{logbook}', parsedArgs.logbook.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LogbookController::destroyLogbook
* @see app/Http/Controllers/LogbookController.php:293
* @route '/logbook/daily/{logbook}'
*/
destroyLogbook.delete = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyLogbook.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\LogbookController::destroyLogbook
* @see app/Http/Controllers/LogbookController.php:293
* @route '/logbook/daily/{logbook}'
*/
const destroyLogbookForm = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyLogbook.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LogbookController::destroyLogbook
* @see app/Http/Controllers/LogbookController.php:293
* @route '/logbook/daily/{logbook}'
*/
destroyLogbookForm.delete = (args: { logbook: number | { id: number } } | [logbook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyLogbook.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyLogbook.form = destroyLogbookForm

const LogbookController = { index, storeProker, updateProker, destroyProker, storeLogbook, updateLogbook, destroyLogbook }

export default LogbookController