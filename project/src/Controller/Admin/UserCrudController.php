<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Пользователя')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавить пользователя')
            ->setPageTitle('edit', fn(User $user) => sprintf('Редактировать : " %s "', $user->getUserIdentifier()))
            ->setEntityLabelInPlural('Пользователи');
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email')
                ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                ->setTextAlign('left')
                ->setDisabled(),

            FormField::addRow(),

            TextField::new('password')
                ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                ->setTextAlign('center')
                ->setDisabled()
                ->onlyOnForms(),

            FormField::addRow(),

            ChoiceField::new('roles', 'Роли')
                ->setChoices(User::availableUserRole())
                ->allowMultipleChoices()
                ->setColumns('col-sm-6 col-lg-5 col-xxl-3')
                ->setTextAlign('center')
                ->setDisabled()
        ];
    }
}
