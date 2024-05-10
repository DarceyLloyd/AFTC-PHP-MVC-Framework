<?php

namespace AFTC\VOs;

class ModelQueryVo
{
    public bool $success;
    public int $rows = 0;
    public int $insertId = 0;
    public array $result = [];
}