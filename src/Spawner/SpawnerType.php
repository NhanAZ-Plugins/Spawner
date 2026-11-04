<?php

declare(strict_types=1);

namespace Spawner;

/**
 * Keeps the historical shop entries mapped to Bedrock entity identifiers.
 *
 * Axolotl-PM uses EntityIdentifier for spawner tile data. The legacy numeric
 * IDs are retained only for translating existing plugin data and tests.
 */
final class SpawnerType{
	/** @var array<string, array{displayName: string, entityIdentifier: string, legacyEntityId: int}> */
	private const TYPES = [
		"zombie" => ["displayName" => "Zombie", "entityIdentifier" => "minecraft:zombie", "legacyEntityId" => 32],
		"skeleton" => ["displayName" => "Skeleton", "entityIdentifier" => "minecraft:skeleton", "legacyEntityId" => 34],
		"pigman" => ["displayName" => "Pigman", "entityIdentifier" => "minecraft:zombie_pigman", "legacyEntityId" => 36],
		"spider" => ["displayName" => "Spider", "entityIdentifier" => "minecraft:spider", "legacyEntityId" => 35],
		"iron_golem" => ["displayName" => "Iron Golem", "entityIdentifier" => "minecraft:iron_golem", "legacyEntityId" => 20],
		"blaze" => ["displayName" => "Blaze", "entityIdentifier" => "minecraft:blaze", "legacyEntityId" => 43],
		"pig" => ["displayName" => "Pig", "entityIdentifier" => "minecraft:pig", "legacyEntityId" => 12],
		"cow" => ["displayName" => "Cow", "entityIdentifier" => "minecraft:cow", "legacyEntityId" => 11],
		"chicken" => ["displayName" => "Chicken", "entityIdentifier" => "minecraft:chicken", "legacyEntityId" => 10],
		"squid" => ["displayName" => "Squid", "entityIdentifier" => "minecraft:squid", "legacyEntityId" => 17]
	];

	/** @return list<string> */
	public static function getAllTypes() : array{
		return array_keys(self::TYPES);
	}

	public static function getDisplayName(string $type) : ?string{
		return self::TYPES[$type]["displayName"] ?? null;
	}

	public static function getEntityIdentifier(string $type) : ?string{
		return self::TYPES[$type]["entityIdentifier"] ?? null;
	}

	public static function getLegacyEntityId(string $type) : ?int{
		return self::TYPES[$type]["legacyEntityId"] ?? null;
	}

	public static function getTypeForEntityIdentifier(string $entityIdentifier) : ?string{
		foreach(self::TYPES as $type => $data){
			if($data["entityIdentifier"] === $entityIdentifier){
				return $type;
			}
		}

		return null;
	}

	public static function getTypeForLegacyEntityId(int $entityId) : ?string{
		foreach(self::TYPES as $type => $data){
			if($data["legacyEntityId"] === $entityId){
				return $type;
			}
		}

		return null;
	}
}
