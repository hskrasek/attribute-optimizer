<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\Group
 *
 * @property int $groupID
 * @property int|null $categoryID
 * @property string|null $groupName
 * @property int|null $iconID
 * @property int|null $useBasePrice
 * @property int|null $anchored
 * @property int|null $anchorable
 * @property int|null $fittableNonSingleton
 * @property int|null $published
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereAnchorable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereAnchored($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCategoryID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereFittableNonSingleton($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereGroupID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereIconID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUseBasePrice($value)
 * @mixin \Eloquent
 * @noinspection PhpFullyQualifiedNameUsageInspection
 * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
 */
	class IdeHelperGroup {}
}

namespace App{
/**
 * App\Type
 *
 * @property int $typeID
 * @property int|null $groupID
 * @property string|null $typeName
 * @property string|null $description
 * @property float|null $mass
 * @property float|null $volume
 * @property float|null $capacity
 * @property int|null $portionSize
 * @property int|null $raceID
 * @property string|null $basePrice
 * @property int|null $published
 * @property int|null $marketGroupID
 * @property int|null $iconID
 * @property int|null $soundID
 * @property int|null $graphicID
 * @property-read \App\Group|null $group
 * @method static \Illuminate\Database\Eloquent\Builder|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type query()
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereBasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereGraphicID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereGroupID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereIconID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereMarketGroupID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereMass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type wherePortionSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereRaceID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereSoundID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereTypeID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Type whereVolume($value)
 * @mixin \Eloquent
 * @noinspection PhpFullyQualifiedNameUsageInspection
 * @noinspection PhpUnnecessaryFullyQualifiedNameInspection
 */
	class IdeHelperType {}
}

