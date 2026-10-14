<?php

namespace Spawner;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\tile\MobSpawner;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\Config;
use onebone\economyapi\EconomyAPI;

class PTask extends PluginTask {
  
  public $plugin;
  public $block;
  public $type;
  
  public function __construct(Main $plugin, Block $block, $type) {
    
    parent::__construct($plugin);
    $this->plugin = $plugin;
    $this->block = $block;
    $this->type = $type;
    }
    
  public function onRun($tick) {
    
    $tile = $this->block->getLevel()->getTile(new Vector3($this->block->getX(), $this->block->getY(), $this->block->getZ()));
    
    
    if($tile instanceof MobSpawner) {
      $entityIds = [
        "zombie" => 32,
        "skeleton" => 34,
        "pigman" => 36,
        "spider" => 35,
        "iron_golem" => 20,
        "blaze" => 43,
        "pig" => 12,
        "cow" => 11,
        "chicken" => 10,
        "squid" => 17
      ];

      if(isset($entityIds[$this->type])) {
        $tile->setEntityId($entityIds[$this->type]);
        $this->plugin->getLogger()->debug("Set spawner entity type to " . $this->type . ".");
      }
     }
   }
 }
class Main extends PluginBase implements Listener {
  
  public $cfg;
  public $eco;
  public function onEnable() {
    
    if(!is_dir($this->getDataFolder())) {
      mkdir($this->getDataFolder());
      }
      
    $this->cfg = new Config($this->getDataFolder() ."settings.yml", Config::YAML, [
    "zombie" => 5000000,
    "skeleton" => 5000000,
    "spider" => 5000000,
    "pigman" => 5000000,
    "iron_golem" => 5000000,
    "blaze" => 5000000,
    "pig" => 5000000,
    "cow" => 5000000,
    "chicken" => 5000000,
    "squid" => 5000000
    ]);
    
    $this->eco = EconomyAPI::getInstance();
    $this->getServer()->getPluginManager()->registerEvents($this,$this);
    
    }
    
  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
    if(strtolower($cmd->getName()) !== "mualong") {
      return false;
    }

    if(!isset($args[0])) {
      $sender->sendMessage("§bGhi: §e/mualong danhsach §bđể xem giá. Dùng §e/mualong <loài> §bđể mua lồng.");
      return true;
    }

    $type = strtolower($args[0]);
    if($type === "danhsach") {
      foreach($this->cfg->getAll() as $key => $price) {
        $sender->sendMessage("§aLồng §b" . $key . ": §e" . $price);
      }
      return true;
    }

    $types = ["zombie", "skeleton", "spider", "pigman", "iron_golem", "blaze", "pig", "cow", "chicken", "squid"];
    if(!in_array($type, $types, true)) {
      $sender->sendMessage("§cLoài không hợp lệ. Dùng /mualong danhsach để xem danh sách.");
      return true;
    }

    if(!$sender instanceof Player) {
      $sender->sendMessage("§cChỉ người chơi đang online mới có thể mua lồng.");
      return true;
    }

    $this->purchaseSpawner($sender, $type);
    return true;
  }

  private function purchaseSpawner(Player $player, $type) {
    $price = $this->cfg->get($type, null);
    if(!is_numeric($price) || !is_finite((float) $price) || (float) $price <= 0) {
      $player->sendMessage("§cGiá lồng này chưa được cấu hình hợp lệ.");
      return false;
    }
    $price = (float) $price;

    $item = Item::get(52, 0, 1);
    $displayName = ucwords(str_replace("_", " ", $type));
    $item->setCustomName("§r§b" . $displayName . " Spawner");
    $inventory = $player->getInventory();
    if(!$inventory->canAddItem($item)) {
      $player->sendMessage("§cHãy chừa một ô trống trong túi trước khi mua.");
      return false;
    }

    $balance = $this->eco->myMoney($player->getName());
    if(!is_numeric($balance) || (float) $balance < $price) {
      $player->sendMessage("§cBạn không đủ tiền để mua lồng: " . $price . "$.");
      return false;
    }

    $result = $this->eco->reduceMoney($player->getName(), $price);
    if($result !== EconomyAPI::RET_SUCCESS) {
      $player->sendMessage("§cGiao dịch không thành công. Tiền chưa bị trừ.");
      return false;
    }

    if(!$inventory->canAddItem($item)) {
      $refund = $this->eco->addMoney($player->getName(), $price);
      if($refund === EconomyAPI::RET_SUCCESS) {
        $player->sendMessage("§cTúi đã đầy trong lúc giao dịch; tiền đã được hoàn lại.");
        return false;
      }

      $player->getLevel()->dropItem($player, $item);
      $this->getLogger()->critical("Could not refund or store a paid spawner for " . $player->getName() . "; dropped it at the player's location.");
      $player->sendMessage("§eGiao dịch đã trừ tiền nhưng không hoàn được; lồng đã được thả tại vị trí của bạn.");
      return false;
    }

    $leftovers = $inventory->addItem($item);
    if(count($leftovers) > 0) {
      $refund = $this->eco->addMoney($player->getName(), $price);
      if($refund === EconomyAPI::RET_SUCCESS) {
        $player->sendMessage("§cKhông thể cất lồng vào túi; tiền đã được hoàn lại.");
        return false;
      }
      foreach($leftovers as $leftover) {
        $player->getLevel()->dropItem($player, $leftover);
      }
      $this->getLogger()->critical("Could not refund a paid spawner for " . $player->getName() . "; dropped the item at the player's location.");
      $player->sendMessage("§eGiao dịch đã trừ tiền nhưng không hoàn được; lồng đã được thả tại vị trí của bạn.");
      return false;
    }

    $player->sendMessage("§a» Bạn đã mua lồng §b" . $displayName . " Spawner§a!");
    return true;
  }

        public function onPlace(BlockPlaceEvent $ev) {
          $p = $ev->getPlayer();

          $block = $ev->getBlock();
          $item = $ev->getItem();
          
          if($block->getId() == 52) {
            
            switch($item->getName()) {
              
              case "§r§bZombie Spawner":
              
                $this->getServer()->getScheduler()->scheduleDelayedTask(new PTask($this, $block, "zombie"), 20);
                break;
              case "§r§bSkeleton Spawner":
              
                $this->getServer()->getScheduler()->scheduleDelayedTask(new PTask($this, $block, "skeleton"), 20);
                break;
              
              case "§r§bSpider Spawner":
              
                $this->getServer()->getScheduler()->scheduleDelayedTask(new PTask($this, $block, "spider"), 20);
                break;
              
              case "§r§bPigman Spawner":
              
                $this->getServer()->getScheduler()->scheduleDelayedTask(new PTask($this, $block, "pigman"), 20);
                break;
                
              case "§r§bIron Golem Spawner":
              
                $this->getServer()->getScheduler()->scheduleDelayedTask(new PTask($this, $block, "iron_golem"), 20);
                break;
                
              case "§r§bBlaze Spawner":
              
                $this->getServer()->getScheduler()->scheduleDelayedTask(new PTask($this, $block, "blaze"), 20);
                break;
              
              case "§r§bPig Spawner":
              
                $this->getServer()->getScheduler()->scheduleDelayedTask(new PTask($this, $block, "pig"), 20);
                break;
              
              case "§r§bCow Spawner":
              
                $this->getServer()->getScheduler()->scheduleDelayedTask(new PTask($this, $block, "cow"), 20);
                break;
                
              case "§r§bChicken Spawner":
              
                $this->getServer()->getScheduler()->scheduleDelayedTask(new PTask($this, $block, "chicken"), 20);
                break;
                
              case "§r§bSquid Spawner":
              
                $this->getServer()->getScheduler()->scheduleDelayedTask(new PTask($this, $block, "squid"), 20);
                break;
               }
             }
           }
           
  private function giveSpawnerItem(Player $player, Item $item) {
    foreach($player->getInventory()->addItem($item) as $leftover) {
      $player->getLevel()->dropItem($player, $leftover);
    }
  }

           public function onBreak(BlockBreakEvent $ev) {
           
           $block = $ev->getBlock();
           $player = $ev->getPlayer();
           $tile = $player->getLevel()->getTile($block);
           if($block->getId() == 52) {
           
           if($player->getInventory()->getItemInHand()->hasEnchantment(16)) {
           
            if($tile instanceof MobSpawner) {
            
              $eid = $tile->getEntityId();
              
              switch($eid) {
              
              case 32:
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bZombie Spawner");
              $this->giveSpawnerItem($player, $item);
              break;
              
              case 36:
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bPigman Spawner");
              $this->giveSpawnerItem($player, $item);
              break;
              
              case 34:
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bSkeleton Spawner");
              $this->giveSpawnerItem($player, $item);
              break;
              
              case 35:
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bSpider Spawner");
              $this->giveSpawnerItem($player, $item);
              break;
              
              case 20:
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bIron Golem Spawner");
              $this->giveSpawnerItem($player, $item);
              break;
              
              case 43:
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bBlaze Spawner");
              $this->giveSpawnerItem($player, $item);
              break;
              
              case 12:
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bPig Spawner");
              $this->giveSpawnerItem($player, $item);
              break;
              
              case 11:
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bCow Spawner");
              $this->giveSpawnerItem($player, $item);
              break;
              
              case 10:
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bChicken Spawner");
              $this->giveSpawnerItem($player, $item);
              break;
              
              case 17:
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bSquid Spawner");
              $this->giveSpawnerItem($player, $item);
              break;
              }
            }
          }
        }
      }
    }
