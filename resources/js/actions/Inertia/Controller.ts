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
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/inventory'
*/
const Controller2a5367f9244dbeae8d78e409e3429a3e = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller2a5367f9244dbeae8d78e409e3429a3e.url(options),
    method: 'get',
})

Controller2a5367f9244dbeae8d78e409e3429a3e.definition = {
    methods: ["get","head"],
    url: '/management/inventory',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/inventory'
*/
Controller2a5367f9244dbeae8d78e409e3429a3e.url = (options?: RouteQueryOptions) => {
    return Controller2a5367f9244dbeae8d78e409e3429a3e.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/inventory'
*/
Controller2a5367f9244dbeae8d78e409e3429a3e.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller2a5367f9244dbeae8d78e409e3429a3e.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/inventory'
*/
Controller2a5367f9244dbeae8d78e409e3429a3e.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller2a5367f9244dbeae8d78e409e3429a3e.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/inventory'
*/
const Controller2a5367f9244dbeae8d78e409e3429a3eForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2a5367f9244dbeae8d78e409e3429a3e.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/inventory'
*/
Controller2a5367f9244dbeae8d78e409e3429a3eForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2a5367f9244dbeae8d78e409e3429a3e.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/inventory'
*/
Controller2a5367f9244dbeae8d78e409e3429a3eForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2a5367f9244dbeae8d78e409e3429a3e.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller2a5367f9244dbeae8d78e409e3429a3e.form = Controller2a5367f9244dbeae8d78e409e3429a3eForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/logistic'
*/
const Controllerbc78e265ee946ca595d9bc944a4839f0 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerbc78e265ee946ca595d9bc944a4839f0.url(options),
    method: 'get',
})

Controllerbc78e265ee946ca595d9bc944a4839f0.definition = {
    methods: ["get","head"],
    url: '/management/logistic',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/logistic'
*/
Controllerbc78e265ee946ca595d9bc944a4839f0.url = (options?: RouteQueryOptions) => {
    return Controllerbc78e265ee946ca595d9bc944a4839f0.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/logistic'
*/
Controllerbc78e265ee946ca595d9bc944a4839f0.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerbc78e265ee946ca595d9bc944a4839f0.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/logistic'
*/
Controllerbc78e265ee946ca595d9bc944a4839f0.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerbc78e265ee946ca595d9bc944a4839f0.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/logistic'
*/
const Controllerbc78e265ee946ca595d9bc944a4839f0Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerbc78e265ee946ca595d9bc944a4839f0.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/logistic'
*/
Controllerbc78e265ee946ca595d9bc944a4839f0Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerbc78e265ee946ca595d9bc944a4839f0.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/logistic'
*/
Controllerbc78e265ee946ca595d9bc944a4839f0Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerbc78e265ee946ca595d9bc944a4839f0.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerbc78e265ee946ca595d9bc944a4839f0.form = Controllerbc78e265ee946ca595d9bc944a4839f0Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/schedule'
*/
const Controller383de1a6870bc27bd361c37542f71c41 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller383de1a6870bc27bd361c37542f71c41.url(options),
    method: 'get',
})

Controller383de1a6870bc27bd361c37542f71c41.definition = {
    methods: ["get","head"],
    url: '/management/schedule',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/schedule'
*/
Controller383de1a6870bc27bd361c37542f71c41.url = (options?: RouteQueryOptions) => {
    return Controller383de1a6870bc27bd361c37542f71c41.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/schedule'
*/
Controller383de1a6870bc27bd361c37542f71c41.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller383de1a6870bc27bd361c37542f71c41.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/schedule'
*/
Controller383de1a6870bc27bd361c37542f71c41.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller383de1a6870bc27bd361c37542f71c41.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/schedule'
*/
const Controller383de1a6870bc27bd361c37542f71c41Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller383de1a6870bc27bd361c37542f71c41.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/schedule'
*/
Controller383de1a6870bc27bd361c37542f71c41Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller383de1a6870bc27bd361c37542f71c41.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/management/schedule'
*/
Controller383de1a6870bc27bd361c37542f71c41Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller383de1a6870bc27bd361c37542f71c41.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller383de1a6870bc27bd361c37542f71c41.form = Controller383de1a6870bc27bd361c37542f71c41Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/voting'
*/
const Controller4f39f405039961f7fb66175445447eec = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller4f39f405039961f7fb66175445447eec.url(options),
    method: 'get',
})

Controller4f39f405039961f7fb66175445447eec.definition = {
    methods: ["get","head"],
    url: '/voting',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/voting'
*/
Controller4f39f405039961f7fb66175445447eec.url = (options?: RouteQueryOptions) => {
    return Controller4f39f405039961f7fb66175445447eec.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/voting'
*/
Controller4f39f405039961f7fb66175445447eec.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller4f39f405039961f7fb66175445447eec.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/voting'
*/
Controller4f39f405039961f7fb66175445447eec.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller4f39f405039961f7fb66175445447eec.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/voting'
*/
const Controller4f39f405039961f7fb66175445447eecForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller4f39f405039961f7fb66175445447eec.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/voting'
*/
Controller4f39f405039961f7fb66175445447eecForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller4f39f405039961f7fb66175445447eec.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/voting'
*/
Controller4f39f405039961f7fb66175445447eecForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller4f39f405039961f7fb66175445447eec.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller4f39f405039961f7fb66175445447eec.form = Controller4f39f405039961f7fb66175445447eecForm

/**
* Multiple routes resolve to \Inertia\Controller::Controller, so this export is a
* dictionary keyed by URI rather than a callable. Call a specific route with `Controller['<uri>'](...)`,
* or import the route by name from your generated `routes/` directory.
*/
const Controller = {
    '/finance': Controllerb048a0b5d791fb5ca2f61779e8b831e6,
    '/logbook': Controllerabb2216759cf014eb2e195195a98e6d3,
    '/management/inventory': Controller2a5367f9244dbeae8d78e409e3429a3e,
    '/management/logistic': Controllerbc78e265ee946ca595d9bc944a4839f0,
    '/management/schedule': Controller383de1a6870bc27bd361c37542f71c41,
    '/voting': Controller4f39f405039961f7fb66175445447eec,
}

export default Controller