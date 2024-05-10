<?php

namespace AFTC\Enums;

enum eQueryMode: string
{
    case FETCH = "fetch";
    case FETCHALL = "fetchall";
    case EXECUTE = "execute";
}