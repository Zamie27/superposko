import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/dashboard'
*/
const Controller518f53198343699ca0810f3ed7566e3f = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller518f53198343699ca0810f3ed7566e3f.url(options),
    method: 'get',
})

Controller518f53198343699ca0810f3ed7566e3f.definition = {
    methods: ["get","head"],
    url: '/host/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/dashboard'
*/
Controller518f53198343699ca0810f3ed7566e3f.url = (options?: RouteQueryOptions) => {
    return Controller518f53198343699ca0810f3ed7566e3f.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/dashboard'
*/
Controller518f53198343699ca0810f3ed7566e3f.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller518f53198343699ca0810f3ed7566e3f.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/dashboard'
*/
Controller518f53198343699ca0810f3ed7566e3f.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller518f53198343699ca0810f3ed7566e3f.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/dashboard'
*/
const Controller518f53198343699ca0810f3ed7566e3fForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller518f53198343699ca0810f3ed7566e3f.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/dashboard'
*/
Controller518f53198343699ca0810f3ed7566e3fForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller518f53198343699ca0810f3ed7566e3f.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/dashboard'
*/
Controller518f53198343699ca0810f3ed7566e3fForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller518f53198343699ca0810f3ed7566e3f.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller518f53198343699ca0810f3ed7566e3f.form = Controller518f53198343699ca0810f3ed7566e3fForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/finance'
*/
const Controllerae6a23dab1f33d2b4933cdf047368094 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerae6a23dab1f33d2b4933cdf047368094.url(options),
    method: 'get',
})

Controllerae6a23dab1f33d2b4933cdf047368094.definition = {
    methods: ["get","head"],
    url: '/host/finance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/finance'
*/
Controllerae6a23dab1f33d2b4933cdf047368094.url = (options?: RouteQueryOptions) => {
    return Controllerae6a23dab1f33d2b4933cdf047368094.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/finance'
*/
Controllerae6a23dab1f33d2b4933cdf047368094.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerae6a23dab1f33d2b4933cdf047368094.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/finance'
*/
Controllerae6a23dab1f33d2b4933cdf047368094.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerae6a23dab1f33d2b4933cdf047368094.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/finance'
*/
const Controllerae6a23dab1f33d2b4933cdf047368094Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerae6a23dab1f33d2b4933cdf047368094.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/finance'
*/
Controllerae6a23dab1f33d2b4933cdf047368094Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerae6a23dab1f33d2b4933cdf047368094.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/finance'
*/
Controllerae6a23dab1f33d2b4933cdf047368094Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerae6a23dab1f33d2b4933cdf047368094.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerae6a23dab1f33d2b4933cdf047368094.form = Controllerae6a23dab1f33d2b4933cdf047368094Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/logbook'
*/
const Controller2f87723b7b6d46b903ec42cbf539764f = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller2f87723b7b6d46b903ec42cbf539764f.url(options),
    method: 'get',
})

Controller2f87723b7b6d46b903ec42cbf539764f.definition = {
    methods: ["get","head"],
    url: '/host/logbook',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/logbook'
*/
Controller2f87723b7b6d46b903ec42cbf539764f.url = (options?: RouteQueryOptions) => {
    return Controller2f87723b7b6d46b903ec42cbf539764f.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/logbook'
*/
Controller2f87723b7b6d46b903ec42cbf539764f.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller2f87723b7b6d46b903ec42cbf539764f.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/logbook'
*/
Controller2f87723b7b6d46b903ec42cbf539764f.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller2f87723b7b6d46b903ec42cbf539764f.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/logbook'
*/
const Controller2f87723b7b6d46b903ec42cbf539764fForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2f87723b7b6d46b903ec42cbf539764f.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/logbook'
*/
Controller2f87723b7b6d46b903ec42cbf539764fForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2f87723b7b6d46b903ec42cbf539764f.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/logbook'
*/
Controller2f87723b7b6d46b903ec42cbf539764fForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2f87723b7b6d46b903ec42cbf539764f.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller2f87723b7b6d46b903ec42cbf539764f.form = Controller2f87723b7b6d46b903ec42cbf539764fForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/inventory'
*/
const Controller717ff98c45c5e08937f5b1ff0a43c8bb = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller717ff98c45c5e08937f5b1ff0a43c8bb.url(options),
    method: 'get',
})

Controller717ff98c45c5e08937f5b1ff0a43c8bb.definition = {
    methods: ["get","head"],
    url: '/host/management/inventory',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/inventory'
*/
Controller717ff98c45c5e08937f5b1ff0a43c8bb.url = (options?: RouteQueryOptions) => {
    return Controller717ff98c45c5e08937f5b1ff0a43c8bb.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/inventory'
*/
Controller717ff98c45c5e08937f5b1ff0a43c8bb.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller717ff98c45c5e08937f5b1ff0a43c8bb.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/inventory'
*/
Controller717ff98c45c5e08937f5b1ff0a43c8bb.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller717ff98c45c5e08937f5b1ff0a43c8bb.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/inventory'
*/
const Controller717ff98c45c5e08937f5b1ff0a43c8bbForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller717ff98c45c5e08937f5b1ff0a43c8bb.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/inventory'
*/
Controller717ff98c45c5e08937f5b1ff0a43c8bbForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller717ff98c45c5e08937f5b1ff0a43c8bb.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/inventory'
*/
Controller717ff98c45c5e08937f5b1ff0a43c8bbForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller717ff98c45c5e08937f5b1ff0a43c8bb.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller717ff98c45c5e08937f5b1ff0a43c8bb.form = Controller717ff98c45c5e08937f5b1ff0a43c8bbForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/logistic'
*/
const Controller2d371f6f9e64454dee18253e3d599097 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller2d371f6f9e64454dee18253e3d599097.url(options),
    method: 'get',
})

Controller2d371f6f9e64454dee18253e3d599097.definition = {
    methods: ["get","head"],
    url: '/host/management/logistic',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/logistic'
*/
Controller2d371f6f9e64454dee18253e3d599097.url = (options?: RouteQueryOptions) => {
    return Controller2d371f6f9e64454dee18253e3d599097.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/logistic'
*/
Controller2d371f6f9e64454dee18253e3d599097.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller2d371f6f9e64454dee18253e3d599097.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/logistic'
*/
Controller2d371f6f9e64454dee18253e3d599097.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller2d371f6f9e64454dee18253e3d599097.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/logistic'
*/
const Controller2d371f6f9e64454dee18253e3d599097Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2d371f6f9e64454dee18253e3d599097.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/logistic'
*/
Controller2d371f6f9e64454dee18253e3d599097Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2d371f6f9e64454dee18253e3d599097.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/logistic'
*/
Controller2d371f6f9e64454dee18253e3d599097Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2d371f6f9e64454dee18253e3d599097.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller2d371f6f9e64454dee18253e3d599097.form = Controller2d371f6f9e64454dee18253e3d599097Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/schedule'
*/
const Controllerd831da3b6902bfc2cf40f5f6662bd0ad = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerd831da3b6902bfc2cf40f5f6662bd0ad.url(options),
    method: 'get',
})

Controllerd831da3b6902bfc2cf40f5f6662bd0ad.definition = {
    methods: ["get","head"],
    url: '/host/management/schedule',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/schedule'
*/
Controllerd831da3b6902bfc2cf40f5f6662bd0ad.url = (options?: RouteQueryOptions) => {
    return Controllerd831da3b6902bfc2cf40f5f6662bd0ad.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/schedule'
*/
Controllerd831da3b6902bfc2cf40f5f6662bd0ad.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerd831da3b6902bfc2cf40f5f6662bd0ad.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/schedule'
*/
Controllerd831da3b6902bfc2cf40f5f6662bd0ad.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerd831da3b6902bfc2cf40f5f6662bd0ad.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/schedule'
*/
const Controllerd831da3b6902bfc2cf40f5f6662bd0adForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerd831da3b6902bfc2cf40f5f6662bd0ad.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/schedule'
*/
Controllerd831da3b6902bfc2cf40f5f6662bd0adForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerd831da3b6902bfc2cf40f5f6662bd0ad.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/management/schedule'
*/
Controllerd831da3b6902bfc2cf40f5f6662bd0adForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerd831da3b6902bfc2cf40f5f6662bd0ad.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerd831da3b6902bfc2cf40f5f6662bd0ad.form = Controllerd831da3b6902bfc2cf40f5f6662bd0adForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/contacts'
*/
const Controllera6c3ca41d272d09cce3cbe8966b34543 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllera6c3ca41d272d09cce3cbe8966b34543.url(options),
    method: 'get',
})

Controllera6c3ca41d272d09cce3cbe8966b34543.definition = {
    methods: ["get","head"],
    url: '/host/contacts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/contacts'
*/
Controllera6c3ca41d272d09cce3cbe8966b34543.url = (options?: RouteQueryOptions) => {
    return Controllera6c3ca41d272d09cce3cbe8966b34543.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/contacts'
*/
Controllera6c3ca41d272d09cce3cbe8966b34543.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllera6c3ca41d272d09cce3cbe8966b34543.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/contacts'
*/
Controllera6c3ca41d272d09cce3cbe8966b34543.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllera6c3ca41d272d09cce3cbe8966b34543.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/contacts'
*/
const Controllera6c3ca41d272d09cce3cbe8966b34543Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllera6c3ca41d272d09cce3cbe8966b34543.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/contacts'
*/
Controllera6c3ca41d272d09cce3cbe8966b34543Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllera6c3ca41d272d09cce3cbe8966b34543.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/contacts'
*/
Controllera6c3ca41d272d09cce3cbe8966b34543Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllera6c3ca41d272d09cce3cbe8966b34543.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllera6c3ca41d272d09cce3cbe8966b34543.form = Controllera6c3ca41d272d09cce3cbe8966b34543Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/repository'
*/
const Controllerba1909dfdafda6f1e6b784abcba7e183 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerba1909dfdafda6f1e6b784abcba7e183.url(options),
    method: 'get',
})

Controllerba1909dfdafda6f1e6b784abcba7e183.definition = {
    methods: ["get","head"],
    url: '/host/repository',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/repository'
*/
Controllerba1909dfdafda6f1e6b784abcba7e183.url = (options?: RouteQueryOptions) => {
    return Controllerba1909dfdafda6f1e6b784abcba7e183.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/repository'
*/
Controllerba1909dfdafda6f1e6b784abcba7e183.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerba1909dfdafda6f1e6b784abcba7e183.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/repository'
*/
Controllerba1909dfdafda6f1e6b784abcba7e183.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerba1909dfdafda6f1e6b784abcba7e183.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/repository'
*/
const Controllerba1909dfdafda6f1e6b784abcba7e183Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerba1909dfdafda6f1e6b784abcba7e183.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/repository'
*/
Controllerba1909dfdafda6f1e6b784abcba7e183Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerba1909dfdafda6f1e6b784abcba7e183.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/repository'
*/
Controllerba1909dfdafda6f1e6b784abcba7e183Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerba1909dfdafda6f1e6b784abcba7e183.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerba1909dfdafda6f1e6b784abcba7e183.form = Controllerba1909dfdafda6f1e6b784abcba7e183Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/voting'
*/
const Controllerbecb95338421b9ab60c21204580b36b5 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerbecb95338421b9ab60c21204580b36b5.url(options),
    method: 'get',
})

Controllerbecb95338421b9ab60c21204580b36b5.definition = {
    methods: ["get","head"],
    url: '/host/voting',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/voting'
*/
Controllerbecb95338421b9ab60c21204580b36b5.url = (options?: RouteQueryOptions) => {
    return Controllerbecb95338421b9ab60c21204580b36b5.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/voting'
*/
Controllerbecb95338421b9ab60c21204580b36b5.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerbecb95338421b9ab60c21204580b36b5.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/voting'
*/
Controllerbecb95338421b9ab60c21204580b36b5.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerbecb95338421b9ab60c21204580b36b5.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/voting'
*/
const Controllerbecb95338421b9ab60c21204580b36b5Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerbecb95338421b9ab60c21204580b36b5.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/voting'
*/
Controllerbecb95338421b9ab60c21204580b36b5Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerbecb95338421b9ab60c21204580b36b5.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/host/voting'
*/
Controllerbecb95338421b9ab60c21204580b36b5Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerbecb95338421b9ab60c21204580b36b5.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerbecb95338421b9ab60c21204580b36b5.form = Controllerbecb95338421b9ab60c21204580b36b5Form

/**
* Multiple routes resolve to \Inertia\Controller::Controller, so this export is a
* dictionary keyed by URI rather than a callable. Call a specific route with `Controller['<uri>'](...)`,
* or import the route by name from your generated `routes/` directory.
*/
const Controller = {
    '/host/dashboard': Controller518f53198343699ca0810f3ed7566e3f,
    '/host/finance': Controllerae6a23dab1f33d2b4933cdf047368094,
    '/host/logbook': Controller2f87723b7b6d46b903ec42cbf539764f,
    '/host/management/inventory': Controller717ff98c45c5e08937f5b1ff0a43c8bb,
    '/host/management/logistic': Controller2d371f6f9e64454dee18253e3d599097,
    '/host/management/schedule': Controllerd831da3b6902bfc2cf40f5f6662bd0ad,
    '/host/contacts': Controllera6c3ca41d272d09cce3cbe8966b34543,
    '/host/repository': Controllerba1909dfdafda6f1e6b784abcba7e183,
    '/host/voting': Controllerbecb95338421b9ab60c21204580b36b5,
}

export default Controller