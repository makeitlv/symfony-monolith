<?php

declare(strict_types=1);

namespace App\Admin\Infrastructure\Controller\Back;

use App\Admin\Application\UseCase\Command\Create\CreateAdminCommand;
use App\Admin\Infrastructure\Query\Admin;
use App\Common\Domain\Bus\Command\CommandBusInterface;
use App\Dashboard\Infrastructure\Controller\Back\AbstractCrudController;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use RuntimeException;
use Symfony\Component\Uid\Uuid;

class AdminCrudController extends AbstractCrudController
{
    public function __construct(
        private CommandBusInterface $bus
    ) {
    }

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
            })
            ->setSearchFields(['email', 'firstname', 'lastname']);
    }

    public function persistEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        if (!$entityInstance instanceof Admin) {
            throw new RuntimeException('Wrong admin!');
        }

        $this->bus->dispatch(new CreateAdminCommand(
            Uuid::v4()->__toString(),
            $entityInstance->email,
            $entityInstance->firstname,
            $entityInstance->lastname
        ));
    }
}
