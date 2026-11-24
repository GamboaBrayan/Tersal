<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create()
            ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create('/catalog')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create('/guide')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
            ->add(Url::create('/sobre-nosotros')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create('/contacto')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create('/legales')->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
            ->add(Url::create('/privacidad')->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY))
            ->add(Url::create('/libro-reclamaciones')->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY));

        \App\Models\Tire::where('status', true)->select(['id', 'updated_at'])->chunk(200, function ($tires) use ($sitemap) {
            foreach ($tires as $tire) {
                $sitemap->add(
                    Url::create("/catalog/{$tire->id}")
                        ->setLastModificationDate($tire->updated_at)
                        ->setPriority(0.8)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                );
            }
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}
