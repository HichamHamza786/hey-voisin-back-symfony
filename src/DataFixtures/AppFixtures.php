<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker; 
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\DataFixtures\Provider\CategoriesProvider;
use App\Entity\Avatar;
use App\Entity\Comment;
use App\Entity\Locality;
use DateTimeImmutable;

class AppFixtures extends Fixture
{   
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher; 
    }

    /**
     * Fonction qui va s'exécuter quand on va charger les fixtures (envoyer les données en BDD)
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
    
        // Instanciation de Faker avec localisation en français
        $faker = \Faker\Factory::create('fr_FR');

        // Instanciation de CategoriesProvider
        $categoriesProvider = new CategoriesProvider();

        // Création de 5 avatars
        $avatarList = [];

        for ($a=1; $a <= 5; $a++) { 
            
            $avatar = new Avatar;
            
            $avatar->setUrl($faker->imageUrl());
            $avatar->setName($faker->name());
            $avatarList[] = $avatar;
            $manager->persist($avatar);
        }


        // Création de 30 villes
        $localityList = [];

        for ($l=1; $l <= 30; $l++) { 
            
            $locality = new Locality;
            $locality->setAdress($faker->streetAddress());
            //
            $postcode = str_replace(' ', '', $faker->postcode());
            $locality->setZipcode($postcode);
            $locality->setCity($faker->city());
            $createdAt = new DateTimeImmutable($faker->dateTimeBetween('-6 months')->format('Y-m-d H:i:s'));
            $locality->setCreatedAt($createdAt);
            $localityList[] = $locality;
            
            $manager->persist($locality);
        }


        // Création d'un administrateur
        $admin = new User;
        
        $admin->setEmail("admin@gmail.com");
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, "admin"));
        $admin->setAlias($faker->name());
        $admin->setFirstname($faker->firstName());
        $admin->setLastname($faker->lastName());
        $admin->setAvatar($avatarList[array_rand($avatarList)]);
        $admin->setLocality($localityList[array_rand($localityList)]);

        $manager->persist($admin);


        // Création d'un modérateur
        $moderator = new User;
        
        $moderator->setEmail("moderator@gmail.com");
        $moderator->setRoles(["ROLE_MODERATOR"]);
        $moderator->setPassword($this->passwordHasher->hashPassword($moderator, "moderator"));
        $moderator->setAlias($faker->name());
        $moderator->setFirstname($faker->firstName());
        $moderator->setLastname($faker->lastName());
        $moderator->setAvatar($avatarList[array_rand($avatarList)]);
        $moderator->setLocality($localityList[array_rand($localityList)]);

        $manager->persist($moderator);


        // Création de 20 utilisateurs
        $userList = [];

        for ($u=1; $u <= 20; $u++) { 
            
            $user = new User;
            //! ATTENTION LE CHAMP EMAIL REQUIERT UNE REVIEW 
            $user->setEmail($faker->email());
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword($this->passwordHasher->hashPassword($user, "user"));
            $user->setAlias($faker->name());
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setAvatar($avatarList[array_rand($avatarList)]);
            $user->setLocality($localityList[array_rand($localityList)]); 
            $userList[] = $user;

            $manager->persist($user); 
        }


        // Création de 5 catégories
        $categoryList = [];

        for ($c=1; $c <= 5; $c++) { 
            
            $category = new Category;
            
            $category->setName($categoriesProvider->postCategories());
            $categoryList[] = $category;
            
            $manager->persist($category);
        }
        
        
        // Création de 15 posts
        $postList = []; 
        
        for ($p=1; $p <= 15; $p++) { 
            
            $post = new Post;
            
            $post->setTitle($faker->sentence(4));
            $post->setResume($faker->paragraph());
            $publishedAt = new DateTimeImmutable($faker->dateTimeBetween('-6 months')->format('Y-m-d H:i:s'));
            $post->setPublishedAt($publishedAt);
            $post->setIsActive($faker->boolean());
            $post->setIsOffer($faker->boolean());
            $post->setCategory($categoryList[array_rand($categoryList)]);
            $post->setLocality($localityList[array_rand($localityList)]);
            $post->setUser($userList[array_rand($userList)]);
            $postList[] = $post;

            $manager->persist($post);
        }

        //! Ce code sera utilisé pour la V1 
       /* // Création de 30 commentaires
        $commentList = [];

        for ($c=1; $c <= 30; $c++) { 
            
            $comment = new Comment;
            
            $comment->setContent($faker->paragraph());
            $comment->setPublishedAt(new DateTimeImmutable($faker->dateTimeBetween('-6 months')->format('Y-m-d H:i:s')));
            $comment->setPost($postList[array_rand($postList)]);
            $comment->setUser($userList[array_rand($userList)]);
            $commentList[] = $comment;
            
            $manager->persist($comment);
        }*/

        $manager->flush();
    }
}
