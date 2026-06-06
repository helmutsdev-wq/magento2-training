<?php
namespace Training\Hello\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Training\Hello\Service\GreetingService;

class Products extends Command
{
    private ProductRepositoryInterface $productRepository;
    private SearchCriteriaBuilder $searchCriteria;
    private GreetingService $greetingService;

    public function __construct(
        ProductRepositoryInterface $productRepository, SearchCriteriaBuilder $searchCriteria, 
        GreetingService $greetingService
        )
    {
        parent::__construct();
        $this->productRepository = $productRepository;
        $this->searchCriteria = $searchCriteria;
        $this->greetingService = $greetingService;
    }

    protected function configure()
    {
        $this->setName('training:products')
            ->setDescription('Lists first 5 products');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->greetingService->getRandomQuote());
        $criteria = $this->searchCriteria->setPageSize(5)->create();
        $products = $this->productRepository->getList($criteria);

        foreach ($products->getItems() as $product) {
            $output->writeln($product->getSku() . ' - ' . $product->getName());
        }

        return Command::SUCCESS;
    }
}