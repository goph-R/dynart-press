const AdminApp = function() {

    const router = new Router();
    const menu = new Menu($('.admin-nav-bar ul'), router);

    this.init = function(options) {
        menu.addAll(options.menuItems || []);
        menu.init();

        router.addAll(options.routes || {});
        router.callFromUrl();
    }

    this.showPage = function(name) {
        $('.admin-page').hide();
        $('.admin-page-' + name).show();
    };

    return this;
};

