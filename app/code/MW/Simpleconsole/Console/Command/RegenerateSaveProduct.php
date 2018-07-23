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

        for($i = 1; $i <= $numberProduct; $i++ ){
            try {
                $product = $this->_objectManager->get('Magento\Catalog\Model\Product');
                $product
                    ->setName('Random name ' . time())
                    ->setSku(time())
                    ->setPrice(0)
                    ->setStatus(2)// Disabled
                    ->setWebsiteIds([1, 2])
                    ->setVisibility(4)
                    ->setAttributeSetId($product->getDefaultAttributeSetId())
                    ->setTypeId('simple')
                    ->save();
            } catch (\Exception $e) {
                // debugging
                $output->writeln($e->getMessage());
            }
        }

        $output->writeln('');
        $output->writeln('');
        $output->writeln('Reindexation...');
        shell_exec('php bin/magento indexer:reindex');

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
