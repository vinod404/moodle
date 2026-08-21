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

namespace core_ai\hook;

use core_ai\aiactions\supported_actions;

/**
 * Hook triggered after the supported AI actions registry is initialised.
 *
 * @package    core_ai
 * @copyright  2026, Aleti Vinod Kumar <vinod.aleti@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins to register additional supported AI actions after the core registry is initialised.')]
#[\core\attribute\tags('ai')]
final class after_supported_ai_actions_registration {
    /**
     * Constructor.
     *
     * @param supported_actions $supportedactions The supported actions registry.
     */
    public function __construct(
        /** @var supported_actions The supported actions registry. */
        private readonly supported_actions $supportedactions,
    ) {
    }

    /**
     * Register an action in the supported actions registry.
     *
     * @param string $classname
     * @return void
     */
    public function add_action(string $classname): void {
        $this->supportedactions->add_action($classname);
    }
}
