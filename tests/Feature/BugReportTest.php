<?php

namespace Tests\Feature;

use App\Models\BugReport;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BugReportTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Test that a logged-in user can submit a bug report without screenshots.
     */
    public function test_logged_in_user_can_submit_bug_report(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/bug-report', [
            'title' => 'Grafik tidak muncul',
            'description' => 'Grafik di dashboard tidak tampil setelah login.',
            'reporter_name' => 'John Doe',
            'contact_info' => 'john@example.com',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('bug_reports', [
            'user_id' => $user->id,
            'title' => 'Grafik tidak muncul',
            'reporter_name' => 'John Doe',
            'contact_info' => 'john@example.com',
            'status' => 'pending',
        ]);
    }

    /**
     * Test that a bug report can include multiple screenshots.
     */
    public function test_bug_report_with_screenshots(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/bug-report', [
            'title' => 'Error pada halaman',
            'description' => 'Ada error saat membuka halaman.',
            'reporter_name' => 'Jane Doe',
            'contact_info' => '081234567890',
            'screenshots' => [
                UploadedFile::fake()->image('bug1.png', 640, 480),
                UploadedFile::fake()->image('bug2.jpg', 800, 600),
            ],
        ]);

        $response->assertRedirect();

        $bugReport = BugReport::where('title', 'Error pada halaman')->first();
        $this->assertNotNull($bugReport);
        $this->assertCount(2, $bugReport->screenshots);

        foreach ($bugReport->screenshots as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    /**
     * Test validation for required fields.
     */
    public function test_bug_report_requires_title_description_and_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/bug-report', [
            'title' => '',
            'description' => '',
            'reporter_name' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'description', 'reporter_name']);
    }

    /**
     * Test max 5 screenshots validation.
     */
    public function test_bug_report_max_five_screenshots(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/bug-report', [
            'title' => 'Too many screenshots',
            'description' => 'Testing max 5 screenshots.',
            'reporter_name' => 'Tester',
            'screenshots' => [
                UploadedFile::fake()->image('s1.png'),
                UploadedFile::fake()->image('s2.png'),
                UploadedFile::fake()->image('s3.png'),
                UploadedFile::fake()->image('s4.png'),
                UploadedFile::fake()->image('s5.png'),
                UploadedFile::fake()->image('s6.png'),
            ],
        ]);

        $response->assertSessionHasErrors('screenshots');
    }

    /**
     * Test admin can view bug reports index.
     */
    public function test_admin_can_view_bug_reports(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/bug-reports');

        $response->assertOk();
    }

    /**
     * Test admin can resolve a bug report.
     */
    public function test_admin_can_resolve_bug_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $bugReport = BugReport::create([
            'title' => 'Test Bug',
            'description' => 'Test description',
            'reporter_name' => 'Reporter',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->put("/admin/bug-reports/{$bugReport->id}/resolve");

        $response->assertRedirect();
        $this->assertDatabaseHas('bug_reports', [
            'id' => $bugReport->id,
            'status' => 'resolved',
        ]);
    }

    /**
     * Test non-admin cannot access admin bug reports.
     */
    public function test_non_admin_cannot_access_admin_bug_reports(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/bug-reports');

        $response->assertStatus(403);
    }
}
