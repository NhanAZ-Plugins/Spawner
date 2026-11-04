<?php

declare(strict_types=1);

namespace Spawner;

use onebone\economyapi\EconomyAPI;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

final class Main extends PluginBase implements Listener{
	private Config $config;
	private EconomyAPI $economy;

	protected function onEnable() : void{
		$dataFolder = $this->getDataFolder();
		if(!is_dir($dataFolder) && !mkdir($dataFolder, 0777, true) && !is_dir($dataFolder)){
			throw new \RuntimeException("Could not create Spawner data folder");
		}

		$settingsPath = $dataFolder . "settings.yml";
		if(!is_file($settingsPath)){
			$this->saveResource("settings.yml");
		}

		$this->config = new Config($settingsPath, Config::YAML);
		$this->economy = EconomyAPI::getInstance();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(strtolower($command->getName()) !== "mualong"){
			return false;
		}

		if(!isset($args[0])){
			$sender->sendMessage("§bGhi: §e/mualong danhsach §bđể xem giá. Dùng §e/mualong <loài> §bđể mua lồng.");
			return true;
		}

		$type = strtolower($args[0]);
		if($type === "danhsach"){
			foreach(SpawnerType::getAllTypes() as $availableType){
				$price = $this->config->get($availableType, "chưa cấu hình");
				$displayPrice = is_scalar($price) ? (string) $price : "cấu hình không hợp lệ";
				$sender->sendMessage("§aLồng §b" . $availableType . ": §e" . $displayPrice);
			}
			return true;
		}

		$displayName = SpawnerType::getDisplayName($type);
		if($displayName === null){
			$sender->sendMessage("§cLoài không hợp lệ. Dùng /mualong danhsach để xem danh sách.");
			return true;
		}

		if(!$sender instanceof Player){
			$sender->sendMessage("§cChỉ người chơi đang online mới có thể mua lồng.");
			return true;
		}

		$this->purchaseSpawner($sender, $type, $displayName);
		return true;
	}

	private function purchaseSpawner(Player $player, string $type, string $displayName) : bool{
		$price = $this->config->get($type, null);
		if(!is_numeric($price) || !is_finite((float) $price) || (float) $price <= 0){
			$player->sendMessage("§cGiá lồng này chưa được cấu hình hợp lệ.");
			return false;
		}
		$price = (float) $price;

		$item = $this->createSpawnerItem($type);
		$inventory = $player->getInventory();
		if(!$inventory->canAddItem($item)){
			$player->sendMessage("§cHãy chừa một ô trống trong túi trước khi mua.");
			return false;
		}

		$playerName = $player->getName();
		$balance = $this->economy->myMoney($playerName);
		if(!is_numeric($balance) || (float) $balance < $price){
			$player->sendMessage("§cBạn không đủ tiền để mua lồng: " . $price . "$.");
			return false;
		}

		if($this->economy->reduceMoney($playerName, $price) !== EconomyAPI::RET_SUCCESS){
			$player->sendMessage("§cGiao dịch không thành công. Tiền chưa bị trừ.");
			return false;
		}

		$leftovers = $inventory->addItem($item);
		if(count($leftovers) > 0){
			if($this->economy->addMoney($playerName, $price) === EconomyAPI::RET_SUCCESS){
				$player->sendMessage("§cKhông thể cất lồng vào túi; tiền đã được hoàn lại.");
				return false;
			}

			foreach($leftovers as $leftover){
				$this->dropItemAtPlayer($player, $leftover);
			}
			$this->getLogger()->critical("Could not refund a paid spawner for " . $playerName . "; dropped the item at the player's location.");
			$player->sendMessage("§eGiao dịch đã trừ tiền nhưng không hoàn được; lồng đã được thả tại vị trí của bạn.");
			return false;
		}

		$player->sendMessage("§a» Bạn đã mua lồng §b" . $displayName . " Spawner§a!");
		return true;
	}

	private function createSpawnerItem(string $type) : Item{
		$displayName = SpawnerType::getDisplayName($type);
		$entityIdentifier = SpawnerType::getEntityIdentifier($type);
		if($displayName === null || $entityIdentifier === null){
			throw new \InvalidArgumentException("Unsupported spawner type: " . $type);
		}

		$blockData = CompoundTag::create()->setString("EntityIdentifier", $entityIdentifier);
		return VanillaBlocks::MONSTER_SPAWNER()->asItem()
			->setCustomBlockData($blockData)
			->setCustomName("§r§b" . $displayName . " Spawner");
	}

	private function dropItemAtPlayer(Player $player, Item $item) : void{
		$player->getWorld()->dropItem($player->getPosition(), $item);
	}

	public function onBreak(BlockBreakEvent $event) : void{
		$block = $event->getBlock();
		if($block->getTypeId() !== BlockTypeIds::MONSTER_SPAWNER || !$event->getItem()->hasEnchantment(VanillaEnchantments::SILK_TOUCH())){
			return;
		}

		$position = $block->getPosition();
		$tile = $position->getWorld()->getTile($position);
		$blockData = $tile?->getCleanedNBT();
		if($blockData === null){
			return;
		}

		$type = SpawnerType::getTypeForEntityIdentifier($blockData->getString("EntityIdentifier", ""));
		if($type === null){
			return;
		}

		$displayName = SpawnerType::getDisplayName($type);
		if($displayName === null){
			return;
		}

		$item = VanillaBlocks::MONSTER_SPAWNER()->asItem()
			->setCustomBlockData($blockData)
			->setCustomName("§r§b" . $displayName . " Spawner");
		$event->setDrops([$item]);
	}
}
