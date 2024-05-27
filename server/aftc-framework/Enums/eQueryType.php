<?php

namespace AFTC\Enums;

enum eQueryType: string
{
    case FETCH = "fetch";
    case FETCHALL = "fetchall";
    case EXECUTE = "execute";
}