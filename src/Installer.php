<?php

/**
 * Installer.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-skills
 */

declare(strict_types=1);

namespace X3P0\Skills;

use Composer\Script\Event;
use RuntimeException;

class Installer
{
	/**
	 * The default destination path relative to the project root.
	 */
	private const DEFAULT_PATH = '.claude/skills';

	/**
	 * The source folder within the package that contains the skills.
	 */
	private const SKILLS_DIR = '.claude/skills';

	/**
	 * Composer post-install-cmd and post-update-cmd entry point. Copies all
	 * skills from the package's skills/ directory into the consuming project's
	 * .claude/skills/ directory. Existing skill folders are overwritten on
	 * each run.
	 */
	public static function install(Event $event): void
	{
		$composer   = $event->getComposer();
		$io         = $event->getIO();
		$extra      = $composer->getPackage()->getExtra();
		$vendorDir  = $composer->getConfig()->get('vendor-dir');
		$projectDir = dirname($vendorDir);

		// Resolve the destination path from extra config or use the default.
		$destination = $extra['x3p0']['skills']['path'] ?? self::DEFAULT_PATH;
		$destination = rtrim($projectDir . '/' . $destination, '/');

		// Resolve the source path inside the installed package.
		$source = $vendorDir . '/x3p0-dev/x3p0-skills/' . self::SKILLS_DIR;

		if (!is_dir($source)) {
			$io->writeError('<warning>x3p0-skills: skills directory not found at ' . $source . '</warning>');
			return;
		}

		if (!is_dir($destination) && !mkdir($destination, 0755, true)) {
			throw new RuntimeException('x3p0-skills: failed to create destination directory: ' . $destination);
		}

		$skills = array_filter(
			scandir($source),
			static fn(string $entry) => $entry !== '.' && $entry !== '..' && is_dir($source . '/' . $entry)
		);

		foreach ($skills as $skill) {
			$skillSource      = $source . '/' . $skill;
			$skillDestination = $destination . '/' . $skill;

			static::removeDirectory($skillDestination);
			static::copyDirectory($skillSource, $skillDestination);

			$io->write('<info>x3p0-skills: installed ' . $skill . '</info>');
		}
	}

	/**
	 * Recursively copies a directory from source to destination.
	 */
	private static function copyDirectory(string $source, string $destination): void
	{
		if (!is_dir($destination) && !mkdir($destination, 0755, true)) {
			throw new RuntimeException('x3p0-skills: failed to create directory: ' . $destination);
		}

		$entries = array_filter(
			scandir($source),
			static fn(string $entry) => $entry !== '.' && $entry !== '..'
		);

		foreach ($entries as $entry) {
			$sourcePath      = $source . '/' . $entry;
			$destinationPath = $destination . '/' . $entry;

			if (is_dir($sourcePath)) {
				static::copyDirectory($sourcePath, $destinationPath);
			} else {
				copy($sourcePath, $destinationPath);
			}
		}
	}

	/**
	 * Recursively removes a directory and its contents.
	 */
	private static function removeDirectory(string $path): void
	{
		if (!is_dir($path)) {
			return;
		}

		$entries = array_filter(
			scandir($path),
			static fn(string $entry) => $entry !== '.' && $entry !== '..'
		);

		foreach ($entries as $entry) {
			$entryPath = $path . '/' . $entry;

			if (is_dir($entryPath)) {
				static::removeDirectory($entryPath);
			} else {
				unlink($entryPath);
			}
		}

		rmdir($path);
	}
}
