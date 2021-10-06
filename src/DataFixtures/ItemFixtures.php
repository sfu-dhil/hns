<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Item;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ItemFixtures extends Fixture implements DependentFixtureInterface {
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 1; $i <= 4; $i++) {
            $fixture = new Item();
            $fixture->setPublic(0 === $i % 2);
            $fixture->setOriginalName('OriginalName ' . $i);
            $fixture->setPath('Path ' . $i);
            $fixture->setMimeType('MimeType ' . $i);
            $fixture->setFileSize($i);
            $fixture->setThumbPath('ThumbPath ' . $i);
            $fixture->setDescription("<p>This is paragraph {$i}</p>");
            $fixture->setLicense("<p>This is paragraph {$i}</p>");
            $fixture->setScrapbook($this->getReference('scrapbook.' . $i));
            $em->persist($fixture);
            $this->setReference('item.' . $i, $fixture);
        }
        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies() {
        return [
            ScrapbookFixtures::class,
        ];
    }
}
