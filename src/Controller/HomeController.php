<?php

namespace Dynart\Press\Controller;

use Dynart\Micro\Request;
use Dynart\Micro\View;
use Dynart\Micro\Config;

use Dynart\Press\Service\MediaService;

class HomeController {

    /** @var Request */
    private $request;

    /** @var MediaService */
    private $mediaService;

    /** @var View */
    private $view;

    private $mediaDir;

    public function __construct(Config $config, Request $request, View $view, MediaService $mediaService) {
        $this->request = $request;
        $this->mediaService = $mediaService;
        $this->view = $view;
        $this->mediaDir = $config->get('photos.media_dir');
    }

    /**
     * @route GET /
     */

    public function index() {
        $dir = $this->request->get('dir', '');
        $search = $this->request->get('search', '');

        $this->mediaService->init($dir);
        $this->mediaService->sync();

        $images = $this->mediaService->findImages();
        $columnCount = 5;
        $columns = [];
        $heights = [];
        for ($i = 0; $i < $columnCount; $i++) {
            $columns[] = [];
            $heights[] = 0;
        }
        foreach ($images as $image) {

            // search for the smallest height column
            $min = 9999999;
            $col = 0;
            foreach ($heights as $column => $height) {
                if ($height < $min) {
                    $col = $column;
                    $min = $height;
                }
            }

            // add the next image there
            $columns[$col][] = $image;
            $heights[$col] += $image['height'] / $image['width'];
        }

        return $this->view->fetch('index', [
            'imageColumns' => $columns,
            'dir' => $dir,
            'search' => $search,
            'mediaDir' => $this->mediaDir,
            'columnCount' => $columnCount
        ]);
    }

    /**
     * @route GET /test
     */
    public function test() {

    }
}