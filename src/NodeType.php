<?php

namespace Dynart\Press;

use Dynart\Micro\App;
use Dynart\Micro\Entities\EntityManager;

use Dynart\Press\Entity\Node;
use Dynart\Press\Service\EventService;
use Dynart\Press\Service\NowProvider;
use Dynart\Press\Service\UserService;

abstract class NodeType {

    protected $id;

    /** @var EventService */
    protected $events;

    /** @var EntityManager */
    protected $em;

    /** @var UserService */
    protected $users;

    /** @var NowProvider */
    protected $nowProvider;

    public function __construct() {
        $app = App::instance();
        $this->events = $app->get(EventService::class);
        $this->em = $app->get(EntityManager::class);
        $this->users = $app->get(UserService::class);
        $this->nowProvider = $app->get(NowProvider::class);
    }

    public function beforeSave(bool $isNew): void {
        $currentUserId = $this->users->current()->id;
        $now = $this->nowProvider->now();
        if ($isNew) {
            $node = new Node();
            $node->type = $this->em->tableNameByClass(get_class($this));
            $node->created_by = $currentUserId;
            $node->created_at = $now;
            $this->saveNode($node, true);
            $this->id = $node->id;
        } else {
            $node = $this->em->findById(Node::class, $this->id);
            $node->updated_by = $currentUserId;
            $node->updated_at = $now;
            $this->saveNode($node, false);
        }
    }

    protected function saveNode(Node $node, bool $isNew) {
        $this->events->emit(Node::EVENT_BEFORE_SAVE, [$node, $isNew]);
        $this->em->save($node, false);
        $this->events->emit(Node::EVENT_AFTER_SAVE, [$node, $isNew]);
    }
}