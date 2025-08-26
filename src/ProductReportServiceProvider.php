<?php

namespace admin\course_reports;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CourseReportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes, views, migrations from the package  
        $this->loadViewsFrom([
            base_path('Modules/Reports/resources/views'), // Published module views first
            resource_path('views/admin/report'), // Published views second
            __DIR__ . '/../resources/views'      // Package views as fallback
        ], 'report');

        // Also register module views with a specific namespace for explicit usage
        if (is_dir(base_path('Modules/Reports/resources/views'))) {
            $this->loadViewsFrom(base_path('Modules/Reports/resources/views'), 'reports-module');
        }

        // Only publish automatically during package installation, not on every request
        // Use 'php artisan courses:publish' command for manual publishing
        // $this->publishWithNamespaceTransformation();

        // Standard publishing for non-PHP files
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('Modules/Reports/resources/views/'),
        ], 'report');

        $this->registerAdminRoutes();
    }

    protected function registerAdminRoutes()
    {
        if (!Schema::hasTable('admins')) {
            return; // Avoid errors before migration
        }

        $admin = DB::table('admins')
            ->orderBy('created_at', 'asc')
            ->first();

        $slug = $admin->website_slug ?? 'admin';

        Route::middleware('web')
            ->prefix("{$slug}/admin") // dynamic prefix
            ->group(function () {
                // Load routes from published module first, then fallback to package
                if (file_exists(base_path('Modules/Reports/routes/web.php'))) {
                    $this->loadRoutesFrom(base_path('Modules/Reports/routes/web.php'));
                } else {
                    $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
                }
            });
    }

    public function register()
    {
        // Register the publish command
        if ($this->app->runningInConsole()) {
            $this->commands([
                \admin\course_reports\Console\Commands\PublishCourseReportsModuleCommand::class,
                \admin\course_reports\Console\Commands\CheckModuleStatusCommand::class,
                \admin\course_reports\Console\Commands\DebugCourseReportsCommand::class,
            ]);
        }
    }

    /**
     * Publish files with namespace transformation
     */
    protected function publishWithNamespaceTransformation()
    {
        // Define the files that need namespace transformation
        $filesWithNamespaces = [
            // Controllers
            __DIR__ . '/../src/Controllers/ReportManagerController.php' => base_path('Modules/Reports/app/Http/Controllers/Admin/ReportManagerController.php'),

            // Routes
            __DIR__ . '/routes/web.php' => base_path('Modules/Reports/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                // Create destination directory if it doesn't exist
                File::ensureDirectoryExists(dirname($destination));

                // Read the source file
                $content = File::get($source);

                // Transform namespaces based on file type
                $content = $this->transformNamespaces($content, $source);

                // Write the transformed content to destination
                File::put($destination, $content);
            }
        }
    }

    /**
     * Transform namespaces in PHP files
     */
    protected function transformNamespaces($content, $sourceFile)
    {
        // Define namespace mappings
        $namespaceTransforms = [
            // Main namespace transformations
            'namespace admin\\course_reports\\Controllers;' => 'namespace Modules\\Reports\\app\\Http\\Controllers\\Admin;',

            // Use statements transformations
            'use admin\\course_reports\\Controllers\\' => 'use Modules\\Reports\\app\\Http\\Controllers\\Admin\\',

            // Class references in routes
            'admin\\course_reports\\Controllers\\ReportManagerController' => 'Modules\\Reports\\app\\Http\\Controllers\\Admin\\ReportManagerController',
        ];

        // Apply transformations
        foreach ($namespaceTransforms as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        // Handle specific file types
        if (str_contains($sourceFile, 'Controllers')) {
            $content = $this->transformControllerNamespaces($content);
        } elseif (str_contains($sourceFile, 'routes')) {
            $content = $this->transformRouteNamespaces($content);
        }

        return $content;
    }

    /**
     * Transform controller-specific namespaces
     */
    protected function transformControllerNamespaces($content)
    {
        // Update use statements for models and requests
        return $content;
    }

    /**
     * Transform model-specific namespaces
     */
    protected function transformModelNamespaces($content)
    {
        // Any model-specific transformations
        return $content;
    }

    /**
     * Transform request-specific namespaces
     */
    protected function transformRequestNamespaces($content)
    {
        // Any request-specific transformations
        return $content;
    }

    /**
     * Transform route-specific namespaces
     */
    protected function transformRouteNamespaces($content)
    {
        // Update controller references in routes
        $content = str_replace(
            'admin\\course_reports\\Controllers\\ReportManagerController',
            'Modules\\Reports\\app\\Http\\Controllers\\Admin\\ReportManagerController',
            $content
        );

        return $content;
    }
}
