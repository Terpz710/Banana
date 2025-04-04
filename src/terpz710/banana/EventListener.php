<?php

declare(strict_types=1);

namespace terpz710\banana;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerLoginEvent;

use pocketmine\utils\Config;

use pocketmine\Server;

use terpz710\messages\Messages;

class EventListener implements Listener {

    public function join(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $name = $player->getName();
        $config = new Config(Banana::getInstance()->getDataFolder() . "messages.yml");

        if ($player->hasPlayedBefore()) {
            $event->setJoinMessage((string) new Messages($config, "join-message", ["{name}"], [$name]));
            $player->sendTitle((string) new Messages($config, "join-title", ["{name}"], [$name]));
            $player->sendSubtitle((string) new Messages($config, "join-subtitle", ["{name}"], [$name]));
        } else {
            $event->setJoinMessage((string) new Messages($config, "newcomer-message", ["{name}"], [$name]));
            $player->sendTitle((string) new Messages($config, "newcomer-title", ["{name}"], [$name]));
            $player->sendSubtitle((string) new Messages($config, "newcomer-subtitle", ["{name}"], [$name]));
        }
    }

    public function quit(PlayerQuitEvent $event) : void{
        $config = new Config(Banana::getInstance()->getDataFolder() . "messages.yml");

        $event->setQuitMessage((string) new Messages($config, "quit-message", ["{name}"], [$event->getPlayer()->getName()]));
    }

    public function login(PlayerLoginEvent $event) : void{
        $event->getPlayer()->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
    }
}