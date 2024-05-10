<?php

namespace AFTC\VOs;

class ApiResponseVo
{
    public int $status;
    public string $message;
    public int $rows;
    public int $insert_id;
    public array $data;
}