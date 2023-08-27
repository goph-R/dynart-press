<?php

namespace Dynart\Press;

use Dynart\Micro\Micro;
use Dynart\Micro\Form;
use Dynart\Micro\Translation;
use Dynart\Micro\EventService;

use Dynart\Micro\Entities\Entity;
use Dynart\Micro\Entities\Database;
use Dynart\Micro\Entities\Query;


class ColumnView {

    const VIEW_DEFAULT = 'ColumnView::viewDefault';

    private $label = '';
    private $width = '';
    private $align = 'left';
    private $view = self::VIEW_DEFAULT;

    public function __construct(array $options) {
        if (array_key_exists('label', $options)) {
            $this->label = $options['label'];
        }
        if (array_key_exists('width', $options)) {
            $this->width = $options['width'];
        }
        if (array_key_exists('align', $options)) {
            $this->align = $options['align'];
        }
        if (array_key_exists('view', $options)) {
            $this->view = $options['view'];
        }
    }

    public function label() {
        return $this->label;
    }

    public function width() {
        return $this->width;
    }

    public function align() {
        return $this->align;
    }

    public function view() {
        return $this->view;
    }

    public static function viewDefault(string $name, array $record) {
        return $record[$name];
    }
}

class ColumnViews {

    private $columnViews = [];

    public function add(array $allParams) {
        foreach ($allParams as $name => $params) {
            $this->columnViews[$name] = new ColumnView($params);
        }
    }

    public function all() {
        return $this->columnViews;
    }
}

abstract class ListService {

    const EVENT_COLUMN_VIEWS_CREATED = 'column_views_created';
    const EVENT_FORM_CREATED = 'form_created';
    const EVENT_QUERY_CREATED = 'query_created';

    /** @var Database */
    protected $db;

    /** @var Translation */
    protected $tr;

    /** @var EventService */
    protected $events;

    /** @var Form */
    protected $form;

    /** @var Query */
    protected $query;

    /** @var ColumnViews */
    protected $columnViews;

    abstract public function createForm(): Form;
    abstract public function createQuery(Form $form): Query;
    abstract public function createColumnViews(): ColumnViews;

    public function __construct(Database $db, Translation $tr, EventService $events) {
        $this->db = $db;
        $this->tr = $tr;
        $this->events = $events;
    }

    public function create() {
        $this->fullCreateColumnViews();
        $this->fullCreateForm();
        $this->fullCreateQuery();
    }

    public function fullCreateColumnViews() {
        $this->columnViews = $this->createColumnViews();
        $this->events->emit($this->columnViewsCreatedEvent(), [$this->columnViews]);
    }

    public function fullCreateForm() {
        $this->form = $this->createForm();
        $this->events->emit($this->formCreatedEvent(), [$this->form]);
        $this->form->process('GET');
    }

    public function fullCreateQuery() {
        $this->query = $this->createQuery($this->form);
        $this->events->emit($this->queryCreatedEvent(), [$this->form, $this->query]);
    }

    public function formCreatedEvent(): string {
        return get_class($this).':'.self::EVENT_FORM_CREATED;
    }

    public function queryCreatedEvent(): string {
        return get_class($this).':'.self::EVENT_QUERY_CREATED;
    }

    public function columnViewsCreatedEvent(): string {
        return get_class($this).':'.self::EVENT_COLUMN_VIEWS_CREATED;
    }

    public function query(): Query {
        return $this->query;
    }

    public function form(): Form {
        return $this->form;
    }

    public function columnViews(): ColumnViews {
        return $this->columnViews;
    }
}

// --------------

class Person extends Entity {
    public $id;
    public $first_name;
    public $last_name;
    public $age;
}

class Person_Text extends Entity {
    public $text_id;
    public $locale;
    public $translated;
}

class PersonListService extends ListService {

    const TEXT_LAST_NAME = 'press:person.last_name';
    const TEXT_FIRST_NAME = 'press:person.first_name';
    const TEXT_AGE = 'press:person.age';
    const TEXT_SEARCH = 'press:person_list.search';
    const TEXT_MIN_AGE = 'press:person_list.min_age';
    const TEXT_MAX_AGE = 'press:person_list.max_age';

    public function createColumnViews(): ColumnViews {
        $columnViews = Micro::create(ColumnViews::class);
        $columnViews->add([
            'last_name' => [
                'label' => $this->tr->get(self::TEXT_LAST_NAME),
                'width' => '40%'
            ],
            'first_name' => [
                'label' => $this->tr->get(self::TEXT_FIRST_NAME),
                'width' => '40%'
            ],
            'age' => [
                'label' => $this->tr->get(self::TEXT_AGE),
                'width' => '20%',
                'align' => 'right'
            ]
        ]);
        return $columnViews;
    }

    public function createForm(): Form {
        $form = Micro::create(Form::class, ['', false]);
        $form->addFields([
            'min_age' => [
                'label' => $this->tr->get(self::TEXT_MIN_AGE),
                'type' => 'integer'
            ],
            'max_age' => [
                'label' => $this->tr->get(self::TEXT_MAX_AGE),
                'type' => 'integer'
            ],
            'text' => [
                'label' => $this->tr->get(self::TEXT_SEARCH),
                'type' => 'text'
            ]
        ]);
        return $form;
    }

    public function createQuery(Form $form): Query {
        /** @var Query $query */
        $query = Micro::create(Query::class, [Person::class]);
        $query->addFields([
            'first_name' => '#Person.first_name',
            'last_name' => '#Person.last_name',
            'age' => '#Person.age',
            'translated' => '#Person_Text.translated'
        ]);
        $query->addInnerJoin(Person_Text::class,
            '#Person_Text.text_id = #Person.id and #Person_Text.locale = :locale',
            [':locale' => $this->tr->locale()]
        );

        $minAge = (int)$form->value('min_age', 0);
        $maxAge = (int)$form->value('max_age', 99999);
        if ($minAge > $maxAge) {
            $minAge = $maxAge;
        }
        if ($minAge) {
            $query->addCondition('age >= :minAge', [':minAge' => $minAge]);
        }
        if ($maxAge) {
            $query->addCondition('age <= :maxAge', [':maxAge' => $maxAge]);
        }
        $text = $form->value('text');
        if ($text) {
            $like = '%'.$this->db->escapeLike($text).'%';
            $query->addCondition(
                'first_name like :firstName or last_name like :lastName or translated like :translated',
                [
                    ':firstName' => $like,
                    ':lastName' => $like,
                    ':translated' => $like
                ]
            );
        }
        return $query;
    }
}