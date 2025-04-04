<?php

declare(strict_types=1);

namespace terpz710\banana\task;

use pocketmine\scheduler\Task;

use terpz710\banana\Banana;

class CooldownTask extends Task {

    public function onRun() : void{
        $time = time() - 86400;

        Banana::getInstance()->getDataBase()->executeChange("cooldowns.cleanup", ["time" => $time]);
    }
}