<?php

namespace AFTC\VOs;

use DateTime;

class JWTPayloadVo
{
    public object $payload;
    public bool $valid = false;
    public DateTime $iss; // issue time
    public DateTime $nbf; // not before time
    public DateTime $exp; // expiry time
    public DateTime $server_time;
}