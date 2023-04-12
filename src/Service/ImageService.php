<?php

namespace Dynart\Press\Service;

use Dynart\Micro\AppException;
use Dynart\Micro\Config;

class ImageService {

    /** @var Config */
    private $config;

    /** @var ImageRepository */
    private $repository;

    private $fullDirPath;
    private $dir;

    public function __construct(Config $config, ImageRepository $repository) {
        $this->config = $config;
        $this->repository = $repository;
    }

    public function init(string $dir) {
        $this->dir = $dir;
        $this->fullDirPath = $this->config->get('photos.media_dir').'/'.$dir;
        if (!file_exists($this->fullDirPath)) {
            throw new AppException("Directory doesn't exist: $dir");
        }
    }

    public function sync() {

        $images = $this->repository->findAll(null, ['dir' => $this->dir]);

        // remove deleted images
        foreach ($images as $image) {
            $fullPath = $this->fullDirPath.'/'.$image['path'];
            if (!file_exists($fullPath)) {
                $this->repository->deleteById($image['id']);
            }
        }

        // go through the given directory
        $d = dir($this->fullDirPath);
        while (false !== ($entry = $d->read())) {

            // search for images
            $fullPath = $this->fullDirPath.'/'.$entry;
            if (is_dir($fullPath)) {
                continue;
            }
            $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                continue;
            }

            $dateFormat = 'Y-m-h H:i:s';
            $pathUpdatedAt = date($dateFormat, filemtime($fullPath));

            // check if the image exists in db and if the file wasn't updated then skip it
            $skip = false;
            $updateId = null;
            foreach ($images as $image) {
                if ($image['path'] == $entry) {
                    $skip = $image['path_updated_at'] == $pathUpdatedAt;
                    $updateId = $image['id'];
                    break;
                }
            }
            if ($skip) {
                continue;
            }

            // skip if it's not an image
            $imageSize = getimagesize($fullPath);
            if (!$imageSize) {
                continue;
            }

            // create or update image
            if ($updateId) {
                $this->repository->update([
                    'updated_at' => date($dateFormat),
                    'path_updated_at' => $pathUpdatedAt,
                    'width' => $imageSize[0],
                    'height' => $imageSize[1]
                ],
                    'id = :id', ['id' => $updateId]
                );
            } else {
                $this->repository->insert([
                    'dir' => $this->dir,
                    'path' => $entry,
                    'path_updated_at' => $pathUpdatedAt,
                    'width' => $imageSize[0],
                    'height' => $imageSize[1],
                    'title' => ''
                ]);
            }

        }
        $d->close();
    }

    public function findImages() {
        return $this->repository->findAll(null, [
            'dir' => $this->dir,
            'order_by' => 'id',
            'order_dir' => 'desc',
            'with_tags' => true
        ]);
    }
}
