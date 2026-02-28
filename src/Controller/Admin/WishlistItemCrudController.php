<?php

namespace App\Controller\Admin;

use App\Entity\WishlistItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class WishlistItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WishlistItem::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user')->autocomplete(),
            AssociationField::new('game')->autocomplete(),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
}
