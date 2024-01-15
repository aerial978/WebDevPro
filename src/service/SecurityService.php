<?php

namespace src\Service;

use src\Core\Form;

class SecurityService
{
    public function RegistrationService()
    {
        $registrationForm = new Form();

        $registrationForm->startForm('post', 'registration', ['id' => 'registrationForm'])
            ->addLabelFor('email', 'Email', ['class' => 'mt-3'])
            ->addInput('email', 'email', ['class' => 'form-control', 'id' => 'email'])
            ->addError('emailError', ['class' => 'text-danger'])
            ->addLabelFor('password', 'Password', ['class' => 'mt-3'])
            ->addInput('password', 'password', ['class' => 'form-control', 'id' => 'password'])
            ->addError('passwordError', ['class' => 'text-danger'])
            ->addLabelFor('password', 'Confirm password', ['class' => 'mt-3'])
            ->addInput('password', 'confirm_password', ['class' => 'form-control', 'id' => 'confirm_password'])
            ->addError('confirmPasswordError', ['class' => 'text-danger'])
            ->addButton('Submit', 'submit', ['class' => 'btn btn-custom btn-submit w-100 p-3', 'id' => 'submit'])
            ->endForm();

        return $registrationForm;
    }

    public function LoginService()
    {
        $loginForm = new Form();

        $loginForm->startForm('post', 'login', ['id' => 'loginForm'])
            ->addLabelFor('email', 'Email', ['class' => 'mt-3'])
            ->addInput('email', 'email', ['class' => 'form-control', 'id' => 'email'])
            ->addLabelFor('password', 'Password', ['class' => 'mt-3'])
            ->addInput('password', 'password', ['class' => 'form-control', 'id' => 'password'])
            ->addButton('Submit', 'submit', ['class' => 'btn btn-custom btn-submit w-100 p-3', 'id' => 'submit'])
            ->endForm();

        return $loginForm;
    }
}
