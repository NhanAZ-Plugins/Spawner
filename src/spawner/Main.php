<?php

namespace Spawner;

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
      echo "Translating to $this->type";
      
      switch($this->type) {
        case "zombie":
        
      $tile->setEntityId(32);
      break;
      
      case "pigman":
       $tile->setEntityId(36);
       break;
       
      case "spider":
       $tile->setEntityId(35);
       break;

      case "iron_golem":
      $tile->setEntityId(20);
      break;
      
      case "blaze":
      $tile->setEntityId(43);
      break;
      
      case "pig":
      $tile->setEntityId(12);
      break;
      
      case "cow":
      $tile->setEntityId(11);
      break;
      
      case "chicken":
      $tile->setEntityId(10);
      break;

      case "squid":
      $tile->setEntityId(17);
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
    
    public function onCommand(CommandSender $s, Command $cmd, $label, array $args) {
      
      if($cmd->getName() == "mualong") {
        
        if(isset($args[0])) {
          
          if(strtolower($args[0]) == "danhsach") {
            foreach($this->cfg->getAll() as $key => $val) {
              
              $s->sendMessage("§aLồng §b". $key .": §e". $val);
              }
            }
            
          switch(strtolower($args[0])) {
            
            case "squid":
              
               $pmoney = $this->eco->myMoney($s->getName());
               
               if($pmoney >= $this->cfg->get("squid")) {
                 
                 $this->eco->reduceMoney($s->getName(), $this->cfg->get("squid"));
                 
                 $spawnblock = Item::get(52, 0, 1);
                 
                 $spawnblock->setCustomName("§r§bSquid Spawner");
                 
                 $s->getInventory()->addItem($spawnblock);
                 $s->sendMessage("§a»Bạn Đã Mua Lồng§b Squid Spawner!");
                 } else {
                   $s->sendMessage("§a»Bạn Không Đủ Tiền Để Mua Lồng : §a". $this->cfg->get("zombie") ."$");
                   }
                  break;
                  
            case "chicken":
              
               $pmoney = $this->eco->myMoney($s->getName());
               
               if($pmoney >= $this->cfg->get("chicken")) {
                 
                 $this->eco->reduceMoney($s->getName(), $this->cfg->get("chicken"));
                 
                 $spawnblock = Item::get(52, 0, 1);
                 
                 $spawnblock->setCustomName("§r§bChicken Spawner");
                 
                 $s->getInventory()->addItem($spawnblock);
                 $s->sendMessage("§a»Bạn Đã Mua Lồng§b Chicken Spawner!");
                 } else {
                   $s->sendMessage("§a»Bạn Không Đủ Tiền Để Mua Lồng : §a". $this->cfg->get("zombie") ."$");
                   }
                  break;
                  
            case "cow":
              
               $pmoney = $this->eco->myMoney($s->getName());
               
               $this->eco->reduceMoney($s->getName(), $this->cfg->get("cow"));
               
               if($pmoney >= $this->cfg->get("cow")) {
                 
                 $spawnblock = Item::get(52, 0, 1);
                 
                 $spawnblock->setCustomName("§r§bCow Spawner");
                 
                 $s->getInventory()->addItem($spawnblock);
                 $s->sendMessage("§a»Bạn Đã Mua Lồng§b Cow Spawner!");
                 } else {
                   $s->sendMessage("§a»Bạn Không Đủ Tiền Để Mua Lồng : §a". $this->cfg->get("zombie") ."$");
                   }
                  break;
                  
            case "pig":
              
               $pmoney = $this->eco->myMoney($s->getName());
               
               if($pmoney >= $this->cfg->get("pig")) {
                 
                 $this->eco->reduceMoney($s->getName(), $this->cfg->get("pig"));
                 
                 $spawnblock = Item::get(52, 0, 1);
                 
                 $spawnblock->setCustomName("§r§bPig Spawner");
                 
                 $s->getInventory()->addItem($spawnblock);
                 $s->sendMessage("§a»Bạn Đã Mua Lồng§b Pig Spawner!");
                 } else {
                   $s->sendMessage("§a»Bạn Không Đủ Tiền Để Mua Lồng : §a". $this->cfg->get("zombie") ."$");
                   }
                  break;
                  
            case "blaze":
              
               $pmoney = $this->eco->myMoney($s->getName());
               
               if($pmoney >= $this->cfg->get("blaze")) {
                 
                 $this->eco->reduceMoney($s->getName(), $this->cfg->get("blaze"));
                 
                 $spawnblock = Item::get(52, 0, 1);
                 
                 $spawnblock->setCustomName("§r§bBlaze Spawner");
                 
                 $s->getInventory()->addItem($spawnblock);
                 $s->sendMessage("§a»Bạn Đã Mua Lồng§b Blaze Spawner!");
                 } else {
                   $s->sendMessage("§a»Bạn Không Đủ Tiền Để Mua Lồng : §a". $this->cfg->get("zombie") ."$");
                   }
                  break;
                  
            case "iron_golem":
              
               $pmoney = $this->eco->myMoney($s->getName());
               
               if($pmoney >= $this->cfg->get("iron_golem")) {
                 
                 $this->eco->reduceMoney($s->getName(), $this->cfg->get("iron_golem"));
                 
                 $spawnblock = Item::get(52, 0, 1);
                 
                 $spawnblock->setCustomName("§r§bIron Golem Spawner");
                 
                 $s->getInventory()->addItem($spawnblock);
                 $s->sendMessage("§a»Bạn Đã Mua Lồng§b Iron Golem Spawner!");
                 } else {
                   $s->sendMessage("§a»Bạn Không Đủ Tiền Để Mua Lồng : §a". $this->cfg->get("zombie") ."$");
                   }
                  break;
                  
            case "pigman":
              
               $pmoney = $this->eco->myMoney($s->getName());
               
               if($pmoney >= $this->cfg->get("pigman")) {
                 
                 $this->eco->reduceMoney($s->getName(), $this->cfg->get("pigman"));
               
                 $spawnblock = Item::get(52, 0, 1);
                 
                 $spawnblock->setCustomName("§r§bPigman Spawner");
                 
                 $s->getInventory()->addItem($spawnblock);
                 $s->sendMessage("§a»Bạn Đã Mua Lồng§b PigMan Spawner!");
                 } else {
                   $s->sendMessage("§a»Bạn Không Đủ Tiền Để Mua Lồng : §a". $this->cfg->get("zombie") ."$");
                   }
                  break;
                  
            case "spider":
              
               $pmoney = $this->eco->myMoney($s->getName());
               
               if($pmoney >= $this->cfg->get("spider")) {
                 
                 $this->eco->reduceMoney($s->getName(), $this->cfg->get("spider"));
                 
                 $spawnblock = Item::get(52, 0, 1);
                 
                 $spawnblock->setCustomName("§r§bSpider Spawner");
                 
                 $s->getInventory()->addItem($spawnblock);
                 $s->sendMessage("§a»Bạn Đã Mua Lồng§b Spider Spawner!");
                 } else {
                   $s->sendMessage("§a»Bạn Không Đủ Tiền Để Mua Lồng : §a". $this->cfg->get("zombie") ."$");
                   }
                  break;
                  
               case "skeleton":
              
               $pmoney = $this->eco->myMoney($s->getName());
               
               if($pmoney >= $this->cfg->get("skeleton")) {
                 
                 $this->eco->reduceMoney($s->getName(), $this->cfg->get("skeleton"));
                 
                 $spawnblock = Item::get(52, 0, 1);
                 
                 $spawnblock->setCustomName("§r§bSkeleton Spawner");
                 
                 $s->getInventory()->addItem($spawnblock);
                 $s->sendMessage("§a»Bạn Đã Mua Lồng§b Skeleton Spawner!");
                 } else {
                   $s->sendMessage("§a»Bạn Không Đủ Tiền Để Mua Lồng : §a". $this->cfg->get("zombie") ."$");
                   }
                  break;
                  
            case "zombie":
              
               $pmoney = $this->eco->myMoney($s->getName());
               
               if($pmoney >= $this->cfg->get("zombie")) {
                 
                 $this->eco->reduceMoney($s->getName(), $this->cfg->get("zombie"));
                 
                 $spawnblock = Item::get(52, 0, 1);
                 
                 $spawnblock->setCustomName("§r§bZombie Spawner");
                 
                 $s->getInventory()->addItem($spawnblock);
                 $s->sendMessage("§a»Bạn Đã Mua Lồng§b Zombie Spawner!");
                 } else {
                   $s->sendMessage("§a»Bạn Không Đủ Tiền Để Mua Lồng : §a". $this->cfg->get("zombie") ."§a$");
                   }
                  break;
                }
              } else {
                $s->sendMessage("§bGhi:§e /mualong dangsach§b Để Xem Danh Sách Lồng. Ghi:§e /mualong (Tên Thú)§b Để Mua Lồng");
                }
              }
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
           
           public function onBreak(BlockBreakEvent $ev) {
           
           $block = $ev->getBlock();
           $player = $ev->getPlayer();
           $tile = $player->getLevel()->getTile($block);
           if($block->getId() == 52) {
           
           if($player->getInventory()->getItemInHand()->hasEnchantment(16)) {
           
            if($tile instanceof MobSpawner) {
            
              $eid = $tile->getEntityId();
              
              switch($eid) {
              
              case "32":
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bZombie Spawner");
              $player->getInventory()->addItem($item);
              break;
              
              case "33":
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bPigman Spawner");
              $player->getInventory()->addItem($item);
              break;
              
              case "34":
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bSkeleton Spawner");
              $player->getInventory()->addItem($item);
              break;
              
              case "35":
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bSpider Spawner");
              $player->getInventory()->addItem($item);
              break;
              
              case "20":
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bIron Golem Spawner");
              $player->getInventory()->addItem($item);
              break;
              
              case "43":
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bBlaze Spawner");
              $player->getInventory()->addItem($item);
              break;
              
              case "12":
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bPig Spawner");
              $player->getInventory()->addItem($item);
              break;
              
              case "10":
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bCow Spawner");
              $player->getInventory()->addItem($item);
              break;
              
              case "11":
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bChicken Spawner");
              $player->getInventory()->addItem($item);
              break;
              
              case "13":
              $item = Item::get(52, 0, 1);
              $item->setCustomName("§r§bSquid Spawner");
              $player->getInventory()->addItem($item);
              break;
              }
            }
          }
        }
      }
    }