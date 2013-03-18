<?php

namespace AppTest\Model;

class News extends \Phalcon\Mvc\Model
{
    const PRIORITY_HIGH   = 'high';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_LOW    = 'low';

    public $id;

    public $title;

    public $date;

    public $priority;

    public $content;

    public static function getTopNews($limit)
    {
        return self::find([
            'priority = ?0',
            'bind'  => [self::PRIORITY_HIGH],
            'order' => 'date DESC',
            'limit' => (int) $limit
        ]);
    }
}
