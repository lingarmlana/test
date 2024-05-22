<?php

/**
 * @param array $districts
 * @return int
 */
function getBestDistrict(array $districts): int
{
    $left = $right = $lastObjectDistrictID = $bestDistrict = [];

    for ($i = 0; $i < count($districts); $i++) {
        foreach ($districts[$i] as $objectCode => $objectExists) {
            if ($objectExists) {
                $lastObjectDistrictID[$objectCode] = $i;
                $left[$i][$objectCode] = 0;
            } else {
                if (array_key_exists($objectCode, $lastObjectDistrictID)) {
                    $left[$i][$objectCode] = $i - $lastObjectDistrictID[$objectCode];
                } else {
                    $left[$i][$objectCode] = null;
                }
            }
        }
    }

    $lastObjectDistrictID = [];
    for ($i = count($districts) - 1; $i >= 0; $i--) {
        $currentDistance = [];

        foreach ($districts[$i] as $objectCode => $objectExists) {
            if ($objectExists) {
                $lastObjectDistrictID[$objectCode] = $i;
                $right[$i][$objectCode] = 0;
            } else {
                if (array_key_exists($objectCode, $lastObjectDistrictID)) {
                    $right[$i][$objectCode] = $lastObjectDistrictID[$objectCode] - $i;
                } else {
                    $right[$i][$objectCode] = $left[$i][$objectCode];
                }
            }

            $currentDistance[$objectCode] = getCurrentDistance($left[$i][$objectCode], $right[$i][$objectCode]);
        }

        $bestDistrict = updateBestDistrict($bestDistrict, $currentDistance, $i);
    }

    return $bestDistrict['id'];
}

/**
 * @param mixed $left
 * @param int $right
 * @return int
 */
function getCurrentDistance(mixed $left, int $right): int
{
    return ($left === null ? $right : $left) < $right ? $left : $right;
}

/**
 * @param array $bestDistrict
 * @param array $currentDistance
 * @param int $districtId
 * @return array
 */
function updateBestDistrict(array $bestDistrict, array $currentDistance, int $districtId): array
{
    if (empty($bestDistrict) ||
        max($currentDistance) < $bestDistrict['max_distance'] ||
        (max($currentDistance) == $bestDistrict['max_distance'] && array_sum($currentDistance) <= $bestDistrict['sum_distance'])
    ) {
        $bestDistrict['id'] = $districtId;
        $bestDistrict['max_distance'] = max($currentDistance);
        $bestDistrict['sum_distance'] = array_sum($currentDistance);
    }

    return $bestDistrict;
}

