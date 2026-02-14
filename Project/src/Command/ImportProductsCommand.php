<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:import-products',
    description: 'Import products from a CSV file',
)]
class ImportProductsCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $io->error(sprintf('File "%s" does not exist.', $filePath));
            return Command::FAILURE;
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ','); // Try comma first
            if (!$header || count($header) < 3) {
                 rewind($handle);
                 $header = fgetcsv($handle, 1000, ';'); // Try semicolon
            }
            
            // Basic check for headers
            // Assuming order: name, description, price
            
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                 if(count($data) < 3) {
                     // check semicolon
                     $line = implode(',', $data);
                     $data = str_getcsv($line, ';');
                 }

                 if (count($data) >= 3) {
                     $product = new Product();
                     $product->setName($data[0]);
                     $product->setDescription($data[1]);
                     $product->setPrice((float) $data[2]);
                     
                     // Default type
                     $product->setType('physical');

                     $this->entityManager->persist($product);
                 }
            }
            fclose($handle);
            
            $this->entityManager->flush();
            $io->success('Products imported successfully.');
            return Command::SUCCESS;
        }

        $io->error('Unable to open file.');
        return Command::FAILURE;
    }
}
