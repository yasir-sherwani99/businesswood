<?php

namespace Corals\Foundation\Traits;

trait ModelUniqueCode
{
    /**
     * @param $codePrefix
     * @param string $codeColumn
     * @param bool $isSequential
     * @param int $codeLength
     * @return string
     */
    public static function getCode($codePrefix, $codeColumn = 'code', $isSequential = true, $codeLength = 6)
    {
        if ($isSequential) {
            // Get the last created order
            $lastRecord = self::query()->orderBy('id', 'desc')->first();

            // We get here if there is no records at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;

            if ($lastRecord) {
                $number = $lastRecord->id;
            }

            do {
                $code = $codePrefix . '-' . sprintf('%0' . $codeLength . 'd', intval($number) + 1);

                // Add the string in front and higher up the number.
                // the %06d part makes sure that there are always 6 numbers in the string.
                // so it adds the missing zero's when needed.
                $recordExists = self::query()->where($codeColumn, $code)->first();
            } while ($recordExists);

            return $code;
        } else {
            //RandomCode
            while (true) {
                $code = randomCode($codePrefix, $codeLength);
                if (!self::query()->where($codeColumn, $code)->first()) {
                    return $code;
                    break;
                }
            }
        }
    }

    public static function findByCode($code, $codeColumn = 'code')
    {
        return self::where($codeColumn, $code)->first();
    }
}
