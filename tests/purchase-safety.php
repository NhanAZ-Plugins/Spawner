<?php

namespace pocketmine\plugin {
    class PluginBase {
        private $logger;
        public function __construct() { $this->logger = new \SpawnerTest\Logger(); }
        public function getLogger() { return $this->logger; }
    }
}

namespace pocketmine\command {
    class CommandSender {
        public $messages = [];
        public function sendMessage($message) { $this->messages[] = $message; }
    }
    class Command {
        private $name;
        public function __construct($name) { $this->name = $name; }
        public function getName() { return $this->name; }
    }
}

namespace pocketmine\event {
    interface Listener {}
}

namespace pocketmine\event\block {
    class BlockPlaceEvent {}
    class BlockBreakEvent {
        private $block;
        private $player;
        public function __construct($block, $player) { $this->block = $block; $this->player = $player; }
        public function getBlock() { return $this->block; }
        public function getPlayer() { return $this->player; }
    }
}

namespace pocketmine\math {
    class Vector3 {
        public function __construct($x = 0, $y = 0, $z = 0) {}
    }
}

namespace pocketmine\block {
    class Block {
        public function getLevel() { return null; }
        public function getX() { return 0; }
        public function getY() { return 0; }
        public function getZ() { return 0; }
    }
}

namespace pocketmine\tile {
    class MobSpawner {
        public $entityId;
        public function setEntityId($entityId) { $this->entityId = $entityId; }
        public function getEntityId() { return $this->entityId; }
    }
}

namespace pocketmine\scheduler {
    class PluginTask {
        public function __construct($plugin) {}
    }
}

namespace pocketmine\utils {
    class Config {}
}

namespace pocketmine\item {
    class Item {
        public $name;
        public static function get($id, $meta, $count) { return new self(); }
        public function setCustomName($name) { $this->name = $name; }
        public function getName() { return $this->name; }
        public function hasEnchantment($id) { return true; }
    }
}

namespace onebone\economyapi {
    class EconomyAPI {
        const RET_SUCCESS = 1;
        public $balance = 100;
        public $reduceResult = 1;
        public $refundResult = 1;
        public $reductions = 0;
        public $onReduce;
        public function myMoney($player) { return $this->balance; }
        public function reduceMoney($player, $amount) {
            $this->reductions++;
            if($this->reduceResult !== self::RET_SUCCESS) { return $this->reduceResult; }
            $this->balance -= $amount;
            if(is_callable($this->onReduce)) { call_user_func($this->onReduce); }
            return self::RET_SUCCESS;
        }
        public function addMoney($player, $amount) {
            if($this->refundResult !== self::RET_SUCCESS) { return $this->refundResult; }
            $this->balance += $amount;
            return self::RET_SUCCESS;
        }
    }
}

namespace pocketmine {
    class Player extends \pocketmine\command\CommandSender {
        private $name;
        private $inventory;
        private $level;
        public function __construct($name, $inventory, $level) {
            $this->name = $name;
            $this->inventory = $inventory;
            $this->level = $level;
        }
        public function getName() { return $this->name; }
        public function getInventory() { return $this->inventory; }
        public function getLevel() { return $this->level; }
    }
}

namespace SpawnerTest {
    class Logger {
        public $critical = [];
        public function debug($message) {}
        public function critical($message) { $this->critical[] = $message; }
    }
    class Config {
        private $values;
        public function __construct($values) { $this->values = $values; }
        public function get($key, $default = null) { return array_key_exists($key, $this->values) ? $this->values[$key] : $default; }
        public function getAll() { return $this->values; }
    }
    class Inventory {
        public $canAdd = true;
        public $items = [];
        public function canAddItem($item) { return $this->canAdd; }
        public function getItemInHand() { return new \pocketmine\item\Item(); }
        public function addItem($item) {
            if(!$this->canAdd) { return [$item]; }
            $this->items[] = $item;
            return [];
        }
    }
    class Level {
        public $drops = [];
        public $tile;
        public function dropItem($position, $item) { $this->drops[] = $item; }
        public function getTile($position) { return $this->tile; }
    }
    class Block extends \pocketmine\block\Block {
        private $level;
        private $id;
        public function __construct($level, $id = 52) { $this->level = $level; $this->id = $id; }
        public function getLevel() { return $this->level; }
        public function getId() { return $this->id; }
    }
}

namespace {
    require dirname(__DIR__) . "/src/spawner/Main.php";

    function check($condition, $message) {
        if(!$condition) { throw new \RuntimeException($message); }
    }

    function setup($price = 25, $balance = 100) {
        $inventory = new \SpawnerTest\Inventory();
        $level = new \SpawnerTest\Level();
        $player = new \pocketmine\Player("Tester", $inventory, $level);
        $economy = new \onebone\economyapi\EconomyAPI();
        $economy->balance = $balance;
        $main = new \Spawner\Main();
        $main->cfg = new \SpawnerTest\Config(["zombie" => $price]);
        $main->eco = $economy;
        return [$main, $player, $economy, $inventory, $level];
    }

    $command = new \pocketmine\command\Command("mualong");

    [$main, $player, $economy, $inventory] = setup();
    check($main->onCommand($player, $command, "mualong", ["zombie"]), "Purchase command was not handled.");
    check($economy->balance === 75.0 && count($inventory->items) === 1, "Successful purchase must charge once and grant one item.");
    check($inventory->items[0]->getName() === "§r§bZombie Spawner", "Purchase gave the wrong spawner name.");

    [$main, $player, $economy, $inventory] = setup();
    $inventory->canAdd = false;
    $main->onCommand($player, $command, "mualong", ["zombie"]);
    check($economy->reductions === 0 && $economy->balance === 100, "Full inventory must not charge the player.");

    [$main, $player, $economy, $inventory] = setup(25, 10);
    $main->onCommand($player, $command, "mualong", ["zombie"]);
    check($economy->reductions === 0 && count($inventory->items) === 0, "Insufficient balance must not charge or grant an item.");

    [$main, $player, $economy, $inventory] = setup();
    $economy->reduceResult = -2;
    $main->onCommand($player, $command, "mualong", ["zombie"]);
    check($economy->balance === 100 && count($inventory->items) === 0, "Rejected EconomyAPI transaction must not grant an item.");

    [$main, $player, $economy, $inventory] = setup();
    $economy->onReduce = function() use ($inventory) { $inventory->canAdd = false; };
    $main->onCommand($player, $command, "mualong", ["zombie"]);
    check($economy->balance === 100.0 && count($inventory->items) === 0, "Capacity race should refund a successful debit.");

    [$main, $player, $economy, $inventory, $level] = setup();
    $economy->refundResult = -2;
    $economy->onReduce = function() use ($inventory) { $inventory->canAdd = false; };
    $main->onCommand($player, $command, "mualong", ["zombie"]);
    check(count($level->drops) === 1 && $economy->balance === 75.0, "When a refund is rejected, the paid item must be dropped instead of lost.");

    [$main, $player, $economy, $inventory] = setup("not-a-price");
    $main->onCommand($player, $command, "mualong", ["zombie"]);
    check($economy->reductions === 0 && count($inventory->items) === 0, "Invalid configured price must not charge or grant an item.");

    $entityIds = [
        "zombie" => 32, "skeleton" => 34, "pigman" => 36, "spider" => 35,
        "iron_golem" => 20, "blaze" => 43, "pig" => 12, "cow" => 11,
        "chicken" => 10, "squid" => 17
    ];
    foreach($entityIds as $type => $entityId) {
        [$main, $player, $economy, $inventory, $level] = setup();
        $level->tile = new \pocketmine\tile\MobSpawner();
        (new \Spawner\PTask($main, new \SpawnerTest\Block($level), $type))->onRun(0);
        check($level->tile->entityId === $entityId, "Placed $type spawner mapped to the wrong entity ID.");
    }

    $displayNames = [
        32 => "Zombie", 34 => "Skeleton", 36 => "Pigman", 35 => "Spider",
        20 => "Iron Golem", 43 => "Blaze", 12 => "Pig", 11 => "Cow",
        10 => "Chicken", 17 => "Squid"
    ];
    foreach($displayNames as $entityId => $displayName) {
        [$main, $player, $economy, $inventory, $level] = setup();
        $level->tile = new \pocketmine\tile\MobSpawner();
        $level->tile->setEntityId($entityId);
        $main->onBreak(new \pocketmine\event\block\BlockBreakEvent(new \SpawnerTest\Block($level), $player));
        check(count($inventory->items) === 1, "Breaking $displayName spawner did not return an item.");
        check($inventory->items[0]->getName() === "§r§b$displayName Spawner", "Breaking $displayName spawner returned the wrong type.");
    }

    echo "Spawner purchase and entity mapping checks passed.\n";
}

