<?php
namespace Magenest\MultipleWishlist\Ui\Component\Listing\Columns;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Backend\Block\Widget\Grid as WidgetGrid;

class Qty extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $Director = $objectManager->create("Magenest\Movie\Model\Director");
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $ID=$item['director_id'];
                $Data = $Director->load($ID);
                $item['director_id'] = $Data->getName();
            }
        }*/
        return $dataSource;
    }
}