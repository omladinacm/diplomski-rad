<?php


class FormSanitizer
{
    public static function sanitizeFormString($inputText): string
    {
        $inputText = strip_tags($inputText);
        $inputText = str_replace(" ", "", $inputText);
        $inputText = strtolower($inputText);
        return ucfirst($inputText);
    }

    public static function sanitizeFormUsername($inputText): array|string
    {
        $inputText = strip_tags($inputText);
        return str_replace(" ", "", $inputText);
    }

    public static function sanitizeFormPassword($inputText): string
    {
        return strip_tags($inputText);
    }

    public static function sanitizeFormEmail($inputText): array|string
    {
        $inputText = strip_tags($inputText);
        return str_replace(" ", "", $inputText);
    }
}