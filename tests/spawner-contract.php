<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag{
	final class CompoundTag{
		/** @var array<string, int|string> */
		private array $values = [];

		public static function create() : self{
			return new self();
		}

		public function setString(string $name, string $value) : self{
			$this->values[$name] = $value;
			return $this;
		}

		public function setInt(string $name, int $value) : self{
			$this->values[$name] = $value;
			return $this;
		}

		public function getString(string $name, string $default = "") : string{
			$value = $this->values[$name] ?? $default;
			return is_string($value) ? $value : $default;
		}

		public function getInt(string $name, int $default = 0) : int{
			$value = $this->values[$name] ?? $default;
			return is_int($value) ? $value : $default;
		}
	}
}

namespace pocketmine\block{
	final class BlockTypeIds{
		public const MONSTER_SPAWNER = 42;
	}

	class Block{
		public function __construct(private int $typeId, private \pocketmine\world\Position $position){}
		public function getTypeId() : int{ return $this->typeId; }
		public function getPosition() : \pocketmine\world\Position{ return $this->position; }
	}

	final class BlockState{
		public function asItem() : \pocketmine\item\Item{ return new \pocketmine\item\Item(); }
	}

	final class VanillaBlocks{
		public static function MONSTER_SPAWNER() : BlockState{ return new BlockState(); }
	}
}

namespace pocketmine\item\enchantment{
	final class VanillaEnchantments{
		public static function SILK_TOUCH() : string{ return "silk-touch"; }
	}
}

namespace pocketmine\item{
	class Item{
		private string $customName = "";
		private ?\pocketmine\nbt\tag\CompoundTag $customBlockData = null;
		public bool $hasSilkTouch = true;

		public function setCustomName(string $name) : self{ $this->customName = $name; return $this; }
		public function getCustomName() : string{ return $this->customName; }
		public function setCustomBlockData(\pocketmine\nbt\tag\CompoundTag $data) : self{ $this->customBlockData = $data; return $this; }
		public function getCustomBlockData() : ?\pocketmine\nbt\tag\CompoundTag{ return $this->customBlockData; }
		public function hasEnchantment(string $enchantment) : bool{ return $enchantment === "silk-touch" && $this->hasSilkTouch; }
	}
}

namespace pocketmine\command{
	class CommandSender{
		/** @var list<string> */
		public array $messages = [];
		public function sendMessage(string $message) : void{ $this->messages[] = $message; }
	}

	final class Command{
		public function __construct(private string $name){}
		public function getName() : string{ return $this->name; }
	}
}

namespace pocketmine\player{
	final class Player extends \pocketmine\command\CommandSender{
		public function __construct(private string $name, private \SpawnerTest\Inventory $inventory, private \pocketmine\world\World $world, private \pocketmine\world\Position $position){}
		public function getName() : string{ return $this->name; }
		public function getInventory() : \SpawnerTest\Inventory{ return $this->inventory; }
		public function getWorld() : \pocketmine\world\World{ return $this->world; }
		public function getPosition() : \pocketmine\world\Position{ return $this->position; }
	}
}

namespace pocketmine\event{
	interface Listener{}
}

namespace pocketmine\event\block{
	final class BlockBreakEvent{
		/** @var list<\pocketmine\item\Item> */
		private array $drops = [];
		public function __construct(private \pocketmine\block\Block $block, private \pocketmine\player\Player $player, private \pocketmine\item\Item $item){}
		public function getBlock() : \pocketmine\block\Block{ return $this->block; }
		public function getPlayer() : \pocketmine\player\Player{ return $this->player; }
		public function getItem() : \pocketmine\item\Item{ return $this->item; }
		/** @param list<\pocketmine\item\Item> $drops */
		public function setDrops(array $drops) : void{ $this->drops = $drops; }
		/** @return list<\pocketmine\item\Item> */
		public function getDrops() : array{ return $this->drops; }
	}
}

namespace pocketmine\plugin{
	class PluginBase{
		private \SpawnerTest\Logger $logger;
		public function __construct(){ $this->logger = new \SpawnerTest\Logger(); }
		public function getLogger() : \SpawnerTest\Logger{ return $this->logger; }
	}
}

namespace pocketmine\utils{
	final class Config{
		public const YAML = 2;
		/** @param array<string, int|string> $values */
		public function __construct(private array $values){}
		public function get(string $key, mixed $default = null) : mixed{ return $this->values[$key] ?? $default; }
	}
}

namespace pocketmine\world{
	final class Position{
		public function __construct(private World $world){}
		public function getWorld() : World{ return $this->world; }
	}

	final class World{
		public ?\pocketmine\block\tile\Tile $tile = null;
		/** @var list<\pocketmine\item\Item> */
		public array $droppedItems = [];
		public function getTile(Position $position) : ?\pocketmine\block\tile\Tile{ return $this->tile; }
		public function dropItem(Position $position, \pocketmine\item\Item $item) : void{ $this->droppedItems[] = $item; }
	}
}

namespace pocketmine\block\tile{
	final class Tile{
		public function __construct(private ?\pocketmine\nbt\tag\CompoundTag $data){}
		public function getCleanedNBT() : ?\pocketmine\nbt\tag\CompoundTag{ return $this->data; }
	}
}

namespace onebone\economyapi{
	final class EconomyAPI{
		public const RET_SUCCESS = 1;
		public float $balance = 100;
		public int $reduceResult = self::RET_SUCCESS;
		public int $refundResult = self::RET_SUCCESS;
		public int $reductions = 0;
		public ?\Closure $afterReduction = null;
		public function myMoney(string $player) : float{ return $this->balance; }
		public function reduceMoney(string $player, float $amount) : int{
			$this->reductions++;
			if($this->reduceResult !== self::RET_SUCCESS){ return $this->reduceResult; }
			$this->balance -= $amount;
			if($this->afterReduction !== null){ ($this->afterReduction)(); }
			return self::RET_SUCCESS;
		}
		public function addMoney(string $player, float $amount) : int{
			if($this->refundResult !== self::RET_SUCCESS){ return $this->refundResult; }
			$this->balance += $amount;
			return self::RET_SUCCESS;
		}
	}
}

namespace SpawnerTest{
	final class Logger{
		/** @var list<string> */
		public array $criticalMessages = [];
		public function critical(string $message) : void{ $this->criticalMessages[] = $message; }
	}

	final class Inventory{
		public bool $canAdd = true;
		/** @var list<\pocketmine\item\Item> */
		public array $items = [];
		public function canAddItem(\pocketmine\item\Item $item) : bool{ return $this->canAdd; }
		/** @return list<\pocketmine\item\Item> */
		public function addItem(\pocketmine\item\Item $item) : array{
			if(!$this->canAdd){ return [$item]; }
			$this->items[] = $item;
			return [];
		}
	}
}

namespace{
	require dirname(__DIR__) . "/src/Spawner/SpawnerType.php";
	require dirname(__DIR__) . "/src/Spawner/Main.php";

	function check(bool $condition, string $message) : void{
		if(!$condition){ throw new \RuntimeException($message); }
	}

	/** @return array{\Spawner\Main, \pocketmine\player\Player, \onebone\economyapi\EconomyAPI, \SpawnerTest\Inventory, \pocketmine\world\World} */
	function setup(mixed $price = 25, float $balance = 100) : array{
		$inventory = new \SpawnerTest\Inventory();
		$world = new \pocketmine\world\World();
		$position = new \pocketmine\world\Position($world);
		$player = new \pocketmine\player\Player("Tester", $inventory, $world, $position);
		$economy = new \onebone\economyapi\EconomyAPI();
		$economy->balance = $balance;
		$main = new \Spawner\Main();
		$configProperty = new \ReflectionProperty(\Spawner\Main::class, "config");
		$configProperty->setValue($main, new \pocketmine\utils\Config(["zombie" => $price]));
		$economyProperty = new \ReflectionProperty(\Spawner\Main::class, "economy");
		$economyProperty->setValue($main, $economy);
		return [$main, $player, $economy, $inventory, $world];
	}

	$types = [
		"zombie" => ["minecraft:zombie", 32, "Zombie"],
		"skeleton" => ["minecraft:skeleton", 34, "Skeleton"],
		"pigman" => ["minecraft:zombie_pigman", 36, "Pigman"],
		"spider" => ["minecraft:spider", 35, "Spider"],
		"iron_golem" => ["minecraft:iron_golem", 20, "Iron Golem"],
		"blaze" => ["minecraft:blaze", 43, "Blaze"],
		"pig" => ["minecraft:pig", 12, "Pig"],
		"cow" => ["minecraft:cow", 11, "Cow"],
		"chicken" => ["minecraft:chicken", 10, "Chicken"],
		"squid" => ["minecraft:squid", 17, "Squid"]
	];
	check(count(\Spawner\SpawnerType::getAllTypes()) === count($types), "The shop type list must retain all ten original entries.");
	foreach($types as $type => [$identifier, $legacyId, $name]){
		check(\Spawner\SpawnerType::getEntityIdentifier($type) === $identifier, "Wrong EntityIdentifier for $type.");
		check(\Spawner\SpawnerType::getLegacyEntityId($type) === $legacyId, "Wrong legacy entity ID for $type.");
		check(\Spawner\SpawnerType::getDisplayName($type) === $name, "Wrong display name for $type.");
		check(\Spawner\SpawnerType::getTypeForEntityIdentifier($identifier) === $type, "Could not recover $type from its EntityIdentifier.");
		check(\Spawner\SpawnerType::getTypeForLegacyEntityId($legacyId) === $type, "Could not recover $type from its legacy entity ID.");
	}
	check(\Spawner\SpawnerType::getTypeForEntityIdentifier("minecraft:unknown") === null, "Unknown entity identifiers must remain unsupported.");

	$command = new \pocketmine\command\Command("mualong");
	[$main, $player, $economy, $inventory] = setup();
	check($main->onCommand($player, $command, "mualong", ["zombie"]), "Purchase command was not handled.");
	check($economy->balance === 75.0 && count($inventory->items) === 1, "Successful purchase must charge once and grant one item.");
	check($inventory->items[0]->getCustomName() === "§r§bZombie Spawner", "Purchase gave the wrong display name.");
	check($inventory->items[0]->getCustomBlockData()?->getString("EntityIdentifier") === "minecraft:zombie", "Purchased item did not store its mob identifier.");

	[$main, $player, $economy, $inventory] = setup();
	$inventory->canAdd = false;
	$main->onCommand($player, $command, "mualong", ["zombie"]);
	check($economy->reductions === 0 && $economy->balance === 100.0, "Full inventory must not charge the player.");

	[$main, $player, $economy, $inventory] = setup(25, 10);
	$main->onCommand($player, $command, "mualong", ["zombie"]);
	check($economy->reductions === 0 && count($inventory->items) === 0, "Insufficient balance must not charge or grant an item.");

	[$main, $player, $economy, $inventory] = setup();
	$economy->reduceResult = -2;
	$main->onCommand($player, $command, "mualong", ["zombie"]);
	check($economy->balance === 100.0 && count($inventory->items) === 0, "Rejected EconomyAPI transaction must not grant an item.");

	[$main, $player, $economy, $inventory] = setup();
	$economy->afterReduction = static function() use ($inventory) : void{ $inventory->canAdd = false; };
	$main->onCommand($player, $command, "mualong", ["zombie"]);
	check($economy->balance === 100.0 && count($inventory->items) === 0, "Capacity race should refund a successful debit.");

	[$main, $player, $economy, $inventory, $world] = setup();
	$economy->refundResult = -2;
	$economy->afterReduction = static function() use ($inventory) : void{ $inventory->canAdd = false; };
	$main->onCommand($player, $command, "mualong", ["zombie"]);
	check(count($world->droppedItems) === 1 && $economy->balance === 75.0, "A rejected refund must not lose a paid spawner.");

	[$main, $player, $economy, $inventory] = setup("not-a-price");
	$main->onCommand($player, $command, "mualong", ["zombie"]);
	check($economy->reductions === 0 && count($inventory->items) === 0, "Invalid configured price must not charge or grant an item.");

	[$main, $player, , , $world] = setup();
	$world->tile = new \pocketmine\block\tile\Tile(\pocketmine\nbt\tag\CompoundTag::create()->setString("EntityIdentifier", "minecraft:blaze")->setInt("Delay", 123));
	$tool = new \pocketmine\item\Item();
	$block = new \pocketmine\block\Block(\pocketmine\block\BlockTypeIds::MONSTER_SPAWNER, $player->getPosition());
	$event = new \pocketmine\event\block\BlockBreakEvent($block, $player, $tool);
	$main->onBreak($event);
	check(count($event->getDrops()) === 1, "Silk Touch must recover a configured spawner.");
	check($event->getDrops()[0]->getCustomName() === "§r§bBlaze Spawner", "Recovered spawner had the wrong display name.");
	check($event->getDrops()[0]->getCustomBlockData()?->getString("EntityIdentifier") === "minecraft:blaze", "Recovered item lost its entity identifier.");
	check($event->getDrops()[0]->getCustomBlockData()?->getInt("Delay") === 123, "Recovered item must retain the spawner tile data.");

	[$main, $player, , , $world] = setup();
	$world->tile = new \pocketmine\block\tile\Tile(\pocketmine\nbt\tag\CompoundTag::create()->setString("EntityIdentifier", "minecraft:blaze"));
	$tool = new \pocketmine\item\Item();
	$tool->hasSilkTouch = false;
	$event = new \pocketmine\event\block\BlockBreakEvent(new \pocketmine\block\Block(\pocketmine\block\BlockTypeIds::MONSTER_SPAWNER, $player->getPosition()), $player, $tool);
	$main->onBreak($event);
	check($event->getDrops() === [], "A tool without Silk Touch must not receive a spawner drop.");

	echo "Spawner purchase safety, API 5 entity data, and Silk Touch recovery checks passed.\n";
}
