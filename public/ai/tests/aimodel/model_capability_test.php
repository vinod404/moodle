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

namespace core_ai\aimodel;

use aiprovider_openai\provider;

/**
 * Tests for AI model capabilities.
 *
 * @package    core_ai
 * @copyright  2026, Aleti Vinod Kumar <vinod.aleti@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(model_capability::class)]
final class model_capability_test extends \advanced_testcase {
    /**
     * Test text processor resolution.
     */
    public function test_text_generation_processor_resolution(): void {
        $provider = new provider(
            enabled: true,
            name: 'test',
            config: '{}',
        );

        $this->assertEquals(
            '\\aiprovider_openai\\process_generate_text',
            '\\' . model_capability::OUTPUT_TEXT->get_processor_class($provider),
        );
    }

    /**
     * Test image processor resolution.
     */
    public function test_image_generation_processor_resolution(): void {
        $provider = new provider(
            enabled: true,
            name: 'test',
            config: '{}',
        );

        $this->assertEquals(
            '\\aiprovider_openai\\process_generate_image',
            '\\' . model_capability::OUTPUT_IMAGE->get_processor_class($provider),
        );
    }

    /**
     * Test the enum contains the supported model capabilities.
     */
    public function test_model_capability_cases(): void {
        $this->assertSame([
            model_capability::OUTPUT_TEXT,
            model_capability::OUTPUT_IMAGE,
            model_capability::TOOL_CALLING,
        ], model_capability::cases());
    }
}
