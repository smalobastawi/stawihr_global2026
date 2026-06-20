<?php

namespace App\Console\Commands;

use App\Support\AppVersion;
use Illuminate\Console\Command;

class StampVersionCommand extends Command
{
    protected $signature = 'app:stamp-version
                            {version : Semantic version e.g. 2.5.0 or v2.5.0}
                            {--ref= : Git ref (tag or branch)}
                            {--commit= : Git commit SHA}';

    protected $description = 'Write VERSION.json for deployment and upgrade tracking';

    public function handle(): int
    {
        $version = ltrim($this->argument('version'), 'vV');
        $ref = $this->option('ref') ?: ('v'.$version);
        $commit = $this->option('commit') ?: $this->detectCommitSha();

        AppVersion::write([
            'version' => $version,
            'git_ref' => $ref,
            'commit' => $commit,
            'built_at' => now()->toIso8601String(),
            'repository' => env('GITHUB_HR_REPOSITORY', 'smalobastawi/stawihr_global2026'),
        ]);

        $this->info("VERSION.json stamped: {$version} ({$ref}) @ ".($commit ? substr($commit, 0, 7) : 'unknown'));

        return self::SUCCESS;
    }

    protected function detectCommitSha(): ?string
    {
        $output = shell_exec('git rev-parse HEAD 2>/dev/null');

        return $output ? trim($output) : null;
    }
}
