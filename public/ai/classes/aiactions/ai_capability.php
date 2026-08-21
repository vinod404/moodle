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

use core_ai\provider;

/**
 * Supported AI action capabilities.
 *
 * @package    core_ai
 * @copyright  2026, Aleti Vinod Kumar <vinod.aleti@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum ai_capability {
    case OUTPUT_TEXT;
    case OUTPUT_IMAGE;

    /**
     * Get the provider processor class for this capability.
     *
     * @param provider $provider
     * @return string
     */
    public function get_processor_class(provider $provider): string {
        return match ($this) {
            self::OUTPUT_TEXT => self::build_processor_class($provider, 'process_generate_text'),
            self::OUTPUT_IMAGE => self::build_processor_class($provider, 'process_generate_image'),
        };
    }

    /**
     * Build a processor class name inside the provider namespace.
     *
     * @param provider $provider
     * @param string $processorname
     * @return string
     */
    private static function build_processor_class(provider $provider, string $processorname): string {
        $pluginnamespace = strstr($provider::class, '\\', true);
        return $pluginnamespace . '\\' . $processorname;
    }
}
