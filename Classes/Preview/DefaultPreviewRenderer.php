<?php

namespace PAGEmachine\Searchable\Preview;

/*
 * This file is part of the PAGEmachine Searchable project.
 */

/**
 * Default preview renderer.
 */
class DefaultPreviewRenderer extends AbstractPreviewRenderer implements PreviewRendererInterface
{
    /**
     * Renders the preview
     *
     * @param  array $record
     */
    public function render($record): string
    {
        return implode(', ', $record);
    }
}
