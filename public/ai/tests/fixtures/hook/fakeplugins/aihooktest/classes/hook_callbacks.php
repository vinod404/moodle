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

namespace fake_aihooktest;

use core_ai\aiactions\hook_registered_action;
use core_ai\hook\after_supported_ai_actions_registration;

/**
 * Hook callbacks for supported action registry tests.
 * @package    core_ai
 * @copyright  2026, Aleti Vinod Kumar <vinod.aleti@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Register a test action through the supported actions hook.
     *
     * @param after_supported_ai_actions_registration $hook
     * @return void
     */
    public static function register_test_action(after_supported_ai_actions_registration $hook): void {
        $hook->add_action(hook_registered_action::class);
    }
}
