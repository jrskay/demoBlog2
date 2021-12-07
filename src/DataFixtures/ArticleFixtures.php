<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR'); // on veut des données en Francais

        for ($i = 1; $i <= 3; $i++) {
            // ici nous allons crée 3 categories fakées grace a faker
            $category = new Category;
            $category->setTitle($faker->sentence(3))
                ->setDescription($faker->paragraph());

            $manager->persist($category);

            for ($j = 1; $j <= mt_rand(4, 6); $j++) {
                // ici nous allons crée entre 4 et 6 articles fakées
                $article = new Article;
                $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

                $manager->persist($article);

                for ($k = 1; $k <= mt_rand(4, 10); $k++) {
                    // ici nous allons crée entre 4 et 10 commentaires par article
                    $comment = new Comment;
                    $content = '<p>' . join('</p><p>', $faker->paragraphs(2)) . '</p>';

                    // $now = new \DateTime();
                    // $interval = $now->diff($article->getCreatedAt());
                    // $days = $interval->days;
                    // $minimum = '-' . $days . 'days';// -49 days

                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
