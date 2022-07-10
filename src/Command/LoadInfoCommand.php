<?php

namespace App\Command;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Director;
use App\Entity\Film;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Error;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Columns {
    const Title = 'title';
    const ReleaseDate = 'date_published';
    const Genre = 'genre';
    const Duration = 'duration';
    const Producer = 'production_company';
    const Directors = 'director';
    const Actors = 'actors';
}


class LoadInfoCommand extends Command
{
    protected static $defaultName = 'app:load-info';
    protected static $defaultDescription = '';
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to load all data from csv')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to csv with data')
            ->setDescription('Loads info from csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $allRows = $this->getCsvAsArray($path);

        if (sizeof($allRows) > 0) {

            try {
                $filmRepository = $this->entityManager->getRepository(Film::class);
                $actorRepository = $this->entityManager->getRepository(Actor::class);
                $directorRepository = $this->entityManager->getRepository(Director::class);
                $genreRepository = $this->entityManager->getRepository(Category::class);

                foreach (array_chunk($allRows, 10, true) as $rows) {

                    foreach ($rows as $row) {
                        if(!$filmRepository->findOneBy(['Title' => $row[Columns::Title]])) {

                            $filmInfo = $row;
                            $filmInfo[Columns::ReleaseDate] = DateTime::createFromFormat('Y-m-d', $filmInfo[Columns::ReleaseDate]);
                            $filmInfo[Columns::Actors] = $this->getAndPersistRelated($actorRepository, $row[Columns::Actors], 'App\Entity\Actor');
                            $filmInfo[Columns::Directors] = $this->getAndPersistRelated($directorRepository, $row[Columns::Directors], 'App\Entity\Director');
                            $filmInfo[Columns::Genre] = $this->getAndPersistRelated($genreRepository, $row[Columns::Genre], 'App\Entity\Category');
                            $film = new Film($filmInfo);
                            $this->entityManager->persist($film);
                        }
                    }

                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }

                $output->writeln('--------------- SUCCESS --------------');
                return Command::SUCCESS;
            } catch (Error $e) {
                $output->writeln('--------------- SOMETHING WENT WRONG --------------');
                return Command::FAILURE;
            }
        }

        $output->writeln('--------------- INVALID DATA --------------');
        return Command::INVALID;
    }

    private function getCsvAsArray(string $path) : array {
        $result = [];

        if (file_exists($path)) {
            $correlation = [Columns::Title => 1, Columns::ReleaseDate => 4, Columns::Genre => 5, Columns::Duration => 6, Columns::Producer => 11, Columns::Directors => 9, Columns::Actors => 12];
            $csvStream = fopen($path, 'r');
            fgetcsv($csvStream); //discard first line from headers
            $row = fgetcsv($csvStream);

            while($row != null) {
                $actualRow = [];

                foreach ($correlation as $key => $value) {

                    $actualRow[$key] = $row[$value];
                }

                $date = DateTime::createFromFormat('Y-m-d', $actualRow[Columns::ReleaseDate]);
                if ($date !== false && !array_sum($date::getLastErrors())) {
                    $result[] = $actualRow;
                }

                $row = fgetcsv($csvStream);
            }
        }

        return $result;
    }

    private function getAndPersistRelated(\Doctrine\ORM\EntityRepository $repository, string $stringList, string $className) : array
    {
        $resultSet = [];

        foreach (array_map('ltrim', explode(',', $stringList)) as $name) {
            if ($existingRecord = $repository->findOneBy(['Name' => $name])) {
                $resultSet[] = $existingRecord;
            } else {
                $object = new $className($name);
                $this->entityManager->persist($object);
                $resultSet[] = $object;
            }
        }

        return $resultSet;
    }
}