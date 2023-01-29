<?php

declare(strict_types=1);

namespace App\Admin\Infrastructure\Controller\Back;

use App\Admin\Infrastructure\Query\Admin;
use App\Common\Infrastructure\Controller\Back\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

class AdminCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Admin::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field::new('uuid')
            ->onlyOnDetail();

        yield EmailField::new('email');
        yield Field::new('firstname');
        yield Field::new('lastname');

        yield Field::new('role')
            ->hideOnForm();
        yield Field::new('status')
            ->hideOnForm();
        yield Field::new('createdAt')
            ->hideOnForm();
        yield Field::new('updatedAt')
            ->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPageTitle(Crud::PAGE_DETAIL, static function (Admin $admin) {
                return $admin->email;
            })
            ->setPageTitle(Crud::PAGE_EDIT, static function (Admin $admin) {
                return sprintf('Edit %s', $admin->email);
            });
    }
}
