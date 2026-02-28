<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email'),
            TextField::new('username'),
            ChoiceField::new('roles')
                ->setChoices(['ROLE_ADMIN' => 'ROLE_ADMIN', 'ROLE_USER' => 'ROLE_USER'])
                ->allowMultipleChoices()
                ->renderExpanded(),
            TextField::new('password')
                ->setFormType(PasswordType::class)
                ->hideOnIndex()
                ->setRequired($pageName === 'new'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        $this->hashPasswordIfNeeded($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        $this->hashPasswordIfNeeded($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPasswordIfNeeded(mixed $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }
        $plainPassword = $entityInstance->getPassword();
        if ($plainPassword && !str_starts_with($plainPassword, '$2y$') && !str_starts_with($plainPassword, '$argon')) {
            $hashed = $this->passwordHasher->hashPassword($entityInstance, $plainPassword);
            $entityInstance->setPassword($hashed);
        }
    }
}
