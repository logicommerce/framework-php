<?php

namespace FWK\Core\Resources;

use SDK\Core\Resources\BatchRequests;

class BatchLocalResults {

    private static ?\WeakMap $results = null;

    public static function add(BatchRequests $batchRequests, string $batchName, mixed $result): void {
        $results = self::getResults();
        $batchResults = $results[$batchRequests] ?? [];
        $batchResults[$batchName] = $result;
        $results[$batchRequests] = $batchResults;
    }

    public static function pull(BatchRequests $batchRequests): array {
        $results = self::getResults();
        $batchResults = $results[$batchRequests] ?? [];
        unset($results[$batchRequests]);
        return $batchResults;
    }

    private static function getResults(): \WeakMap {
        if (is_null(self::$results)) {
            self::$results = new \WeakMap();
        }
        return self::$results;
    }
}
