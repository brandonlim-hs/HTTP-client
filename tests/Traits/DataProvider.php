<?php

namespace HttpClient\Tests\Traits;

/**
 * Trait to convert data into valid data provider array.
 *
 * @package HttpClient\Tests\Traits
 */
trait DataProvider
{
    /**
     * Return a data provider array converted from the given array.
     *
     * @param array $data The array to be converted to valid data provider format.
     * @param bool $useOriginalKey Flag whether to use the original keys.
     * @return array Return a data provider array.
     */
    private function toDataProviderArray(array $data, bool $useOriginalKey = false)
    {
        $dataProviderArray = [];
        foreach ($data as $key => $value) {
            $key = $useOriginalKey ? $key : json_encode($value);
            $dataProviderArray[$key] = [$value];
        }
        return $dataProviderArray;
    }
}