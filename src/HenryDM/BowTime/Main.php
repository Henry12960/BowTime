<?php 

namespace HenryDM\BowTime;

/**
 * Adapted from the Wizardry License
 *
 * Copyright (c) 2022 HenryDM and Taco
 *
 * Permission is hereby granted to any persons and/or organizations
 * using this software to copy, modify, merge, publish, and distribute it.
 * Said persons and/or organizations are not allowed to use the software or
 * any derivatives of the work for commercial use or any other means to generate
 * income, nor are they allowed to claim this software as their own.
 *
 * The persons and/or organizations are also disallowed from sub-licensing
 * and/or trademarking this software without explicit permission from HenryDM and Taco.
 *
 * Any persons and/or organizations using this software must disclose their
 * source code and have it publicly available, include this license,
 * provide sufficient credit to the original authors of the project (IE: HenryDM and Taco),
 * as well as provide a link to the original project.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,FITNESS FOR A PARTICULAR
 * PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
 
# Entity Events
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\ProjectileLaunchEvent;

# Login and Quit Event
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

# Other Events
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

use function str_replace;

class Main extends PluginBase implements Listener  {

    /*** @var array */
    private array $cooldown = [];

    /*** @var array */
    private array $settings = [];

    public function onEnable() : void {
		
        $this->settings = $this->getConfig()->getAll();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
	    $this->saveDefaultConfig();
    }

    /**
     * @param PlayerLoginEvent $event
     */
    public function onLogin(PlayerLoginEvent $event) : void {
        $this->cooldown[$event->getPlayer()->getName()] = "0.00";
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event) : void{
        unset($this->cooldown[$event->getPlayer()->getName()]);
    }
	
    /**
     * @param ProjectileLaunchEvent $event
     */
    public function onLaunch(ProjectileLaunchEvent $event) : void {
        $entity = $event->getEntity();
        if ($entity instanceof Arrow) {
            $player = $entity->getOwningEntity();
            if ($player instanceof Player) {
                $time = microtime(true) - (float)$this->cooldown[$player->getName()];
                $settings = $this->settings;
                if ($time < (float)$settings["cooldown"]) {
                    $settings["pop-up"] ? $player->sendPopup(str_replace("{TIME}", $time, $settings["message"])) : $player->sendMessage(str_replace("{TIME}", $time, $settings["message"]));
                    $event->cancel();
                    return;
                }
                $this->cooldown[$player->getName()] = (string)microtime(true);
            }
        }
    }
}
