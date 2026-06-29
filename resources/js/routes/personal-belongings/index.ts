import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PersonalBelongingController::index
* @see app/Http/Controllers/PersonalBelongingController.php:18
* @route '/personal-belongings'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/personal-belongings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PersonalBelongingController::index
* @see app/Http/Controllers/PersonalBelongingController.php:18
* @route '/personal-belongings'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PersonalBelongingController::index
* @see app/Http/Controllers/PersonalBelongingController.php:18
* @route '/personal-belongings'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::index
* @see app/Http/Controllers/PersonalBelongingController.php:18
* @route '/personal-belongings'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::index
* @see app/Http/Controllers/PersonalBelongingController.php:18
* @route '/personal-belongings'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::index
* @see app/Http/Controllers/PersonalBelongingController.php:18
* @route '/personal-belongings'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::index
* @see app/Http/Controllers/PersonalBelongingController.php:18
* @route '/personal-belongings'
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
* @see \App\Http\Controllers\PersonalBelongingController::store
* @see app/Http/Controllers/PersonalBelongingController.php:34
* @route '/personal-belongings'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/personal-belongings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PersonalBelongingController::store
* @see app/Http/Controllers/PersonalBelongingController.php:34
* @route '/personal-belongings'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PersonalBelongingController::store
* @see app/Http/Controllers/PersonalBelongingController.php:34
* @route '/personal-belongings'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::store
* @see app/Http/Controllers/PersonalBelongingController.php:34
* @route '/personal-belongings'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::store
* @see app/Http/Controllers/PersonalBelongingController.php:34
* @route '/personal-belongings'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\PersonalBelongingController::update
* @see app/Http/Controllers/PersonalBelongingController.php:67
* @route '/personal-belongings/{personalBelonging}'
*/
export const update = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/personal-belongings/{personalBelonging}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\PersonalBelongingController::update
* @see app/Http/Controllers/PersonalBelongingController.php:67
* @route '/personal-belongings/{personalBelonging}'
*/
update.url = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { personalBelonging: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { personalBelonging: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            personalBelonging: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        personalBelonging: typeof args.personalBelonging === 'object'
        ? args.personalBelonging.id
        : args.personalBelonging,
    }

    return update.definition.url
            .replace('{personalBelonging}', parsedArgs.personalBelonging.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PersonalBelongingController::update
* @see app/Http/Controllers/PersonalBelongingController.php:67
* @route '/personal-belongings/{personalBelonging}'
*/
update.put = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::update
* @see app/Http/Controllers/PersonalBelongingController.php:67
* @route '/personal-belongings/{personalBelonging}'
*/
const updateForm = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::update
* @see app/Http/Controllers/PersonalBelongingController.php:67
* @route '/personal-belongings/{personalBelonging}'
*/
updateForm.put = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\PersonalBelongingController::destroy
* @see app/Http/Controllers/PersonalBelongingController.php:101
* @route '/personal-belongings/{personalBelonging}'
*/
export const destroy = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/personal-belongings/{personalBelonging}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\PersonalBelongingController::destroy
* @see app/Http/Controllers/PersonalBelongingController.php:101
* @route '/personal-belongings/{personalBelonging}'
*/
destroy.url = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { personalBelonging: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { personalBelonging: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            personalBelonging: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        personalBelonging: typeof args.personalBelonging === 'object'
        ? args.personalBelonging.id
        : args.personalBelonging,
    }

    return destroy.definition.url
            .replace('{personalBelonging}', parsedArgs.personalBelonging.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PersonalBelongingController::destroy
* @see app/Http/Controllers/PersonalBelongingController.php:101
* @route '/personal-belongings/{personalBelonging}'
*/
destroy.delete = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::destroy
* @see app/Http/Controllers/PersonalBelongingController.php:101
* @route '/personal-belongings/{personalBelonging}'
*/
const destroyForm = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::destroy
* @see app/Http/Controllers/PersonalBelongingController.php:101
* @route '/personal-belongings/{personalBelonging}'
*/
destroyForm.delete = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\PersonalBelongingController::togglePacked
* @see app/Http/Controllers/PersonalBelongingController.php:124
* @route '/personal-belongings/{personalBelonging}/toggle-packed'
*/
export const togglePacked = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: togglePacked.url(args, options),
    method: 'post',
})

togglePacked.definition = {
    methods: ["post"],
    url: '/personal-belongings/{personalBelonging}/toggle-packed',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PersonalBelongingController::togglePacked
* @see app/Http/Controllers/PersonalBelongingController.php:124
* @route '/personal-belongings/{personalBelonging}/toggle-packed'
*/
togglePacked.url = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { personalBelonging: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { personalBelonging: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            personalBelonging: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        personalBelonging: typeof args.personalBelonging === 'object'
        ? args.personalBelonging.id
        : args.personalBelonging,
    }

    return togglePacked.definition.url
            .replace('{personalBelonging}', parsedArgs.personalBelonging.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PersonalBelongingController::togglePacked
* @see app/Http/Controllers/PersonalBelongingController.php:124
* @route '/personal-belongings/{personalBelonging}/toggle-packed'
*/
togglePacked.post = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: togglePacked.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::togglePacked
* @see app/Http/Controllers/PersonalBelongingController.php:124
* @route '/personal-belongings/{personalBelonging}/toggle-packed'
*/
const togglePackedForm = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: togglePacked.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PersonalBelongingController::togglePacked
* @see app/Http/Controllers/PersonalBelongingController.php:124
* @route '/personal-belongings/{personalBelonging}/toggle-packed'
*/
togglePackedForm.post = (args: { personalBelonging: number | { id: number } } | [personalBelonging: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: togglePacked.url(args, options),
    method: 'post',
})

togglePacked.form = togglePackedForm

const personalBelongings = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    togglePacked: Object.assign(togglePacked, togglePacked),
}

export default personalBelongings