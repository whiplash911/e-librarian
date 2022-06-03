<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(private UserRepository $userRepository, private UserPasswordHasherInterface $passwordHasher)
    {}

    public function create(string $data)
    {
        try {
            $user = new User();
            $data = json_decode($data);
            $plaintextPassword = $data->password;

            // hash the password (based on the security.yaml config for the $user class)
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            $user->setEmail($data->email);
            
            $this->userRepository->add($user);

            return $this->userRepository->findOneBy(['email' => $user->getEmail()]);
        } catch (\Throwable $th) {
            $this->logger->error(
                sprintf("Unable to register user: %s. Trace: %s", $th->getMessage(), $th->getTraceAsString()), 
                ['Registration']
            );

            throw $th;
        }
    }
}
