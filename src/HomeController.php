<?php

namespace Dynart\Press;

use Dynart\Micro\Request;
use Dynart\Micro\View;
use Dynart\Micro\Config;

class HomeController {

    /** @var Request */
    private $request;

    /** @var ImageService */
    private $imageService;

    /** @var View */
    private $view;

    private $mediaDir;

    public function __construct(Config $config, Request $request, View $view, ImageService $imageService) {
        $this->request = $request;
        $this->imageService = $imageService;
        $this->view = $view;
        $this->mediaDir = $config->get('photos.media_dir');
    }

    public function index() {
        $dir = $this->request->get('dir');

        $this->imageService->init($dir);
        $this->imageService->sync();

        $images = $this->imageService->findImages();
        $columns = [[], [], []];
        $heights = [0, 0, 0];
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
            'mediaDir' => $this->mediaDir
        ]);
    }
}