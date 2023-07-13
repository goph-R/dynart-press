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
        items.forEach(function (item) {
            menu.createItem($menu, item);
        });
    };

    this.createItem = function($parent, item) {
        const $item = $('<li>');
        const $link = $('<a>');
        const $icon = $('<i>');
        const $text = $('<span>');

        $item.addClass('admin-menu-item');
        $item.attr('id', 'admin_menu_' + item.id);
        $text.text(item.label);
        if (item.icon) {
            $icon.addClass(item.icon);
            $link.append($icon);
        }
        $link.append($text);
        $item.append($link);
        $parent.append($item);

        if (item.children) {
            const $subMenuList = $('<ul>');
            if (item.children) {
                item.children.forEach(function (itemChild) {
                    menu.createItem($subMenuList, itemChild);
                });
            }
            $item.append($subMenuList);
            $link.click(function () {
                menu.open(item.id);
            });
        } else {
            $link.click(function () {
                router.call(item.route);
            });
        }

    }

};

export { Menu }