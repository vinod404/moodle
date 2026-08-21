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

/**
 * Test action with image capability.
 *
 * @package    core_ai
 * @copyright  2026, Aleti Vinod Kumar <vinod.aleti@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class image_feature_action extends base {
    /**
     * Store the response.
     *
     * @param responses\response_base $response
     * @return int
     */
    public function store(responses\response_base $response): int {
        return 0;
    }

    /**
     * Get the capabilities required by the action.
     *
     * @return ai_capability[]
     */
    public static function get_required_capabilities(): array {
        return [ai_capability::OUTPUT_IMAGE];
    }
}
