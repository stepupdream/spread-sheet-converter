<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Supports;

use Illuminate\Console\OutputStyle;

abstract class LineMessage
{
    /**
     * The output style implementation.
     *
     * @var \Illuminate\Console\OutputStyle
     */
    protected OutputStyle $output;

    /**
     * @var bool whether it is the first time to write.
     */
    protected bool $isFirstTime = true;

    /**
     * Set the output implementation that should be used by the console.
     *
     * @param  \Illuminate\Console\OutputStyle  $output
     * @return $this
     */
    public function setOutput(OutputStyle $output): static
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Output console log.
     *
     * @param  string  $content
     * @param  string  $bgColor
     * @param  string  $fgColor
     * @param  string  $title
     * @return void
     */
    public function write(string $content, string $title, string $bgColor = 'blue', string $fgColor = 'white'): void
    {
        if ($this->isFirstTime) {
            $this->isFirstTime = false;
            $this->output->writeln(sprintf("\n <bg=%s;fg=%s> %s </> %s", $bgColor, $fgColor, $title, $content));
        } else {
            $this->output->writeln(sprintf(' <bg=%s;fg=%s> %s </> %s', $bgColor, $fgColor, $title, $content));
        }
    }
}
