<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Lib\Enumerations;

class JobStatus
{
    public static $Apply = 0;
    public static $SHORTLIST = 1;
    public static $REJECT = 2;
    public static $CALL_FOR_INTERVIEW = 3;
    public static $HIRE = 4;
}