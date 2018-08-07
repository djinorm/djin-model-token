<?php
/**
 * Created for djin-model-token.
 * Datetime: 16.05.2018 13:27
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace DjinORM\Models\Token;


class TokenClass extends Token
{

    public static function getModelName(): string
    {
        return 'token';
    }
}