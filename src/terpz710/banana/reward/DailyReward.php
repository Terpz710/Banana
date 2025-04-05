<?php

declare(strict_types=1);

namespace terpz710\banana\reward;

use pocketmine\player\Player;

use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\enchantment\EnchantmentInstance;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use terpz710\banana\Banana;

use terpz710\messages\Messages;

class DailyReward {
    use SingletonTrait;

    public function hasClaimedDailyReward(Player $player, callable $callback) : void{
        $name = strtolower($player->getName());
        Banana::getInstance()->getDataBase()->executeSelect("cooldowns.get", ["username" => $name], function(array $rows) use ($callback) {
            if (isset($rows[0])) {
                $last = (int)$rows[0]["last_claim"];
                $callback(time() - $last < 86400);
            } else {
                $callback(false);
            }
        });
    }

    public function claimDailyReward(Player $player) : void{
        $config = new Config(Banana::getInstance()->getDataFolder() . "messages.yml");

        $this->hasClaimedDailyReward($player, function(bool $claimed) use ($player) {
            if ($claimed) {
                $player->sendMessage((string) new Messages($config, "already-claimed-dailyreward"));
                return;
            }

            $rewards = new Config(Banana::getInstance()->getDataFolder() . "daily_reward.yml");
            $items = $rewards->get("daily")["items"] ?? [];

            foreach ($items as $data) {
                if (!isset($data["item"])) continue;

                $item = StringToItemParser::getInstance()->parse($data["item"]);
                if (!$item instanceof Item) continue;

                $item->setCount((int)($data["amount"] ?? 1));

                if (isset($data["name"])) {
                    $item->setCustomName($data["name"]);
                }

                if (isset($data["enchantments"]) && is_array($data["enchantments"])) {
                    foreach ($data["enchantments"] as $enchantString) {
                        $parts = explode(":", $enchantString);
                        $enchant = StringToEnchantmentParser::getInstance()->parse($parts[0]);
                        $level = isset($parts[1]) ? (int)$parts[1] : 1;

                        if ($enchant !== null) {
                            $item->addEnchantment(new EnchantmentInstance($enchant, $level));
                        }
                    }
                }

                $player->getInventory()->addItem($item);
            }

            $player->sendMessage((string) new Messages($config, "claimed-dailyreward"));

            Banana::getInstance()->getDataBase()->executeChange("cooldowns.set", [
                "username" => strtolower($player->getName()),
                "last_claim" => time()
            ]);
        });
    }
}
