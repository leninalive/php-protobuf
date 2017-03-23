<?php

abstract class ProtobufMessage
{
    const PB_TYPE_DOUBLE     = 1;
    const PB_TYPE_FIXED32    = 2;
    const PB_TYPE_FIXED64    = 3;
    const PB_TYPE_FLOAT      = 4;
    const PB_TYPE_INT        = 5;
    const PB_TYPE_SIGNED_INT = 6;
    const PB_TYPE_STRING     = 7;
    const PB_TYPE_BOOL       = 8;

    protected static $fields = array();
    protected $values = array();

    /**
     * @return null
     */
    abstract public function reset();

    /**
     * @param int   $position
     * @param mixed $value
     * 
     * @return null
     */
    public function append($position, $value) {
        if (!array_key_exists($position, $this->values) || !is_array($this->values[$position])) {
            $this->values[$position] = array();
        }

        $this->values[$position][] = $value;
        return $this;
    }

    /**
     * @param int $position
     * 
     * @return $this
     */
    public function clear($position) {
        $this->values[$position] = array();
        return $this;
    }

    /**
     * @param bool $onlySet
     * 
     * @return null
     */
    public function dump($onlySet = true) {}

    /**
     *
     * @return null
     */
    public function printDebugString() {}

    /**
     * @param int $position
     * 
     * @return int
     */
    public function count($position) {
        if (array_key_exists($position, $this->values) && is_array($this->values[$position])) {
            return count($this->values[$position]);
        }

        return 0;
    }

    /**
     * @param int      $position
     * @param int|null $offset
     *
     * @return mixed
     */
    public function get($position = -1, $offset = null) {
        $result = null;

        if (array_key_exists($position, $this->values)) {
            $result = $this->values[$position];
        }

        if (null === $result && array_key_exists($position, self::$fields)) {
            $field = self::$fields[$position];
            if (array_key_exists('default', $field)) {
                $result = $field['default'];
            }
        }

        if (null !== $result && null !== $offset && is_array($result)) {
            $result = $result[$offset];
        }

        return $result;

    }

    /**
     * @param string $packed
     * 
     * @throws Exception
     * 
     * @return mixed
     */
    public function parseFromString($packed) {
        $this->values = unserialize($packed, [
            'allowed_classes' => TRUE
        ]);
    }

    /**
     * @throws Exception
     *
     * @return string
     */
    public function serializeToString() {
        return serialize($this->values);
    }

    /**
     * @param int   $position
     * @param mixed $value
     * 
     * @return $this
     */
    public function set($position = -1, $value) {
        $this->values[$position] = $value;
        return $this;
    }
}
