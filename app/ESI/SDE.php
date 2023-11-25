<?php

declare(strict_types=1);

namespace App\ESI;

use Illuminate\Support\Facades\Storage;

final class SDE
{
    /**
     * @phpstan-return array<string, array>|array{name: array{en: string}, }
     */
    public static function types(?string $key = null): array
    {
        //TODO: Cache this
        //TODO: Use serde to deserialize this into and object
        //TODO: Or replace this with SQLite data set and query. OOM with this solution
        $types = once(fn () => yaml_parse_file(Storage::path('sde/fsd/typeIDs.yaml')));

        if ($key === null) {
            return $types;
        }

        return $types[$key];
    }
}
