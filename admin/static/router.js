const Router = function() {

    const router = this;

    let routes = {};

    this.addAll = function(newRoutes) {
        Object.assign(routes, newRoutes);
    };

    this.callFromUrl = function() {
        const route = window.location.hash.slice(1) || "/";
        router.call(route);
    };

    this.call = function(route) { // TODO: parameters (path, query)
        if (route in routes) {
            //history.replaceState(undefined, undefined, '#' + route);
            window.location.hash = route;
            routes[route]();
        } else {
            console.error('No route found: ' + route);
        }
    };

    window.addEventListener('hashchange', function() {
        router.callFromUrl();
    });

};
