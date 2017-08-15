<?php

namespace NLOGCustomMessage;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerChatEvent;
use ifteam\RankManager\rank\RankProvider;
use pocketmine\event\Listener;

class Main extends PluginBase implements Listener {
	
	public $config;
	
	public function onEnable() {
		
		$this->getLogger()->info("커스텀 메세지 플러그인");
		
		@mkdir($this->getDataFolder());
		$this->config = new Config($this->getDataFolder()."view.yml", Config::YAML, [
				"availableParameter" => [
						"{CHAT}" => "플레이어가 채팅을 친 내용입니다.",
						
						"{PLAYER}" => "채팅을 친 플레이어의 이름입니다.",
						"{LEVEL}" => "채팅을 친 플레이어의 레벨명입니다.",
						"{TIME}" => "채팅을 친 시간입니다.",
						
						"{RANK}" => "채팅을 친 플레이어가 사용하는 칭호 (랭크매니저)"
				],
					
				"view" => "<{PLAYER}> {CHAT}"
		]);
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
	}
	
	public function onChat (PlayerChatEvent $ev) {
		if ($ev->isCancelled()) return;
		$config = $this->config->get("view");
		$view = str_replace([
				"{CHAT}",
				"{PLAYER}",
				"{LEVEL}",
				"{TIME}",
				"{RANK}"
		], [
				$ev->getMessage(),
				$ev->getPlayer()->getName(),
				$ev->getPlayer()->getLevel()->getName(),
				date("g:i a"),
				RankProvider::getInstance()->getRank($ev->getPlayer())->getPrefix()
		], $config);
		$ev->setCancelled(true);
		$this->getServer()->broadcastMessage($view);
	}
	
}