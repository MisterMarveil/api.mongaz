<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\PointOfSale;

class PointOfSaleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $points = [
            [
                'name' => 'Mokolo Market',
                'address' => 'Mokolo Market, Yaoundé, Cameroon',
                'lat' => '3.866667',
                'lon' => '11.516667'
            ],
            [
                'name' => 'Bastos Market',
                'address' => 'Bastos Market, Yaoundé, Cameroon',
                'lat' => '3.868420',
                'lon' => '11.518650'
            ],
            [
                'name' => 'Mfoundi Market',
                'address' => 'Mfoundi Market, Yaoundé, Cameroon',
                'lat' => '3.857500',
                'lon' => '11.518611'
            ],
            [
                'name' => 'Etoa Meki Market',
                'address' => 'Etoa Meki Market, Yaoundé, Cameroon',
                'lat' => '3.882222',
                'lon' => '11.494167'
            ],
            [
                'name' => 'Nkolbisson Market',
                'address' => 'Nkolbisson Market, Yaoundé, Cameroon',
                'lat' => '3.896389',
                'lon' => '11.450278'
            ]
        ];

        foreach ($points as $pointData) {
            $point = new PointOfSale();
            $point->setName($pointData['name'])
                  ->setAddress($pointData['address'])
                  ->setLat($pointData['lat'])
                  ->setLon($pointData['lon']);

            $manager->persist($point);
        }

        $manager->flush();
    }
}