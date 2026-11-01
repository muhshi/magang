<?php

namespace Tests\Feature;

use App\Filament\Widgets\InternshipBarChart;
use App\Filament\Widgets\InternshipPieChart;
use App\Models\Attendance;
use App\Models\Internship;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    public function test_intern_dashboard_source_uses_created_at_not_start_time(): void
    {
        $fileContent = file_get_contents(app_path('Filament/Widgets/InternDashboard.php'));
        $this->assertStringNotContainsString("->where('start_time', '>=', \$bulanIni)", $fileContent);
        $this->assertStringContainsString("created_at", $fileContent);
    }

    public function test_internship_bar_chart_does_not_contain_blog_posts_label(): void
    {
        $fileContent = file_get_contents(app_path('Filament/Widgets/InternshipBarChart.php'));
        $this->assertStringNotContainsString("'label' => 'Blog posts'", $fileContent);
        $this->assertStringContainsString("Pendaftar", $fileContent);
    }

    public function test_stats_overview_does_not_rely_on_legacy_project_or_ticket_statuses(): void
    {
        $fileContent = file_get_contents(app_path('Filament/Widgets/StatsOverview.php'));
        $this->assertStringNotContainsString("Project::count()", $fileContent);
        $this->assertStringNotContainsString("ticket_statuses", $fileContent);
    }
}
