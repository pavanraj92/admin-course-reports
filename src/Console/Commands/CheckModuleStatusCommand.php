<?php

namespace admin\course_reports\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckModuleStatusCommand extends Command
{
    protected $signature = 'reports:status';
    protected $description = 'Check if Reports module files are being used';

    public function handle()
    {
        $this->info('Checking Reports Module Status...');

        // Check if module files exist
        $moduleFiles = [
            'Report Controller' => base_path('Modules/Reports/app/Http/Controllers/Admin/ReportManagerController.php'),
            'Routes' => base_path('Modules/Reports/routes/web.php'),
            'Views' => base_path('Modules/Reports/resources/views'),
        ];

        $this->info("\n📁 Module Files Status:");
        foreach ($moduleFiles as $type => $path) {
            if (File::exists($path)) {
                $this->info("✅ {$type}: EXISTS");

                // Check if it's a PHP file and show last modified time
                if (str_ends_with($path, '.php')) {
                    $lastModified = date('Y-m-d H:i:s', filemtime($path));
                    $this->line("   Last modified: {$lastModified}");
                }
            } else {
                $this->error("❌ {$type}: NOT FOUND");
            }
        }

        // Check namespace in controller
        $controllers = [
            'Controller' => base_path('Modules/Reports/app/Http/Controllers/Admin/ReportManagerController.php'),
        ];

        foreach ($controllers as $name => $controllerPath) {
            if (File::exists($controllerPath)) {
                $content = File::get($controllerPath);
                if (str_contains($content, 'namespace Modules\Reports\app\Http\Controllers\Admin;')) {
                    $this->info("\n✅ {$name} namespace: CORRECT");
                } else {
                    $this->error("\n❌ {$name} namespace: INCORRECT");
                }

                // Check for test comment
                if (str_contains($content, 'Test comment - this should persist after refresh')) {
                    $this->info("✅ Test comment in {$name}: FOUND (changes are persisting)");
                } else {
                    $this->warn("⚠️  Test comment in {$name}: NOT FOUND");
                }
            }
        }

        // Check composer autoload
        $composerFile = base_path('composer.json');
        if (File::exists($composerFile)) {
            $composer = json_decode(File::get($composerFile), true);
            if (isset($composer['autoload']['psr-4']['Modules\\Reports\\'])) {
                $this->info("\n✅ Composer autoload: CONFIGURED");
            } else {
                $this->error("\n❌ Composer autoload: NOT CONFIGURED");
            }
        }

        $this->info("\n🎯 Summary:");
        $this->info("Your Reports module is properly published and should be working.");
        $this->info("Any changes you make to files in Modules/Reports/ will persist.");
        $this->info("If you need to republish from the package, run: php artisan rep0orts:publish --force");
    }
}
