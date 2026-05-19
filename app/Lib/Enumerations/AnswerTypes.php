<?php

namespace App\Lib\Enumerations;

class AnswerTypes
{
    public const SINGLE_CHOICE = 1;
    public const MULTIPLE_CHOICE = 2;
    public const TEXT = 3; // short answer
    public const TEXTAREA = 4; // long answers
    public const DROPDOWN = 5;
    public const NUMBER = 6;
    public const DATEPICKER = 7;
    public const RATING_SCALE = 8;
    public const LIKERT_SCALE = 9;
    public const FILE_UPLOAD = 10;
    public const YES_NO = 11;

    public static function toArray(): array
    {
        return [
            self::SINGLE_CHOICE => 'Single Choice',
            self::MULTIPLE_CHOICE => 'Multiple Choices',
            self::TEXT => 'Short Answer',
            self::TEXTAREA => 'Long Answer',
            self::DROPDOWN => 'Dropdown Answer',
            self::NUMBER => 'Number',
            self::DATEPICKER => 'Datepicker',
            self::RATING_SCALE => 'Rating Scale',
            self::LIKERT_SCALE => 'Likert Scale',
            self::FILE_UPLOAD => 'Fileupload',
            self::YES_NO => 'Yes/No Answers',
        ];
    }
    public static function getName($value): string
    {
        switch ($value) {
            case self::SINGLE_CHOICE:
                return 'Single Choice';
            case self::MULTIPLE_CHOICE:
                return 'Multiple Choices';
            case self::TEXT:
                return 'Short Answer';
            case self::TEXTAREA:
                return 'Long Answer';
            case self::DROPDOWN:
                return 'Dropdown Answer';
            case self::NUMBER:
                return 'Number';
            case self::DATEPICKER:
                return 'Datepicker';
            case self::RATING_SCALE:
                return 'Rating Scale';
            case self::LIKERT_SCALE:
                return 'Likert Scale';
            case self::FILE_UPLOAD:
                return 'Fileupload';
            case self::YES_NO:
                return 'Yes/No Answers';
            default:
                return 'UNKNOWN';
        }
    }
}