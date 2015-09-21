<?php

namespace Model;

use Lib\Persistence;

class FavoriteSong extends Persistence
{
    protected $song;
    protected $userId;

    public static function create($userId, $songId)
    {
        return self::insert(['user_id' => $userId , 'song_id' => $songId]);
    }

    public static function remove($userId, $songId)
    {
        return self::delete(['user_id' => $userId , 'song_id' => $songId]);
    }

    protected function hydrate($raw)
    {
        $this->song = Song::loadById($raw['song_id']);
        $this->userId = (int) $raw['user_id'];
    }

    public static function loadByUser($id)
    {
        return self::loadByParameters(['user_id' => $id]);
    }

    static protected function getTableName()
    {
        return 'favorite_song';
    }

    function jsonSerialize()
    {
        return [
            'song_id' => $this->song->getId(),
            'title'   => $this->song->getTitle(),
        ];
    }
}
