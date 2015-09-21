<?php

namespace Model;

use Lib\Persistence;

class Song extends Persistence
{
    protected $id;
    protected $title;
    protected $duration;

    protected function hydrate($raw)
    {
        $this->id       = (int) $raw['id'];
        $this->title    = $raw['title'];
        $this->duration = (int) $raw['duration'];
    }

    static protected function getTableName()
    {
        return 'song';
    }

    function jsonSerialize()
    {
        return [
            'song_id'  => $this->id,
            'title'    => $this->title,
            'duration' => $this->duration,
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
