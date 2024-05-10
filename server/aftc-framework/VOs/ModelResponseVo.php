<?php

namespace AFTC\VOs;

class ModelResponseVo
{
    public int $status;
    public bool $success;
    public string $message;
    public int $rows;
    public int $insert_id;
    public array $data;
}