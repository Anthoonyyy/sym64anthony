<?php

namespace App\DataFixtures;

use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# Pour hacher les mots de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
# Chargement de faker
use Faker\Factory as Faker;
# Chargement de slugify
use Cocur\Slugify\Slugify;
# on récupère l'entité User
use App\Entity\User;
# entité Article
use App\Entity\Article;
# entité section
use App\Entity\Section;

class AppFixtures extends Fixture
{
    # attribut contenant le hacher de mot de passe
    private UserPasswordHasherInterface $passwordHasher;

    # constructeur qui remplit les attributs
    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
    )
    {
        # hache le mot de passe
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
       #Gestion de User

        // Création de Faker
        $faker = Faker::create('fr_FR');
        // Création de slugify
        $slugify = new Slugify();

        // Instanciation d'un nouvel user
        $user = new User();
        $user->setUsername('admin');
        # création d'un mail au hasard
        $mail = $faker->email();
        $user->setEmail($mail);

        $user->setRoles(['ROLE_ADMIN','ROLE_REDAC','ROLE_MODERATOR']);
        # hachage du mot de passe
        $pwdHash = $this->passwordHasher->hashPassword($user, 'admin');
        # insertion du mdp haché
        $user->setPassword($pwdHash);
        $user->setActivate(true);
        $user->setUniqid(uniqid("user", true));
        # Création d'un fullname avec Faker
        $fullname = $faker->name();
        $user->setFullname($fullname);

        // création/ update d'un tableau contenant
        // les User qui peuvent écrire un article
        $users[] = $user;
        $manager->persist($user);

        ###
        # Instanciation de 5 Rédacteurs
        #
        for($i = 1; $i <= 5; $i++){
            $user = new User();
            $user->setUsername('redac'.$i);
            $mail = $faker->email();
            $user->setEmail($mail);
            $user->setRoles(['ROLE_REDAC']);
            $pwdHash = $this->passwordHasher->hashPassword($user, 'redac'.$i);
            $user->setPassword($pwdHash);
            $user->setActivate(true);
            $fullname = $faker->name();
            $user->setFullname($fullname);
            $user->setUniqid(uniqid("user", true));
            // création/ update d'un tableau contenant
            // les User qui peuvent écrire un article
            $users[] = $user;

            # Utilisation du $manager pour mettre le
            # User en mémoire
            $manager->persist($user);
        }

        for($i = 1; $i <= 24; $i++){
            $user = new User();
            $user->setUsername('user'.$i);
            $mail = $faker->email();
            $user->setEmail($mail);
            $user->setRoles(['ROLE_USER']);
            $pwdHash = $this->passwordHasher->hashPassword($user, 'user'.$i);
            $user->setPassword($pwdHash);
            # on va activer 3 user sur 4
            $randActive = mt_rand(0,3);
            $user->setActivate($randActive);
            $fullname = $faker->name();
            $user->setFullname($fullname);
            $user->setUniqid(uniqid("user", true));
            $users[] = $user;
            $manager->persist($user);
        }

        ###
        # GESTION d'Articles
        ###
        for($i = 1; $i <= 160; $i++){
            $article = new Article();
            // on prend un auteur au hasard
            $user = array_rand($users);
            $article->setUser($users[$user]);

            // Utilisation de faker pour le titre
            $title = $faker->realTextBetween(20,150);
            $article->setTitle($title);
            // slugify à partir du titre pour titleSlug
            $article->setTitleSlug($slugify->slugify($title));

            // texte entre 3 et 6 paragraphes
            $article->setText($faker->paragraphs(mt_rand(3,6), true));
            // on va remonter dans le passé entre 6 mois et mtn
            $article->setArticleDateCreate($faker->dateTimeBetween('-6 months', 'now'));

            // on va publier 3 articles sur 4 (1,2,3 => publié, 4 => non publié)
            $published = mt_rand(1, 4) < 4;
            $article->setPublished($published);

            if($published) {
                // Si l'article est publié, on définit une date de publication après la date de création
                $articleDateCreate = $article->getArticleDateCreate();
                $article->setArticleDatePosted($faker->dateTimeBetween($articleDateCreate, 'now'));
            }

            // On garde les articles
            $articles[] = $article;
            $manager->persist($article);
        }

        ###
        # GESTION de Section
        ###

        for($i = 1; $i <= 6; $i++){
            $section = new Section();
            $title = $faker->realTextBetween(8,15);
            $section->setSectionTitle($title);
            // slugify à partir du titre pour titleSlug
            $section->setSectionSlug($slugify->slugify($title));

            $section->setSectionDetail($faker->realTextBetween(100,400));
            $articlesRandom =  array_rand($articles, mt_rand(2, min(40, count($articles))));

            foreach ($articlesRandom as $article){

                $section->addArticle($articles[$article]);
            }



            $manager->persist($section);
        }

        $manager->flush();
    }
}
