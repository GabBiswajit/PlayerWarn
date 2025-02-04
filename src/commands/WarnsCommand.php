<?php

/*
 * Copyright (c) 2023 AIPTU
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/AIPTU/PlayerWarn
 */

declare(strict_types=1);

namespace aiptu\playerwarn\commands;

use aiptu\playerwarn\PlayerWarn;
use aiptu\playerwarn\WarnEntry;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use function count;

class WarnsCommand extends Command implements PluginOwned {
	public function __construct(
		private PlayerWarn $plugin
	) {
		parent::__construct('warns', 'View warnings for a player');
		$this->setPermission('playerwarn.command.warns');
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
		if (!$this->testPermission($sender)) {
			return false;
		}

		$playerName = $args[0] ?? $sender->getName();
		$hasWarnings = $this->plugin->getWarns()->hasWarnings($playerName);

		if (!$hasWarnings) {
			$sender->sendMessage(TextFormat::RED . "No warnings found for {$playerName}.");
			return false;
		}

		$warns = $this->plugin->getWarns()->getWarns($playerName);
		$warningCount = count($warns);

		$message = TextFormat::AQUA . "Warnings for {$playerName} (Count: {$warningCount}):";
		foreach ($warns as $warnEntry) {
			$timestamp = $warnEntry->getTimestamp()->format(WarnEntry::DATE_TIME_FORMAT);
			$reason = $warnEntry->getReason();
			$source = $warnEntry->getSource();
			$message .= TextFormat::GRAY . "\n- Timestamp: {$timestamp} | Reason: {$reason} | Source: {$source}";
		}

		$sender->sendMessage($message);

		return true;
	}

	public function getOwningPlugin() : PlayerWarn {
		return $this->plugin;
	}
}
