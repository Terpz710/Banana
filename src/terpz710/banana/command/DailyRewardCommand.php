<?php

declare(strict_types=1);

namespace terpz710\banana\command;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use terpz710\banana\Banana;

use terpz710\messages\Messages;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

class DailyRewardCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("banana.dailyreward");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        $config = new Config(Banana::getInstance()->getDataFolder() . "messages.yml");
        
        if (!$sender instanceof Player) {
            $sender->sendMessage((string) new Messages($config, "use-command-ingame"));
            return;
        }

        DailyReward::getInstance()->claimDailyReward($sender);
    }
}
