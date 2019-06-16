<?php

/**
 * // English
 *
 * Texter, the display FloatingTextPerticle plugin for PocketMine-MP
 * Copyright (c) 2019 yuko fuyutsuki < https://github.com/fuyutsuki >
 *
 * This software is distributed under "NCSA license".
 * You should have received a copy of the MIT license
 * along with this program.  If not, see
 * < https://opensource.org/licenses/NCSA >.
 *
 * ---------------------------------------------------------------------
 * // 日本語
 *
 * TexterはPocketMine-MP向けのFloatingTextPerticleを表示するプラグインです
 * Copyright (c) 2019 yuko fuyutsuki < https://github.com/fuyutsuki >
 *
 * このソフトウェアは"MITライセンス"下で配布されています。
 * あなたはこのプログラムと共にNCSAライセンスのコピーを受け取ったはずです。
 * 受け取っていない場合、下記のURLからご覧ください。
 * < https://opensource.org/licenses/NCSA >
 */

declare(strict_types = 1);

namespace tokyo\pmmp\Texter\command\sub;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use tokyo\pmmp\libform\element\Input;
use tokyo\pmmp\libform\element\Label;
use tokyo\pmmp\libform\FormApi;
use tokyo\pmmp\Texter\Core;
use tokyo\pmmp\Texter\data\FloatingTextData;
use tokyo\pmmp\Texter\text\Text;
use tokyo\pmmp\Texter\TexterApi;

/**
 * Class TxtMove
 * @package tokyo\pmmp\Texter\command\sub
 */
class TxtMove extends TexterSubCommand {

  /** @var int response key */
  public const FT_NAME = 1;

  public function execute(string $default = ""): void {
    $pluginDescription = Core::get()->getDescription();
    $description = $this->lang->translateString("form.move.description");
    $ftName = $this->lang->translateString("form.ftname");

    $custom = FormApi::makeCustomForm(function (Player $player, ?array $response) use ($pluginDescription) {
      if (!FormApi::formCancelled($response)) {
        $level = $player->getLevel();
        if (!empty($response[self::FT_NAME])) {
          $ft = TexterApi::getFtByLevel($level, $response[self::FT_NAME]);
          if ($ft !== null) {
            if ($ft->isOwner($player)) {
              $ft
                ->setPosition(Position::fromObject($player->add(0, 2, 0), $level))
                ->sendToLevel($level, Text::SEND_TYPE_MOVE);
              FloatingTextData::make()->saveFtChange($ft);
              $message = $this->lang->translateString("command.txt.move.success", [
                $ft->getName(),
                $this->lang->translateString("form.move.here")// TODO: xyz specification
              ]);
              $player->sendMessage(TextFormat::GREEN . $pluginDescription->getPrefix() . $message);
            }else {
              $message = $this->lang->translateString("error.permission");
              $player->sendMessage(TextFormat::RED . $pluginDescription->getPrefix() . $message);
            }
          }else {
            $message = $this->lang->translateString("error.ftname.not.exists", [
              $response[self::FT_NAME]
            ]);
            $player->sendMessage(TextFormat::RED . $pluginDescription->getPrefix() . $message);
          }
        }else {
          $message = $this->lang->translateString("error.ftname.not.specified");
          $player->sendMessage(TextFormat::RED . $pluginDescription->getPrefix() . $message);
        }
      }
    });

    $custom
      ->addElement(new Label($description))
      ->addElement(new Input($ftName, $ftName, $default))
      ->setTitle($pluginDescription->getPrefix() . "/txt remove")
      ->sendToPlayer($this->player);
  }
}