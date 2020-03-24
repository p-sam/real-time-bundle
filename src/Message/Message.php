<?php

namespace SP\RealTimeBundle\Message;

class Message implements \JsonSerializable
{
    /**
     * @var mixed actually scalar|array|\JsonSerializable
     */
    private $data;

    public function __construct(&$data)
    {
        if (!is_scalar($data) && !is_array($data) && !($data instanceof \JsonSerializable)) {
            throw new \InvalidArgumentException('$data must be either a scalar, an array or implement \\JsonSerializable');
        }

        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        if ($this->data instanceof \JsonSerializable) {
            return $this->data->jsonSerialize();
        }

        return $this->data;
    }
}
