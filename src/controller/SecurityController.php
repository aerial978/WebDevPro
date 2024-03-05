<?php

namespace src\controller;

use src\Core\Form;
use src\Models\UserModel;
use src\Constants\ErrorMessage;
use src\Service\SecurityFormService;

class SecurityController extends BaseController
{
    /**
     * Gère le processus d'inscription de l'utilisateur.
     *
     * Cette méthode vérifie les données d'inscription reçues, valide les champs du formulaire,
     * et enregistre un nouvel utilisateur si aucune erreur n'est détectée.
     *
     * @return void
     */
    public function handleRegistration()
    {
        $errors = [];

        try {
            if (isset($_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
                if (Form::validate($_POST, ['email', 'password'])) {
                    $email = trim(htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'));
                    $password = trim(htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8'));
                    $confirmPassword = trim($_POST['confirm_password']);

                    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors['email'] = ErrorMessage::EMAIL_INVALID;
                    }

                    $regexPassword = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$#!%*?&])[A-Za-z\d@$#!%*?&]{12,}$/';
                    if (empty($password) || !preg_match($regexPassword, $password)) {
                        $errors['password'] = ErrorMessage::PASSWORD_INVALID;
                    }

                    if (empty($confirmPassword) || $confirmPassword !== $password) {
                        $errors['password_confirm'] = ErrorMessage::PASSWORD_MISMATCH;
                    }

                    if (empty($errors)) {
                        $user = new UserModel();
                        if ($user->findOneByEmail($email)) {
                            $errors['emailexist'] = ErrorMessage::EMAIL_ALREADYEXISTS;
                        } else {
                            $password = password_hash($_POST['password'], PASSWORD_ARGON2I);

                            $user->setEmail($email)
                                ->setPassword($password);

                            $user->create();
                        }
                    }
                }
            }

            $securityFormService = new SecurityFormService();
            $registrationForm = $securityFormService->registrationService();

            $this->twig->display('frontend/Security/registration.html.twig', [
                'errors' => $errors,
                'registrationForm' => $registrationForm->create()
            ]);
        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }

    /**
    * Gère le processus de connexion de l'utilisateur.
    *
    * Cette méthode vérifie les données de connexion reçues et authentifie l'utilisateur.
    *
    * @return void
    */
    public function handleLogin()
    {
        $errors = [];

        try {
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!empty($_POST['email']) || !empty($_POST['password'])) {
                    $userModel = new UserModel();
                    $userArray = $userModel->findOneByEmail(strip_tags($_POST['email']));

                    if ($userArray) {
                        $user = $userModel->hydrate($userArray);
                        $hashPassword = $user->getPassword();
                        if (password_verify($_POST['password'], $hashPassword)) {
                            $user->setSession();
                            header('Location: admin/dashboard');
                        } else {
                            $errors['error'] = 'Invalid email address or/and password !';
                        }
                    } else {
                        $errors['error'] = 'Invalid email address or/and password !';
                    }
                } else {
                    $errors['error'] = 'Invalid email address or/and password !';
                }
            }

            $securityFormService = new SecurityFormService();
            $loginForm = $securityFormService->loginService();

            $this->twig->display('frontend/Security/login.html.twig', [
                'errors' => $errors,
                'loginForm' => $loginForm->create(),
            ]);
        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }

    /**
     * Déconnecte l'utilisateur en supprimant sa session.
     *
     * @return void
     */
    public function logout()
    {
        // Démarrez la session si ce n'est pas déjà fait
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Utilisez isset() pour vérifier si la clé est définie dans $_SESSION
        if (isset($_SESSION['user'])) {
            // Supprimez la clé 'user' de la session
            unset($_SESSION['user']);
        }

        // Terminez la session
        session_destroy();

        // Redirigez vers la page d'accueil
        header('Location: home');
        exit;
    }
}
