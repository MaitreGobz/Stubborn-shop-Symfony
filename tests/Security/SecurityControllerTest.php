<?php

namespace App\Tests\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Tests fonctionnels liés à la page de connexion.
 */
class SecurityControllerTest extends WebTestCase
{
     public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Se connecter');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
    }

    public function testUserCanLoginWithValidCredentials(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $testEmail = 'login-test@example.com';
        $plainPassword = 'password123';

        // Cleanup / creation of a test user
        $userRepository = $em->getRepository(User::class);
        if ($existing = $userRepository->findOneBy(['email' => $testEmail])) {
            $em->remove($existing);
            $em->flush();
        }

        $user = new User();
        $user->setName('Login Test User');
        $user->setEmail($testEmail);
        $user->setDeliveryAddress('8 rue du Bac, 54100 Nancy');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $plainPassword)
        );
        // We consider an account that has already been verified for the test
        $user->setIsVerified(true); 

        $em->persist($user);
        $em->flush();

        // Show login page
        $crawler = $client->request('GET', '/login');

        // Submit the login form
        $client->submitForm('Se connecter', [
            '_username' => $testEmail,
            '_password' => $plainPassword,
        ]);

        // We expect to be redirected to the homepage
        $this->assertResponseRedirects('/');
        $client->followRedirect();

        // Verifies that the user is properly authenticated
        $this->assertSelectorExists('a[href="/logout"]', 
            'Le lien de déconnexion doit être visible après connexion.');
    }
}
