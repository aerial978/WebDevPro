<?php

namespace src\Service;

use src\Core\Form;

class securityFormService
{
    public function RegistrationService()
    {
        $registrationForm = new Form();

        $registrationForm->startForm('post', 'registration', ['id' => 'registrationForm'])

            ->addLabelFor('username', 'Username', ['class' => 'mt-3'])
            ->addInput('username', 'username', ['class' => 'form-control', 'id' => 'username'])
            ->addError('usernameError', ['class' => 'text-danger'])

            ->addLabelFor('email', 'Email', ['class' => 'mt-3'])
            ->addInput('email', 'email', ['class' => 'form-control', 'id' => 'email'])
            ->addError('emailError', ['class' => 'text-danger'])

            ->addLabelFor('password', 'Password', ['class' => 'mt-3'])
            ->addInput('password', 'password', ['class' => 'form-control', 'id' => 'password'])
            ->addError('passwordError', ['class' => 'text-danger'])

            ->addLabelFor('password', 'Confirm password', ['class' => 'mt-3'])
            ->addInput('password', 'confirm_password', ['class' => 'form-control', 'id' => 'confirm_password'])
            ->addError('confirmPasswordError', ['class' => 'text-danger'])

            ->addButton('Submit', 'submit', 'submit', ['class' => 'btn btn-custom btn-submit w-100 p-3', 'id' => 'submit'])
            ->endForm();

        return $registrationForm;
    }
}
