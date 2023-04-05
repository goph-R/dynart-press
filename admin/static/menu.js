const Menu = function($menu, router) {

    const menu = this;

    let items = [];

    this.open = function (id) {
        $('#admin_menu_' + id + ' ul').show();
    };

    this.addAll = function(newItems) { // TODO: before/after parameter
        items = items.concat(newItems);
    };

    this.init = function() {
        $menu.empty();
        console.log(items);
        items.forEach(function (item) {
            menu.createItem(item);
        });
    };

    this.createItem = function(item) {
        const $item = $('<li>');
        const $link = $('<a>');
        const $icon = $('<i>');
        const $text = $('<span>');
        const $subMenuList = $('<ul>');
        $item.addClass('admin-menu-item');
        $item.attr('id', 'admin_menu_' + item.id);
        if (item.children) {
            //adminApp.createMenu($subMenuList, menuItem.children, 'admin-sub-nav');
        }
        $text.text(item.label);
        if (item.icon) {
            $icon.addClass(item.icon);
            $link.append($icon);
        }
        if (item.children) {
            $link.click(function () {
                menu.open(item.id);
            });
        } else {
            $link.click(function () {
                router.call(item.route);
            });
        }
        $link.append($text);
        $item.append($link);
        if (item.children) {
            $menu.append($subMenuList);
        }
        $menu.append($item);
    }

};

export { Menu }