<?php

namespace Corals\Modules\Directory\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\Directory\Facades\Directory;
use Corals\Modules\Directory\Models\Listing;
use Corals\Modules\Directory\Transformers\ListingTransformer;
use Yajra\DataTables\EloquentDataTable;

class ListingsDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('directory.models.listing.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new ListingTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param Listing $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(Listing $model)
    {
        return $model->newQuery();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['visible' => false],
            'image' => ['width' => '50px', 'title' => trans('Directory::attributes.listing.image'), 'orderable' => false, 'searchable' => false],
            'name' => ['title' => trans('Directory::attributes.listing.name')],
            'location' => ['title' => trans('Directory::attributes.listing.location'), 'orderable' => false, 'searchable' => false],
            'categories' => ['title' => trans('Directory::attributes.listing.categories'), 'orderable' => false, 'searchable' => false],
            'tags' => ['title' => trans('Directory::attributes.listing.tags'), 'orderable' => false, 'searchable' => false, 'width' => '5%'],
            'status' => ['title' => trans('Corals::attributes.status')],
            'user_id' => ['title' => trans('Directory::attributes.listing.owner')],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at')],
        ];
    }

    public function getFilters()
    {
        return [
            'name' => ['title' => trans('Directory::attributes.listing.title_name'), 'class' => 'col-md-2', 'type' => 'text', 'condition' => 'like', 'active' => true],
            'location.id' => ['title' => trans('Directory::attributes.listing.location'), 'class' => 'col-md-2', 'type' => 'select2', 'options' => \Address::getLocationsList('Directory'), 'active' => true],
            'description' => ['title' => trans('Directory::attributes.listing.description'), 'class' => 'col-md-3', 'type' => 'text', 'condition' => 'like', 'active' => true],
            'status' => ['title' => trans('Directory::labels.listing.active_listings'), 'class' => 'col-md-2', 'checked_value' => 'active', 'type' => 'boolean', 'active' => true],
        ];
    }

    protected function getBulkActions()
    {
        return [
            'delete' => ['title' => trans('Corals::labels.delete'), 'permission' => 'Directory::listing.delete', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'active' => ['title' => '<i class="fa fa-check-circle"></i> ' . trans('Directory::attributes.listing.status_options.active'), 'permission' => 'Directory::listing.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'inActive' => ['title' => '<i class="fa fa-check-circle-o"></i> ' . trans('Directory::attributes.listing.status_options.inactive'), 'permission' => 'Directory::listing.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'archived' => ['title' => '<i class="fa fa-check-circle-o"></i> ' . trans('Directory::attributes.listing.status_options.archived'), 'permission' => 'Directory::listing.update', 'confirmation' => trans('Corals::labels.confirmation.title')],
            'pending' => ['title' => '<i class="fa fa-check-circle-o"></i> ' . trans('Directory::attributes.listing.status_options.pending'), 'permission' => 'Directory::listing.update', 'confirmation' => trans('Corals::labels.confirmation.title')]
        ];
    }

    protected function getOptions()
    {
        $url = url(config('directory.models.listing.resource_url'));
        return ['resource_url' => $url];
    }
}
