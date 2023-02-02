<?php

declare(strict_types=1);

namespace App\Admin\Infrastructure\Controller\Back;

use App\Admin\Application\UseCase\Command\Activate\ActivateAdminCommand;
use App\Admin\Application\UseCase\Command\Block\BlockAdminCommand;
use App\Admin\Application\UseCase\Command\Create\CreateAdminCommand;
use App\Admin\Application\UseCase\Command\Delete\DeleteAdminCommand;
use App\Admin\Application\UseCase\Command\NewPassword\NewAdminPasswordCommand;
use App\Admin\Application\UseCase\Command\Update\UpdateAdminCommand;
use App\Admin\Infrastructure\Query\Admin;
use App\Common\Domain\Bus\Command\CommandBusInterface;
use App\Dashboard\Infrastructure\Controller\Back\AbstractCrudController;
use App\Dashboard\Infrastructure\Settings\DashboardSettings;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Uid\Uuid;
use RuntimeException;
use Throwable;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class AdminCrudController extends AbstractCrudController
{
    public function __construct(
        private RequestStack $requestStack,
        private AdminUrlGenerator $adminUrlGenerator,
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

    public function configureActions(Actions $actions): Actions
    {
        $activateAction = Action::new('activate')
            ->linkToUrl(function() {
                $request = $this->requestStack->getCurrentRequest();

                if (!$request instanceof Request) {
                    throw new RuntimeException('Wrong request!');
                }

                return $this->adminUrlGenerator->setAll($request->query->all())
                    ->setAction('activate')
                    ->generateUrl();
            })
            ->addCssClass('btn')
            ->setIcon('fa fa-lock-open')
            ->displayIf(static function(Admin $admin): bool {
                return $admin->status !== 'activated';
            });

        $blockAction = Action::new('block')
            ->linkToUrl(function() {
                $request = $this->requestStack->getCurrentRequest();

                if (!$request instanceof Request) {
                    throw new RuntimeException('Wrong request!');
                }

                return $this->adminUrlGenerator->setAll($request->query->all())
                    ->setAction('block')
                    ->generateUrl();
            })
            ->addCssClass('btn')
            ->setIcon('fa fa-lock')
            ->displayIf(static function(Admin $admin): bool {
                return $admin->status !== 'blocked';
            });

        $resetPassword = Action::new('resetPassword')
            ->linkToUrl(function() {
                $request = $this->requestStack->getCurrentRequest();

                if (!$request instanceof Request) {
                    throw new RuntimeException('Wrong request!');
                }

                return $this->adminUrlGenerator->setAll($request->query->all())
                    ->setAction('resetPassword')
                    ->generateUrl();
            })
            ->addCssClass('btn')
            ->setIcon('fa fa-passport');

        return parent::configureActions($actions)
            ->add(Crud::PAGE_DETAIL, $activateAction)
            ->add(Crud::PAGE_DETAIL, $blockAction)
            ->add(Crud::PAGE_DETAIL, $resetPassword)
            ->reorder(
                Crud::PAGE_DETAIL,
                [
                    Action::EDIT,
                    'activate',
                    'block',
                    'resetPassword',
                    Action::DELETE,
                    Action::INDEX
                ]
            );
    }

    public function persist(EntityManagerInterface $entityManager, mixed $entityInstance): void
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

    public function update(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        if (!$entityInstance instanceof Admin) {
            throw new RuntimeException('Wrong admin!');
        }

        $this->bus->dispatch(new UpdateAdminCommand(
            $entityInstance->uuid,
            $entityInstance->email,
            $entityInstance->firstname,
            $entityInstance->lastname
        ));
    }

    public function remove(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        if (!$entityInstance instanceof Admin) {
            throw new RuntimeException('Wrong admin!');
        }

        $this->bus->dispatch(new DeleteAdminCommand(
            $entityInstance->uuid
        ));
    }

    public function activate(
        AdminContext $adminContext,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        return $this->action($adminContext, $adminUrlGenerator, 'activate');
    }

    public function block(
        AdminContext $adminContext,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        return $this->action($adminContext, $adminUrlGenerator, 'block');
    }

    public function resetPassword(
        AdminContext $adminContext,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        return $this->action($adminContext, $adminUrlGenerator, 'resetPassword');
    }

    private function action(
        AdminContext $adminContext,
        AdminUrlGenerator $adminUrlGenerator,
        string $type
    ): Response {
        $entityInstance = $adminContext->getEntity()->getInstance();
        if (!$entityInstance instanceof Admin) {
            throw new RuntimeException(sprintf('Entity is missing or not a %s.', Admin::class));
        }

        try {
            if ($type === 'activate') {
                $this->bus->dispatch(new ActivateAdminCommand(
                    $entityInstance->uuid
                ));

                $this->addFlash(
                    'success',
                    new TranslatableMessage(
                        'Admin activated.',
                        [],
                        DashboardSettings::ADMIN_TRANSLATION_DOMAIN
                    )
                );
            }

            if ($type === 'block') {
                $this->bus->dispatch(new BlockAdminCommand(
                    $entityInstance->uuid
                ));

                $this->addFlash(
                    'success',
                    new TranslatableMessage(
                        'Admin blocked.',
                        [],
                        DashboardSettings::ADMIN_TRANSLATION_DOMAIN
                    )
                );
            }

            if ($type === 'resetPassword') {
                $this->bus->dispatch(new NewAdminPasswordCommand(
                    $entityInstance->uuid
                ));

                $this->addFlash(
                    'success',
                    new TranslatableMessage(
                        'Password reset.',
                        [],
                        DashboardSettings::ADMIN_TRANSLATION_DOMAIN
                    )
                );
            }
        } catch (DomainException $exception) {
            $this->addFlash(
                'danger',
                new TranslatableMessage(
                    $exception->getMessage(),
                    [],
                    DashboardSettings::ADMIN_TRANSLATION_DOMAIN
                )
            );
        } catch (Throwable $exception) {
            if ($exception instanceof RuntimeException) {
                throw $exception;
            }

            $this->addFlash(
                'danger',
                new TranslatableMessage(
                    'Something went wrong! Please, contact with administrator to figure out.',
                    [],
                    DashboardSettings::ADMIN_TRANSLATION_DOMAIN
                )
            );
        }

        $targetUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Crud::PAGE_DETAIL)
            ->setEntityId($entityInstance->uuid)
            ->generateUrl();

        return $this->redirect($targetUrl);
    }
}
