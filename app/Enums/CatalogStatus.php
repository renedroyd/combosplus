<?php

namespace App\Enums;

enum CatalogStatus: string
{
    case Draft = 'draft';
    case Unpublished = 'unpublished';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::Unpublished => 'No publicado',
            self::Published => 'Publicado',
        };
    }
}
