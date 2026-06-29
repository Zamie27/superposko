import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:22
* @route '/management/inventory'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/management/inventory',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:22
* @route '/management/inventory'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:22
* @route '/management/inventory'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:22
* @route '/management/inventory'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:22
* @route '/management/inventory'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:22
* @route '/management/inventory'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InventoryController::index
* @see app/Http/Controllers/InventoryController.php:22
* @route '/management/inventory'
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
* @see \App\Http\Controllers\InventoryController::store
* @see app/Http/Controllers/InventoryController.php:48
* @route '/management/inventory'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/management/inventory',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InventoryController::store
* @see app/Http/Controllers/InventoryController.php:48
* @route '/management/inventory'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::store
* @see app/Http/Controllers/InventoryController.php:48
* @route '/management/inventory'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InventoryController::store
* @see app/Http/Controllers/InventoryController.php:48
* @route '/management/inventory'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InventoryController::store
* @see app/Http/Controllers/InventoryController.php:48
* @route '/management/inventory'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\InventoryController::update
* @see app/Http/Controllers/InventoryController.php:124
* @route '/management/inventory/{inventory}'
*/
export const update = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/management/inventory/{inventory}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\InventoryController::update
* @see app/Http/Controllers/InventoryController.php:124
* @route '/management/inventory/{inventory}'
*/
update.url = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { inventory: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { inventory: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            inventory: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        inventory: typeof args.inventory === 'object'
        ? args.inventory.id
        : args.inventory,
    }

    return update.definition.url
            .replace('{inventory}', parsedArgs.inventory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::update
* @see app/Http/Controllers/InventoryController.php:124
* @route '/management/inventory/{inventory}'
*/
update.put = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\InventoryController::update
* @see app/Http/Controllers/InventoryController.php:124
* @route '/management/inventory/{inventory}'
*/
const updateForm = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InventoryController::update
* @see app/Http/Controllers/InventoryController.php:124
* @route '/management/inventory/{inventory}'
*/
updateForm.put = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\InventoryController::destroy
* @see app/Http/Controllers/InventoryController.php:216
* @route '/management/inventory/{inventory}'
*/
export const destroy = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/management/inventory/{inventory}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\InventoryController::destroy
* @see app/Http/Controllers/InventoryController.php:216
* @route '/management/inventory/{inventory}'
*/
destroy.url = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { inventory: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { inventory: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            inventory: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        inventory: typeof args.inventory === 'object'
        ? args.inventory.id
        : args.inventory,
    }

    return destroy.definition.url
            .replace('{inventory}', parsedArgs.inventory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InventoryController::destroy
* @see app/Http/Controllers/InventoryController.php:216
* @route '/management/inventory/{inventory}'
*/
destroy.delete = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\InventoryController::destroy
* @see app/Http/Controllers/InventoryController.php:216
* @route '/management/inventory/{inventory}'
*/
const destroyForm = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InventoryController::destroy
* @see app/Http/Controllers/InventoryController.php:216
* @route '/management/inventory/{inventory}'
*/
destroyForm.delete = (args: { inventory: number | { id: number } } | [inventory: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const inventory = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default inventory