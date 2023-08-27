<?php

namespace Dynart\Press\Service;

use Dynart\Micro\App;
use Dynart\Micro\Entities\Database;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\Entity;

use Dynart\Micro\Micro;
use Dynart\Press\Entity\Node;
use Dynart\Press\Entity\NodeEntity;

class NodeService {

    /** @var Database */
    protected $db;

    /** @var EntityManager */
    protected $em;

    /** @var UserService */
    protected $userService;

    /** @var DateService */
    protected $dateService;

    public function __construct(Database $db, EntityManager $em, DateService $dateService) {
        $this->db = $db;
        $this->em = $em;
        $this->dateService = $dateService;
    }

    public function postConstruct() {
        $this->userService = Micro::get(UserService::class);
    }

    public function save(NodeEntity $entity) {
        $currentUserId = $this->userService->current()->id;
        $now = $this->dateService->now();
        if ($entity->isNew()) {
            $entity->id = $this->em->insert(Node::class, [
                'type' => get_class($entity),
                'created_by' => $currentUserId,
                'created_at' => $now
            ]);
        } else {
            $this->em->update(Node::class, [
                'updated_by' => $currentUserId,
                'updated_at' => $now
            ], 'id = :id', [':id' => $entity->id]);
        }
        $this->em->save($entity);
    }
}