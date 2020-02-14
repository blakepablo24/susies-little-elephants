<?php

namespace App\DataFixtures;

use App\DataFixtures\ChildFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Child;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Service\UploaderHelper;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
        public function load(ObjectManager $manager)
            {
                foreach ($this->getPostData() as [$subject, $content, $date, $imageSize, $imageName, $child_id])
                {
                    $child_id = $manager->getRepository(Child::class)->find($child_id);
                    $post = new Post();
                    $post->setChild($child_id);
                    $post->setSubject($subject);
                    $post->setImageName($imageName);
                    $post->setImageSize($imageSize);

                    $image = new Image();
                    $imageFile = new UploadedFile('public/img/1.jpg');
                    $image->$imageFile = $imageFile;

                    $post->setImageFile($imageFile);
                    $post->setContent($content);
                    $post->setDate($date);
                    $manager->persist($post);
                }

                $manager->flush();
            }

            private function getPostData(): array
            {
                return [
                    ['Wet Play', 
                        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                        \DateTime::createFromFormat('Y-m-d', "2018-09-09"),
                         1024,
                         'bananaman.jpeg',
                        1],
                    ['Dry Play',
                        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                        \DateTime::createFromFormat('Y-m-d', "2018-09-09"),
                        1024,
                        'bananaman.jpeg',
                        1],
                    ['Book Day',
                        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                        \DateTime::createFromFormat('Y-m-d', "2018-09-09"),                        
                        1024,
                        'bananaman.jpeg',
                        2],
                    ['Play day',
                        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                        \DateTime::createFromFormat('Y-m-d', "2018-09-09"),                       
                        1024,
                        'bananaman.jpg',
                        3],
                    ['Aquarium',
                        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                        \DateTime::createFromFormat('Y-m-d', "2018-09-09"),                        
                        1024,
                        'bananaman.jpg', 
                        4],
                ];
            }

            public function getDependencies()
        {
            return array(
                ChildFixtures::class
            );
        }
}
