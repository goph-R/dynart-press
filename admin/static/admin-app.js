import { Menu } from './menu.js';
import { Router } from './router.js';

$.fn.adminApp = function() {

    const router = new Router();
    const menu = new Menu($('.admin-nav-bar ul'), router);

    this.init = function(options) {
        menu.addAll(options.menuItems || []);
        menu.init();

        router.addAll(options.routes || {});
        router.callFromUrl();
    };

    this.showPage = function(name) {

        // TODO: use a Page class with init/show/hide

        $('.admin-page').hide();
        $('.admin-page-' + name).show();
    };

    return this;
};

