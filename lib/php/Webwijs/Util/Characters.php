<?php

namespace Webwijs\Util;

/**
 * The Character class is a utility class which contains
 * several methods for determining a character's category
 * and for converting unicode characters.
 *
 * This class is inspired by the Character class used within the 
 * Java language which is a wrapper class for a char primitive.
 *
 * @author Chris Harris
 * @version 0.0.1
 */
class Characters
{
    /**
     * The maximum value for a leading-surrogate.
     * 
     * @var int
     */
    const MAX_HIGH_SURROGATE = 0xDBFF;

    /**
     * The minimum value for a leading-surrogate.
     *
     * @var int
     */
    const MIN_HIGH_SURROGATE = 0xD800;
    
    /**
     * The maximum value for a trailing-surrogate.
     *
     * @var int
     */
    const MAX_LOW_SURROGATE = 0xDFFF;
    
    /**
     * The minimum value for a trailing-surrogate.
     *
     * @var int
     */
    const MIN_LOW_SURROGATE = 0xDC00;

    /**
     * The minimum value of a supplementary code point.
     *
     * @var int
     */
    const MIN_SUPPLEMENTARY_CODE_POINT = 0x010000;
    
    /**
     * The maximum value of a Unicode code point.
     *
     * @var int
     */
    const MAX_CODE_POINT = 0x10FFFF;
    
    /**
     * The miminum value of a Unicode code point.
     *
     * @var int
     */
    const MIN_CODE_POINT = 0x000000;

    /**
     * Returns a Unicode code point for the given surrogate-pair.  
     *
     * @param int $lead the leading-surrogate value.
     * @param int $trail the trailing-surrogate value.
     * @return int the code point composed from the specified surrogate pair.
     *
     * @link http://crocodillon.com/blog/parsing-emoji-unicode-in-javascript
     * @link http://docs.oracle.com/javase/7/docs/api/java/lang/Character.html#toCodePoint(char,%20char)
     */
    public static function toCodePoint($lead, $trail)
    {
        return (($lead - self::MIN_HIGH_SURROGATE) << 10) + 
                    ($trail - self::MIN_LOW_SURROGATE) + self::MIN_SUPPLEMENTARY_CODE_POINT;      
    }
    
    /**
     * Determines whether the specified character is Unicode high-surrogate code unit 
     * (also know as a leading-surrogate).
     *
     * @param int $char the character to be tested.
     * @return bool true if the character is between MIN_HIGH_SURROGATE and MAX_HIGH_SURROGATE inclusive,
     *              false otherwise.
     * @link http://en.wikipedia.org/wiki/Universal_Character_Set_characters#Surrogates
     */
    public static function isHighSurrogate($char)
    {
        return ($char >= self::MIN_HIGH_SURROGATE && $char <= self::MAX_HIGH_SURROGATE);
    }
    
    /**
     * Determines whether the specified character is Unicode low-surrogate code unit 
     * (also know as a trailing-surrogate).
     *
     * @param int $char the character to be tested.
     * @return bool true if the character is between MIN_LOW_SURROGATE and MAX_LOW_SURROGATE inclusive,
     *              false otherwise.
     * @link http://en.wikipedia.org/wiki/Universal_Character_Set_characters#Surrogates
     */
    public static function isLowSurrogate($char)
    {
        return ($char >= self::MIN_LOW_SURROGATE && $char <= self::MAX_LOW_SURROGATE);
    }
    
    /**
     * Determines whether the specified pair of characters is a valid surrogate pair.
     *
     * @param int $lead the leading-surrogate value to be tested.
     * @param int $trail the trailing-surrogate value to be tested.
     * @return bool true if the given specified pair of characters if a valid surrogate
     *                   pair, false otherwise.
     * @see Characters::isHighSurrogate($char)
     * @see Characters::isLowSurrogate($char)
     * @link http://en.wikipedia.org/wiki/Universal_Character_Set_characters#Surrogates
     */
    public static function isSurrogatePair($lead, $trail)
    {
        return (self::isHighSurrogate($lead) && self::isLowSurrogate($trail));
    }
    
    /**
     * Determines whether the specified code point is a valid Unicode code point within
     * the range of 0x0000 and 0x10FFFF inclusive.
     *
     * @param int $codePoint the code point to be tested.
     * @return bool true if the given code point is a valid Unicode code point,
     *                   false otherwise.
     * @link http://en.wikipedia.org/wiki/Code_point
     */
    public static function isValidCodePoint($codePoint) {
        return ($codePoint >= self::MIN_CODE_POINT && $codePoint <= self::MAX_CODE_POINT);
    }
}
