<?php

declare(strict_types=1);

namespace StepUpDream\SpreadSheetConverter\DefinitionDocument\Creators;

use Illuminate\Support\Str;
use StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute;

class BladeLoader
{
    /**
     * Read the blade file.
     *
     * @param  string  $useBladeFileName
     * @param  \StepUpDream\SpreadSheetConverter\DefinitionDocument\Definitions\ParentAttribute  $parentAttribute
     * @return string
     */
    public function loadBladeFile(string $useBladeFileName, ParentAttribute $parentAttribute): string
    {
        return view('spread-sheet-converter::'.Str::snake($useBladeFileName), [
            'parentAttribute' => $parentAttribute,
        ])->render();
    }
}
