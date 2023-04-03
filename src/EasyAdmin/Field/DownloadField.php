<?php

namespace App\EasyAdmin\Field;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class DownloadField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, $label = null): self 
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('easy_admin/fields/download_field.html.twig');
    }
}