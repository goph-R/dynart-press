import { Menu } from './menu.js';
import { Router } from './router.js';
import { Page } from './page.js';

$.fn.adminApp = function() {

    const router = new Router();
    const menu = new Menu($('.admin-nav-bar ul'), router);
    const pages = {};
    const plugins = {}

    this.addPlugin = function(pluginName, options) {
        plugins[plugin.name()] = plugin
    };

    this.init = function(options) {
        menu.addAll(options.menuItems || []);
        menu.init();

        pages.addAll(options.pages || []);
        pages.init();

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

