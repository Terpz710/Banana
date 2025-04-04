<?php

declare(strict_types=1);

namespace terpz710\banana;

use pocketmine\plugin\PluginBase;

use terpz710\banana\command\DailyRewardCommand;

use terpz710\banana\task\CooldownTask;

use poggit\libasynql\libasynql;
use poggit\libasynql\DataConnector;

use CortexPE\Commando\PacketHooker;

use DaPigGuy\libPiggyUpdateChecker\libPiggyUpdateChecker;

class Banana extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();
        $this->saveResource("messages.yml");
        $this->saveResource("daily_reward.yml");

        $this->init();

        libPiggyUpdateChecker::init($this);

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getCommandMap()->register("Banana", new DailyRewardCommand($this, "daily", "claim your daily reward!"));

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getScheduler()->scheduleRepeatingTask(new CooldownTask(), 72000);
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    protected function init() : void{
        $this->db = libasynql::create($this, $this->getConfig()->get("database"), [
            "sqlite" => "database/sqlite.sql",
            "mysql" => "database/mysql.sql"
        ]);

        $this->db->executeGeneric("table.cooldowns");
    }

    public function getDataBase() : DataConnector{
        return $this->db;
    }
}