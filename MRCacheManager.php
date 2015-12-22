<?php

/**
 * @author Vitalii Edifanov
 * @copyright (c) 2015, Vitalii Edifanov
 */

namespace mediarise;

use Yii;
use yii\redis\Cache;

class MRCacheManager extends Cache {

    // ключ по которому хранится префикс текущих ключей кэша
    private $cacheId;
    // суффикс определяющий принадлежность данных
    private $suffix = '_data';
    // параметр определяющий тип кэша
    public $isSessionCache = false;
    // параметр определяющий время жизни кэша
    public $lifeTime = 0;

    /**
     * Инициализация, определение какой кэш будет использоваться.
     */
    public function init() {
        parent::init();

        if ($this->isSessionCache) {
            $this->suffix = '_session';
        }
        // ключ в котором хранится текущее значение суффикса кэша
        $this->cacheId = $this->keyPrefix . $this->suffix;

        $isFlush = Yii::$app->request->get('flush-cache');
        
        var_dump($isFlush);
        
        if ($isFlush !== NULL) {
            $this->flush();
        }
    }

    public function set($key, $value, $expire = 0, $dependency = NULL) {
        $expire = ($expire == 0) ? 0 : $this->lifeTime;
        $currentCacheId = $this->_getCurrentCacheId();
        return parent::set($key . '_' . $currentCacheId . $this->suffix, $value, $expire, $dependency);
    }

    public function get($key) {
        $currentCacheId = $this->_getCurrentCacheId();
        return parent::get($key . '_' . $currentCacheId . $this->suffix);
    }

    /**
     * Сброс кеша - увеличение счётчика на 1
     */
    public function flush() {

        $currentCacheId = $this->_getCurrentCacheId();

        $return = parent::set($this->cacheId, $currentCacheId + 1);
        if ($return !== false) {
            Yii::warning('Flushing ' . $this->suffix . ' cache', 'info', get_class($this));
        }
        return $return;
    }

    /**
     * Возвращает текущее значение суффиксов ключей кэша
     * @return int
     */
    private function _getCurrentCacheId() {

        $currentCacheId = parent::get($this->cacheId);

        if ($currentCacheId === false) {
            $currentCacheId = 0;
            parent::set($this->cacheId, $currentCacheId);
        }
        return $currentCacheId;
    }

}
