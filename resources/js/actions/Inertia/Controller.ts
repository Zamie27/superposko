import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/finance'
*/
const Controllerb048a0b5d791fb5ca2f61779e8b831e6 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerb048a0b5d791fb5ca2f61779e8b831e6.url(options),
    method: 'get',
})

Controllerb048a0b5d791fb5ca2f61779e8b831e6.definition = {
    methods: ["get","head"],
    url: '/finance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/finance'
*/
Controllerb048a0b5d791fb5ca2f61779e8b831e6.url = (options?: RouteQueryOptions) => {
    return Controllerb048a0b5d791fb5ca2f61779e8b831e6.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/finance'
*/
Controllerb048a0b5d791fb5ca2f61779e8b831e6.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerb048a0b5d791fb5ca2f61779e8b831e6.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/finance'
*/
Controllerb048a0b5d791fb5ca2f61779e8b831e6.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerb048a0b5d791fb5ca2f61779e8b831e6.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/finance'
*/
const Controllerb048a0b5d791fb5ca2f61779e8b831e6Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb048a0b5d791fb5ca2f61779e8b831e6.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/finance'
*/
Controllerb048a0b5d791fb5ca2f61779e8b831e6Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb048a0b5d791fb5ca2f61779e8b831e6.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/finance'
*/
Controllerb048a0b5d791fb5ca2f61779e8b831e6Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb048a0b5d791fb5ca2f61779e8b831e6.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerb048a0b5d791fb5ca2f61779e8b831e6.form = Controllerb048a0b5d791fb5ca2f61779e8b831e6Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logbook'
*/
const Controllerabb2216759cf014eb2e195195a98e6d3 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerabb2216759cf014eb2e195195a98e6d3.url(options),
    method: 'get',
})

Controllerabb2216759cf014eb2e195195a98e6d3.definition = {
    methods: ["get","head"],
    url: '/logbook',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logbook'
*/
Controllerabb2216759cf014eb2e195195a98e6d3.url = (options?: RouteQueryOptions) => {
    return Controllerabb2216759cf014eb2e195195a98e6d3.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logbook'
*/
Controllerabb2216759cf014eb2e195195a98e6d3.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerabb2216759cf014eb2e195195a98e6d3.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logbook'
*/
Controllerabb2216759cf014eb2e195195a98e6d3.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerabb2216759cf014eb2e195195a98e6d3.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logbook'
*/
const Controllerabb2216759cf014eb2e195195a98e6d3Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerabb2216759cf014eb2e195195a98e6d3.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logbook'
*/
Controllerabb2216759cf014eb2e195195a98e6d3Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerabb2216759cf014eb2e195195a98e6d3.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logbook'
*/
Controllerabb2216759cf014eb2e195195a98e6d3Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerabb2216759cf014eb2e195195a98e6d3.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerabb2216759cf014eb2e195195a98e6d3.form = Controllerabb2216759cf014eb2e195195a98e6d3Form

/**
* Multiple routes resolve to \Inertia\Controller::Controller, so this export is a
* dictionary keyed by URI rather than a callable. Call a specific route with `Controller['<uri>'](...)`,
* or import the route by name from your generated `routes/` directory.
*/
const Controller = {
    '/finance': Controllerb048a0b5d791fb5ca2f61779e8b831e6,
    '/logbook': Controllerabb2216759cf014eb2e195195a98e6d3,
}

export default Controller