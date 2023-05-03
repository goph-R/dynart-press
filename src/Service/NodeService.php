<?php

namespace Dynart\Press\Service;

use Dynart\Micro\App;
use Dynart\Micro\Database;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\Entity;

use Dynart\Press\Entity\Node;
use Dynart\Press\Entity\NodeEntity;

class NodeService {

    /** @var Database */
    protected $db;

    /** @var EventService */
    protected $eventService;

    /** @var EntityManager */
    protected $entityManager;

    /** @var UserService */
    protected $userService;

    /** @var NowProvider */
    protected $nowProvider;

    public function __construct(Database $db, EventService $eventService, EntityManager $entityManager, NowProvider $nowProvider) {
        $this->db = $db;
        $this->eventService = $eventService;
        $this->entityManager = $entityManager;
        $this->nowProvider = $nowProvider;
    }

    public function postConstruct() {
        $this->userService = App::instance()->get(UserService::class);
    }

    public function save(NodeEntity $entity) {
        $currentUserId = $this->userService->current()->id;
        $now = $this->nowProvider->now();
        if ($entity->isNew()) {
            $node = new Node();
            $node->type = $this->entityManager->createTableNameByClass(get_class($entity));
            $node->created_by = $currentUserId;
            $node->created_at = $now;
            $this->saveEntity('node', $node);
            $entity->id = $node->id;
        } else {
            $node = $this->entityManager->findById(Node::class, $entity->id);
            $node->updated_by = $currentUserId;
            $node->updated_at = $now;
            $this->saveEntity('node', $node);
        }
        $this->saveEntity($node->type, $entity);
    }

    protected function saveEntity(string $type, Entity $entity): void {
        $this->eventService->emit($type.':before_save', [$entity]);
        $this->entityManager->save($entity);
        $this->eventService->emit($type.':after_save', [$entity]);
    }
}