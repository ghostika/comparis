<?php
/**
 * Created by PhpStorm.
 * User: andrasratz
 * Date: 24/06/15
 * Time: 14:23
 */

namespace AppBundle\Service;


use AppBundle\Entity\Car;
use AppBundle\Entity\PriceChange;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class ComparisService
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /** @var \Doctrine\ORM\EntityManager */
    private $entityManager;

    private $url;

    public function __construct(OutputInterface $output, EntityManager $entityManager)
    {
        $this->output = $output;
        $this->client = new Client();
        $this->entityManager = $entityManager;
        $this->url = 'https://www.comparis.ch/carfinder/marktplatz?requestobject=%7B%22Make%22%3A%22100000013%22%2C%22Model%22%3Anull%2C%22VehicleType%22%3A%222%2C3%2C4%2C6%22%2C%22Construction%22%3Anull%2C%22FirstMatriculationYearFrom%22%3A%222015%22%2C%22FirstMatriculationYearTo%22%3Anull%2C%22PriceFrom%22%3Anull%2C%22PriceTo%22%3A%2260000%22%2C%22MileageFrom%22%3Anull%2C%22MileageTo%22%3A%221000%22%2C%22MaxAdAge%22%3Anull%2C%22MinComparisPoints%22%3Anull%2C%22OutsideColor%22%3A%22%22%2C%22Transmission%22%3Anull%2C%22FuelType%22%3Anull%2C%22PerformanceFrom%22%3Anull%2C%22PerformanceTo%22%3Anull%2C%22ConsumptionFrom%22%3Anull%2C%22ConsumptionTo%22%3Anull%2C%22SeatsFrom%22%3Anull%2C%22SeatsTo%22%3Anull%2C%22DoorsFrom%22%3Anull%2C%22DoorsTo%22%3Anull%2C%22Cantons%22%3A%22%22%2C%22ComfortOptions%22%3A%22%22%2C%22SecurityOptions%22%3A%22%22%2C%22FreeTextSearch%22%3A%22%22%2C%22CapacityFrom%22%3Anull%2C%22CapacityTo%22%3Anull%2C%22Garage%22%3Anull%2C%22ModelGroup%22%3A%223380%22%2C%22Site%22%3Anull%2C%22EfficiencyCategory%22%3Anull%2C%22Sort%22%3A3%2C%22TypeTag%22%3Anull%7D';
    }

    public function start()
    {
        $result = $this->client->get($this->url);

        $crawler = new Crawler(null, 'https://www.comparis.ch');
        $crawler->addContent($result->getBody());

        $numberOfPages = (int) $crawler->filter('#divTopPaging .paging-link')->last()->text();
        $this->output->writeln('Number of pages: ' . $numberOfPages);
        $links = [];

        $links = array_merge($links, $this->gatherLinks($crawler));

        for ($i = 1;$i < $numberOfPages; $i++) {
            $result = $this->client->get($this->url . '&page=' . $i);

            $crawler = new Crawler(null, 'https://www.comparis.ch');
            $crawler->addContent($result->getBody());

            $links = array_merge($links, $this->gatherLinks($crawler));
        }

        foreach ($links as $link) {
            $this->parseLink($link);
        }
    }

    private function gatherLinks(Crawler $crawler)
    {
        $links = $crawler->filter('.TextLinkMarked')->each(function (Crawler $node, $i) {
                return $node->link()->getUri();
            });

        return $links;
    }

    private function parseLink($link)
    {
        $partsTmp = explode('/', $link);
        $comparisId = end($partsTmp);

        /** @var Car $car */
        $car = $this->entityManager
            ->getRepository('AppBundle:Car')
            ->findOneBy(['comparisId' => $comparisId]);

        try {
            $result = $this->client->get($link, ['allow_redirects' => false]);
        } catch (ClientException $e) {
            if ($car) {
                $car->setDeleted(1);
                $this->entityManager->persist($car);
                $this->entityManager->flush();
            }
            return;
        }

        if ($result->getStatusCode() == '302') {
            if ($car) {
                $car->setDeleted(1);
                $this->entityManager->persist($car);
                $this->entityManager->flush();
            }
            return;
        }

        $crawler = new Crawler();
        $crawler->addContent($result->getBody());

        if (!$car) {
            $car = new Car();
            $car->setLink($link);
            $car->setComparisId($comparisId);

            $this->createCar($crawler, $car);
        } else {
            $currentPrice = (int) str_replace('CHF ', '', str_replace("'", '', $crawler->filter('#pricebox .subcr')->eq(0)->text()));

            // change 'last seen' time
            if ($currentPrice == $car->getPrice()) {
                $car->setModified();
                $this->entityManager->persist($car);
                $this->entityManager->flush();
            } else {
                //create a new priceChange

                $priceChange = new PriceChange();
                $priceChange->setCar($car);
                $priceChange->setOldPrice($car->getPrice());
                $priceChange->setNewPrice($currentPrice);

                $this->entityManager->persist($priceChange);
                $car->setPrice($currentPrice);

                $this->entityManager->persist($car);
                $this->entityManager->flush();
            }
        }

    }

    private function createCar(Crawler $crawler, Car $car)
    {
        $car->setName($crawler->filter('.finderbreadcrumb .last')->text());
        $priceText = $crawler->filter('#pricebox .subcr')->eq(0)->text();
        $priceText = str_replace('CHF ', '', str_replace("'", '', $priceText));
        $car->setPrice((int) $priceText);
        $firstSeen = substr(trim($crawler->filter('#resultTable .TextAnnotation')->text()), -10, 10);
        $firstDate = \DateTime::createFromFormat('d.m.Y', $firstSeen);
        if ($firstDate) {
            $car->setComparisCreated($firstDate);
        }

        $attributes = $crawler->filter('.RowLayout .CellLayout')->each(function(Crawler $node, $i) use ($crawler) {
                if (trim($node->text()) == 'Aufbau:') {
                    return ['setCarType', trim($crawler->filter('.RowLayout .CellLayout')->eq($i + 1)->text())];
                } elseif (trim($node->text()) == 'Kilometerstand:') {
                    return ['setKm', str_replace(' km', '', trim($crawler->filter('.RowLayout .CellLayout')->eq($i + 1)->text()))];
                } elseif (trim($node->text()) == 'Getriebe:') {
                    return ['setValto', trim($crawler->filter('.RowLayout .CellLayout')->eq($i + 1)->text())];
                } elseif (trim($node->text()) == 'Hubraum:') {
                    return ['setCapacity', trim($crawler->filter('.RowLayout .CellLayout')->eq($i + 1)->text())];
                } elseif (trim($node->text()) == 'Leistung:') {
                    return ['setPerformance', str_replace(' PS', '',trim($crawler->filter('.RowLayout .CellLayout')->eq($i + 1)->text()))];
                } elseif (trim($node->text()) == 'Treibstoff:') {
                    return ['setBenzin', trim($crawler->filter('.RowLayout .CellLayout')->eq($i + 1)->text())];
                } elseif (trim($node->text()) == 'CO2-Ausstoss:') {
                    return ['setCo2', str_replace(' g/km', '', trim($crawler->filter('.RowLayout .CellLayout')->eq($i + 1)->text()))];
                }
            });

        foreach ($attributes as $attr) {
            if (!is_array($attr)) {
                continue;
            }
            $car->{$attr[0]}($attr[1]);
        }

        $this->entityManager->persist($car);
        $this->entityManager->flush($car);

        return $car;
    }

}