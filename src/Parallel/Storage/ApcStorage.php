<?php
/**
 * This file is part of RedisClient.
 * git: https://github.com/cheprasov/php-parallel
 *
 * (C) Alexander Cheprasov <cheprasov.84@ya.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Parallel\Storage;

class ApcStorage implements StorageInterface {

    /**
     * @param string $key
     * @param string $field
     * @return string
     */
    protected function getKeyByField($key, $field) {
        return $key.':'.$field;
    }

    /**
     * @inheritdoc
     */
    public function setup() {
    }

    /**
     * @inheritdoc
     */
    public function set($key, $field, $value, $expire = 0) {
        return apcu_store($this->getKeyByField($key, $field), $value, $expire ?: null);
    }

    /**
     * @inheritdoc
     */
    public function get($key, $fields) {
        if (is_string($fields)) {
            return apcu_fetch($this->getKeyByField($key, $fields));
        }
        $result = array_map(function($field) use ($key) {
            return apcu_fetch($this->getKeyByField($key, $field));
        }, $fields);
        return array_combine($fields, array_values($result));
    }

    /**
     * @inheritdoc
     */
    public function del($key, $fields) {
        if (is_string($fields)) {
            apcu_delete($this->getKeyByField($key, $fields));
        }
        return array_map(function($field) use ($key) {
            return apcu_delete($this->getKeyByField($key, $field));
        }, $fields);
    }

}
