<?php

namespace Dynart\Press\Admin;

use Dynart\Micro\View;

class DashboardController {

    /** @var View */
    private $view;

    public function __construct(View $view) {
        $this->view = $view;
    }

    /**
     * Index page for the admin
     *
     * @require view_dashboard  // TODO
     * @route GET /
     * @return string
     */
    public function index() {
        return $this->view->fetch('admin:index');
    }

    /**
     * REST API test endpoint
     *
     * @route GET /api/test/?
     * @param string Name
     * @return array
     */
    public function test($name) {
        return ['hello' => $name];
    }

}
