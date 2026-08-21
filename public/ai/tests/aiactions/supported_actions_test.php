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

defined('MOODLE_INTERNAL') || die();

use core\di;
use coding_exception;
use stdClass;

require_once(__DIR__ . '/fixtures/supported_actions_test_registry.php');
require_once(__DIR__ . '/fixtures/featureless_action.php');
require_once(__DIR__ . '/fixtures/text_feature_action.php');
require_once(__DIR__ . '/fixtures/image_feature_action.php');
require_once(__DIR__ . '/fixtures/multi_capability_action.php');
require_once(__DIR__ . '/fixtures/invalid_capability_action.php');
require_once(__DIR__ . '/fixtures/hook_registered_action.php');

/**
 * Tests for supported action registry.
 *
 * @package    core_ai
 * @copyright  2026, Aleti Vinod Kumar <vinod.aleti@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(supported_actions::class)]
final class supported_actions_test extends \advanced_testcase {
    /**
     * Register a fake plugin for hook tests.
     */
    protected function setup_aihooktest_plugin(): void {
        global $CFG;

        $this->add_mocked_plugintype('fake', "{$CFG->dirroot}/ai/tests/fixtures/hook/fakeplugins");
        $this->add_mocked_plugin('fake', 'aihooktest', "{$CFG->dirroot}/ai/tests/fixtures/hook/fakeplugins/aihooktest");
    }

    /**
     * Create a registry instance without core defaults.
     *
     * @return supported_actions
     */
    private function create_empty_registry(): supported_actions {
        return new supported_actions_test_registry();
    }

    /**
     * Create a registry with the configured hook manager.
     *
     * @return supported_actions
     */
    private function create_registry(): supported_actions {
        return new supported_actions(di::get(\core\hook\manager::class));
    }

    /**
     * Test core registry contents.
     */
    public function test_default_core_actions_are_registered(): void {
        $registry = $this->create_registry();

        $this->assertSame([
            summarise_text::class,
            explain_text::class,
            generate_text::class,
            generate_image::class,
        ], $registry->get_actions_from_capabilities([
            ai_capability::OUTPUT_TEXT,
            ai_capability::OUTPUT_IMAGE,
        ]));
    }

    /**
     * Test add_action registers a valid AI action.
     */
    public function test_add_action_registers_valid_ai_action(): void {
        $registry = $this->create_empty_registry();

        $registry->add_action(text_feature_action::class);

        $this->assertSame([
            text_feature_action::class,
        ], $registry->get_actions_from_capabilities([
            ai_capability::OUTPUT_TEXT,
        ]));
    }

    /**
     * Test add_action rejects non AI action classes.
     */
    public function test_add_action_rejects_non_ai_action(): void {
        $registry = $this->create_empty_registry();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Unsupported AI action class: stdClass');

        $registry->add_action(stdClass::class);
    }

    /**
     * Test add_action rejects invalid required capabilities.
     */
    public function test_add_action_rejects_invalid_capabilities(): void {
        $registry = $this->create_empty_registry();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid capability returned by ' . invalid_capability_action::class);

        $registry->add_action(invalid_capability_action::class);
    }

    /**
     * Test text capability filtering.
     */
    public function test_get_actions_from_capabilities_for_text(): void {
        $registry = $this->create_registry();

        $this->assertSame([
            summarise_text::class,
            explain_text::class,
            generate_text::class,
        ], $registry->get_actions_from_capabilities([
            ai_capability::OUTPUT_TEXT,
        ]));
    }

    /**
     * Test image capability filtering.
     */
    public function test_get_actions_from_capabilities_for_image(): void {
        $registry = $this->create_registry();

        $this->assertSame([
            generate_image::class,
        ], $registry->get_actions_from_capabilities([
            ai_capability::OUTPUT_IMAGE,
        ]));
    }

    /**
     * Test multi-capability actions require all capabilities.
     */
    public function test_get_actions_from_capabilities_requires_all_capabilities(): void {
        $registry = $this->create_empty_registry();
        $registry->add_action(multi_capability_action::class);

        $this->assertSame([], $registry->get_actions_from_capabilities([
            ai_capability::OUTPUT_TEXT,
        ]));

        $this->assertSame([
            multi_capability_action::class,
        ], $registry->get_actions_from_capabilities([
            ai_capability::OUTPUT_TEXT,
            ai_capability::OUTPUT_IMAGE,
        ]));
    }

    /**
     * Test empty capability list.
     */
    public function test_get_actions_from_capabilities_with_empty_capability_list(): void {
        $registry = $this->create_registry();

        $this->assertSame([], $registry->get_actions_from_capabilities([]));
    }

    /**
     * Test no matching capabilities.
     */
    public function test_get_actions_from_capabilities_with_no_matches(): void {
        $registry = $this->create_empty_registry();

        $registry->add_action(featureless_action::class);
        $registry->add_action(text_feature_action::class);

        $this->assertSame([], $registry->get_actions_from_capabilities([
            ai_capability::OUTPUT_IMAGE,
        ]));
    }

    /**
     * Test hook registration.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_hook_registration_can_add_actions(): void {
        $this->resetAfterTest(true);

        $this->setup_aihooktest_plugin();
        di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'fake_aihooktest' => __DIR__ . '/../fixtures/hook/fakeplugins/aihooktest/db/hooks.php',
            ]),
        );

        require_once(__DIR__ . '/../fixtures/hook/fakeplugins/aihooktest/classes/hook_callbacks.php');

        $registry = $this->create_registry();

        $this->assertContains(
            hook_registered_action::class,
            $registry->get_actions_from_capabilities([ai_capability::OUTPUT_TEXT]),
        );
    }
}
