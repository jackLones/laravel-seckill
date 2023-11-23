<?php

namespace App\Helpers;

class SnowflakeHelper
{
    const EPOCH = 1483228800000;
    private $datacenterId;
    private $workerId;
    private $sequence = 0;

    public function __construct($datacenterId, $workerId)
    {
        if ($datacenterId > 31 || $datacenterId < 0) {
            throw new \InvalidArgumentException("Invalid datacenter ID");
        }

        if ($workerId > 31 || $workerId < 0) {
            throw new \InvalidArgumentException("Invalid worker ID");
        }

        $this->datacenterId = $datacenterId;
        $this->workerId = $workerId;
    }

    public function nextId(): int
    {
        $timestamp = $this->timestamp();
        $sequence = $this->nextSequence();

        $id = (($timestamp - self::EPOCH) << 22) | ($this->datacenterId << 17) | ($this->workerId << 12) | $sequence;

        return $id;
    }

    private function timestamp(): float
    {
        return round(microtime(true) * 1000);
    }

    private function nextSequence(): int
    {
        $this->sequence = ($this->sequence + 1) & 4095;

        if ($this->sequence == 0) {
            usleep(1000);
        }

        return $this->sequence;
    }
}
