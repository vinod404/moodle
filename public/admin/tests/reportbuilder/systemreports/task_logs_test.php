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

namespace core_admin\reportbuilder\systemreports;

use advanced_testcase;
use core_admin\reportbuilder\local\systemreports\task_logs;
use core_reportbuilder\system_report_factory;
use context_system;
use core\task\database_logger;
use core_reportbuilder\task\send_schedules;

/**
 * Task logs system report class implementation
 *
 * @package    core_admin
 * @copyright  2025 Aleti Vinod Kumar <vinod.aleti@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * System report tests for Task Logs with dbreads/dbwrites filters
 *
 * @package     core_admin
 * @covers      \core_admin\reportbuilder\local\systemreports\task_logs
 */
final class task_logs_test extends advanced_testcase {
    /**
     * Summary of setUp
     * @return void
     */
    protected function setUp(): void {
        parent::setUp();
        global $PAGE;
        $PAGE->set_url('/admin/tests/reportbuilder/systemreports/task_logs_test.php');
    }

    /**
     * Helper to generate some task logs data
     *
     * @param bool $success
     * @param int $dbreads
     * @param int $dbwrites
     * @param float $timestart
     * @param float $timeend
     * @param string $logoutput
     * @param string $component
     * @param string $hostname
     * @param int $pid
     * @param int|null $userid
     * @return void
     */
    private function generate_task_log_data(
        bool $success,
        int $dbreads,
        int $dbwrites,
        float $timestart,
        float $timeend,
        string $logoutput = 'hello',
        string $component = 'moodle',
        string $hostname = 'phpunit',
        int $pid = 42,
        ?int $userid = null,
    ): void {
        $logpath = make_request_directory() . '/log.txt';
        file_put_contents($logpath, $logoutput);
        $task = new send_schedules();
        $task->set_component($component);
        $task->set_hostname($hostname);
        $task->set_pid($pid);
        if ($userid !== null) {
            $task->userid = $userid;
        }
        database_logger::store_log_for_task($task, $logpath, !$success, $dbreads, $dbwrites, $timestart, $timeend);
    }
    /**
     * Tests the system report filters for dbreads and dbwrites.
     * @return void
     */
    public function test_system_report_dbreads_dbwrites_filters(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->generate_task_log_data(true, 3, 2, 1654038000, 1654038060);
        $this->generate_task_log_data(false, 5, 1, 1654556400, 1654556700);

        $report = system_report_factory::create(task_logs::class, context_system::instance());
        $output = $report->output();
        // Without applying filters.
        $this->assertIsString($output);
        $this->assertStringContainsString('3 reads', $output);
        $this->assertStringContainsString('5 reads', $output);
        // Apply dbreads filter: LESS_THAN 4.
        $report->set_filter_values([
            'task_log:dbreads_operator' => \core_reportbuilder\local\filters\number::LESS_THAN,
            'task_log:dbreads_value1' => 4,
        ]);
        $output = $report->output();
        $this->assertIsString($output);
        $this->assertStringContainsString('3 reads', $output);
        $this->assertStringNotContainsString('5 reads', $output);
        // Apply dbwrites filter: LESS_THAN 2.
        $report->set_filter_values([
            'task_log:dbwrites_operator' => \core_reportbuilder\local\filters\number::LESS_THAN,
            'task_log:dbwrites_value1' => 2,
        ]);
        $output = $report->output();
        $this->assertIsString($output);
        $this->assertStringContainsString('1 writes', $output);
        $this->assertStringNotContainsString('2 writes', $output);
    }
}
