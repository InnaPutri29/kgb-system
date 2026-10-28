<?php

namespace App\Providers;

use App\Support\MagicBytesMimeTypeGuesser;
use Illuminate\Filesystem\LocalFilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;
use Symfony\Component\Mime\MimeTypes;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') === 'local' && request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }

        if (!class_exists(\finfo::class)) {
            $this->tanpaFileinfo();
        }
    }

    /**
     * PHP di hosting tidak memiliki ekstensi fileinfo. Tanpa penyesuaian ini,
     * setiap unggah berkas (logo, bukti PKP, SK Final, impor Excel) gagal dengan error 500.
     */
    private function tanpaFileinfo(): void
    {
        // Validasi unggahan: jenis berkas dikenali dari byte awal isinya
        MimeTypes::getDefault()->registerGuesser(new MagicBytesMimeTypeGuesser());

        // Penyimpanan: jenis berkas dikenali dari ekstensi nama berkas
        Storage::extend('local', function ($app, array $config) {
            $visibility = PortableVisibilityConverter::fromArray(
                $config['permissions'] ?? [],
                $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PRIVATE
            );
            $links = ($config['links'] ?? null) === 'skip' ? LocalAdapter::SKIP_LINKS : LocalAdapter::DISALLOW_LINKS;
            $adapter = new LocalAdapter($config['root'], $visibility, $config['lock'] ?? LOCK_EX, $links, new ExtensionMimeTypeDetector());

            $flysystem = new Filesystem($adapter, Arr::only($config, [
                'directory_visibility', 'disable_asserts', 'retain_visibility', 'temporary_url', 'url', 'visibility',
            ]));

            return new LocalFilesystemAdapter($flysystem, $adapter, $config);
        });
    }
}
