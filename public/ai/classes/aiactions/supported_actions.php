<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

declare(strict_types=1);

namespace core_ai\aiactions;

use coding_exception;
use core\hook\manager;
use core_ai\hook\after_supported_ai_actions_registration;

/**
 * Registry of supported AI actions.
 *
 * @package    core_ai
 * @copyright  2026, Aleti Vinod Kumar <vinod.aleti@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class supported_actions {
    /** @var string[] $actions */
    protected array $actions = [];

    /** @var manager $hookmanager The shared hook manager. */
    private readonly manager $hookmanager;

    /**
     * Constructor.
     *
     * @param manager $hookmanager The shared hook manager, resolved by the DI container.
     */
    public function __construct(manager $hookmanager) {
        $this->hookmanager = $hookmanager;
        $this->add_action(summarise_text::class);
        $this->add_action(explain_text::class);
        $this->add_action(generate_text::class);
        $this->add_action(generate_image::class);

        $this->hookmanager->dispatch(new after_supported_ai_actions_registration($this));
    }

    /**
     * Register an action class.
     *
     * @param string $classname
     * @return void
     */
    public function add_action(string $classname): void {
        if (!is_a($classname, base::class, true)) {
            throw new coding_exception('Unsupported AI action class: ' . $classname);
        }

        foreach ($classname::get_required_capabilities() as $capability) {
            if (!$capability instanceof ai_capability) {
                throw new coding_exception('Invalid capability returned by ' . $classname);
            }
        }

        if (!in_array($classname, $this->actions, true)) {
            $this->actions[] = $classname;
        }
    }

    /**
     * Return actions whose required capabilities are all in the requested capabilities.
     *
     * @param ai_capability[] $capabilities
     * @return string[]
     */
    public function get_actions_from_capabilities(array $capabilities): array {
        if ($capabilities === []) {
            return [];
        }

        return array_values(array_filter($this->actions, static function (string $actionclass) use ($capabilities): bool {
            $requiredcapabilities = $actionclass::get_required_capabilities();
            if ($requiredcapabilities === []) {
                return false;
            }

            foreach ($requiredcapabilities as $actioncapability) {
                if (!in_array($actioncapability, $capabilities, true)) {
                    return false;
                }
            }

            return true;
        }));
    }
}
