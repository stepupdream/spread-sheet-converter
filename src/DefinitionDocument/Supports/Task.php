<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports;

use Illuminate\Console\View\Components\Mutators\EnsureDynamicContentIsHighlighted;
use Illuminate\Console\View\Components\Mutators\EnsureNoPunctuation;
use Illuminate\Console\View\Components\Mutators\EnsureRelativePaths;
use Illuminate\Console\View\Components\Task as BaseTask;
use Symfony\Component\Console\Output\OutputInterface;
use function Termwind\terminal;

class Task extends BaseTask
{
    /**
     * Renders the component using the given arguments.
     *
     * @param  string  $description
     * @param  (callable(): bool)|null  $task
     * @param  int  $verbosity
     * @return void
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    public function render($description, $task = null, $verbosity = OutputInterface::VERBOSITY_NORMAL): void
    {
        $description = $this->mutate($description, [
            EnsureDynamicContentIsHighlighted::class,
            EnsureNoPunctuation::class,
            EnsureRelativePaths::class,
        ]);

        $descriptionWidth = mb_strlen($description);

        $this->output->write("  $description ", false, $verbosity);

        $startTime = microtime(true);

        $result = false;

        try {
            $result = ($task ?: fn () => true)();
        } finally {
            $runTime = $task ? (' '.number_format((microtime(true) - $startTime) * 1000).'ms') : '';

            $runTimeWidth = mb_strlen($runTime);
            $width = min(terminal()->width(), 150);
            $dots = max($width - $descriptionWidth - $runTimeWidth - 10, 0);

            $this->output->write(str_repeat('<fg=gray>.</>', $dots), false, $verbosity);
            $this->output->write("<fg=gray>$runTime</>", false, $verbosity);

            switch ($result) {
                case 'SKIP':
                    $this->output->writeln(' <fg=yellow;options=bold>SKIP</>', $verbosity);
                    break;
                case 'DONE':
                    $this->output->writeln(' <fg=green;options=bold>DONE</>', $verbosity);
                    break;
                default:
                    $this->output->writeln(' <fg=red;options=bold>ERROR</>', $verbosity);
            }
        }
    }
}
