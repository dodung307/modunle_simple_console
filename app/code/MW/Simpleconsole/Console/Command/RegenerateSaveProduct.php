<?php
/**
 * Regenerate Url rewrites
 *
 * @package OlegKoval_RegenerateUrlRewrites
 * @author Oleg Koval <contact@olegkoval.com>
 * @copyright 2017 Oleg Koval
 * @license OSL-3.0, AFL-3.0
 */

namespace MW\Simpleconsole\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RegenerateSaveProduct extends Command
{
    const INPUT_NUMBER_PRODUCT = 'numberpr';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $_objectManager;


    /**
     * Constructor of RegenerateUrlRewrites
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\App\State $appState
    ) {
        $this->_resource = $resource;
        $this->_appState = $appState;
        $this->_objectManager = $objectmanager;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('ok:saveproduct:regenerate')
            ->setDescription('create new product')
            ->setDefinition([
                new InputArgument(
                    self::INPUT_NUMBER_PRODUCT,
                    InputArgument::OPTIONAL,
                    '5'
                )
            ]);
    }

    /**
     * Regenerate Url Rewrites
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // get store Id (if was set)
        $numberProduct = $input->getArgument(self::INPUT_NUMBER_PRODUCT);
        if (is_null($numberProduct)) {
            $numberProduct = $input->getOption(self::INPUT_NUMBER_PRODUCT);
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
        $product = $objectManager->create('\Magento\Catalog\Model\Product');
        $product->setSku('my-sku'); // Set your sku here
        $product->setName('Sample Simple Product'); // Name of Product
        $product->setAttributeSetId(4); // Attribute set id
        $product->setStatus(1); // Status on product enabled/ disabled 1/0
        $product->setWeight(10); // weight of product
        $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
        $product->setTaxClassId(0); // Tax class id
        $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
        $product->setPrice(100); // price of product
        $product->setStockData(
            array(
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 999999999
            )
        );
        $product->save();

        $output->writeln('');
        $output->writeln('');
        $output->writeln('Reindexation...');

        $output->writeln('Cache refreshing...');
        shell_exec('php bin/magento cache:clean');
        shell_exec('php bin/magento cache:flush');
        $output->writeln('Finished');
    }



    /**
     * Display error message
     * @param  OutputInterface  $output
     * @param  string  $errorMsg
     * @param  boolean $displayHint
     * @return void
     */
    private function displayError(&$output, $errorMsg, $displayHint = false)
    {
        $output->writeln('');
        $output->writeln($errorMsg);

        if ($displayHint) {
            $output->writeln('Correct command is: bin/magento ok:saveproduct:regenerate 19');
        }

        $output->writeln('Finished');
    }
}
