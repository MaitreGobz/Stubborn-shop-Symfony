<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels liés à l'inscription utilisateur.
 */
class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name=registration_form]');
        $this->assertSelectorTextContains('h1', 'S\'inscrire');
    }

    public function testUserCanRegister(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);

        $testEmail = 'test-registration@example.com';

        // Cleanup if the test has already been run
        if ($existingUser = $userRepository->findOneBy(['email' => $testEmail])) {
            $em->remove($existingUser);
            $em->flush();
        }

        $client->request('GET', '/register');

        $client->submitForm('Créer un compte', [
            'registration_form[name]'                    => 'TestUser',
            'registration_form[email]'                   => $testEmail,
            'registration_form[deliveryAddress]'         => '8 rue du Bac, 54100 Nancy',
            'registration_form[plainPassword][first]'    => 'password123',
            'registration_form[plainPassword][second]'   => 'password123',
        ]);

        // Redirection to the login page after registration
        $this->assertResponseRedirects('/login');
        $client->followRedirect();

        // Verify that the user has been created
        $user = $userRepository->findOneBy(['email' => $testEmail]);
        $this->assertNotNull($user, 'L\'utilisateur devrait être créé.');
        $this->assertFalse($user->isVerified(), 'Le compte ne doit pas encore être vérifié.');
    }
}
