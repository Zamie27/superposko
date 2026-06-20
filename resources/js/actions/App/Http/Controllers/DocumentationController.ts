import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DocumentationController::index
* @see app/Http/Controllers/DocumentationController.php:21
* @route '/documentation'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/documentation',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DocumentationController::index
* @see app/Http/Controllers/DocumentationController.php:21
* @route '/documentation'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentationController::index
* @see app/Http/Controllers/DocumentationController.php:21
* @route '/documentation'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::index
* @see app/Http/Controllers/DocumentationController.php:21
* @route '/documentation'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DocumentationController::index
* @see app/Http/Controllers/DocumentationController.php:21
* @route '/documentation'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::index
* @see app/Http/Controllers/DocumentationController.php:21
* @route '/documentation'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::index
* @see app/Http/Controllers/DocumentationController.php:21
* @route '/documentation'
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
* @see \App\Http\Controllers\DocumentationController::store
* @see app/Http/Controllers/DocumentationController.php:130
* @route '/documentation/upload'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/documentation/upload',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DocumentationController::store
* @see app/Http/Controllers/DocumentationController.php:130
* @route '/documentation/upload'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentationController::store
* @see app/Http/Controllers/DocumentationController.php:130
* @route '/documentation/upload'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DocumentationController::store
* @see app/Http/Controllers/DocumentationController.php:130
* @route '/documentation/upload'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DocumentationController::store
* @see app/Http/Controllers/DocumentationController.php:130
* @route '/documentation/upload'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\DocumentationController::thumbnail
* @see app/Http/Controllers/DocumentationController.php:75
* @route '/documentation/thumbnail/{id}'
*/
export const thumbnail = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: thumbnail.url(args, options),
    method: 'get',
})

thumbnail.definition = {
    methods: ["get","head"],
    url: '/documentation/thumbnail/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DocumentationController::thumbnail
* @see app/Http/Controllers/DocumentationController.php:75
* @route '/documentation/thumbnail/{id}'
*/
thumbnail.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return thumbnail.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentationController::thumbnail
* @see app/Http/Controllers/DocumentationController.php:75
* @route '/documentation/thumbnail/{id}'
*/
thumbnail.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: thumbnail.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::thumbnail
* @see app/Http/Controllers/DocumentationController.php:75
* @route '/documentation/thumbnail/{id}'
*/
thumbnail.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: thumbnail.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DocumentationController::thumbnail
* @see app/Http/Controllers/DocumentationController.php:75
* @route '/documentation/thumbnail/{id}'
*/
const thumbnailForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: thumbnail.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::thumbnail
* @see app/Http/Controllers/DocumentationController.php:75
* @route '/documentation/thumbnail/{id}'
*/
thumbnailForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: thumbnail.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::thumbnail
* @see app/Http/Controllers/DocumentationController.php:75
* @route '/documentation/thumbnail/{id}'
*/
thumbnailForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: thumbnail.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

thumbnail.form = thumbnailForm

/**
* @see \App\Http\Controllers\DocumentationController::file
* @see app/Http/Controllers/DocumentationController.php:93
* @route '/documentation/file/{id}'
*/
export const file = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: file.url(args, options),
    method: 'get',
})

file.definition = {
    methods: ["get","head"],
    url: '/documentation/file/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DocumentationController::file
* @see app/Http/Controllers/DocumentationController.php:93
* @route '/documentation/file/{id}'
*/
file.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return file.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentationController::file
* @see app/Http/Controllers/DocumentationController.php:93
* @route '/documentation/file/{id}'
*/
file.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: file.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::file
* @see app/Http/Controllers/DocumentationController.php:93
* @route '/documentation/file/{id}'
*/
file.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: file.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DocumentationController::file
* @see app/Http/Controllers/DocumentationController.php:93
* @route '/documentation/file/{id}'
*/
const fileForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: file.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::file
* @see app/Http/Controllers/DocumentationController.php:93
* @route '/documentation/file/{id}'
*/
fileForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: file.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DocumentationController::file
* @see app/Http/Controllers/DocumentationController.php:93
* @route '/documentation/file/{id}'
*/
fileForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: file.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

file.form = fileForm

const DocumentationController = { index, store, thumbnail, file }

export default DocumentationController