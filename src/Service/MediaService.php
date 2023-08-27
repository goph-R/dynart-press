<?php

namespace Dynart\Press\Service;

use Dynart\Micro\Config;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\Query;
use Dynart\Micro\Entities\QueryExecutor;
use Dynart\Micro\MicroException;
use Dynart\Press\Entity\Image;

class MediaService {

    /** @var Config */
    private $config;

    /** @var EntityManager */
    private $em;

    /** @var QueryExecutor */
    private $queryExecutor;

    private $fullDirPath;
    private $dir;

    public function __construct(Config $config, EntityManager $em, QueryExecutor $qe) {
        $this->config = $config;
        $this->em = $em;
        $this->queryExecutor = $qe;
    }

    public function init(string $dir) {
        $this->dir = $dir;
        $this->fullDirPath = $this->config->get('photos.media_dir').'/'.$dir;
        if (!file_exists($this->fullDirPath)) {
            throw new MicroException("Directory doesn't exist: $dir");
        }
    }

    public function sync() {

        $query = new Query(Image::class);
        $query->addCondition('dir = :dir', [':dir' => $this->dir]);

        $images = $this->queryExecutor->findAll($query);

        // remove deleted images
        foreach ($images as $image) {
            $fullPath = $this->fullDirPath.'/'.$image['path'];
            if (!file_exists($fullPath)) {
                $this->em->deleteById(Image::class, $image['id']);
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
                $this->em->update(Image::class, [
                    'updated_at' => date($dateFormat),
                    'path_updated_at' => $pathUpdatedAt,
                    'width' => $imageSize[0],
                    'height' => $imageSize[1]
                ],
                    'id = :id', ['id' => $updateId]
                );
            } else {
                $this->em->insert(Image::class, [
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
        $query = new Query(Image::class);
        $query->addCondition('dir = :dir', [':dir' => $this->dir]);
        $query->addOrderBy('created_at', 'desc');
        return $this->queryExecutor->findAll($query);
    }
}
